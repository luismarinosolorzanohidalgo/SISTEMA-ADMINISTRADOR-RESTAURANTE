<?php
// platos.php - UI avanzado + API AJAX (list, get, save, toggle, delete)
// Requisitos: conexion.php debe definir $conn (mysqli). Carpeta 'uploads/' (se crea si no existe).
session_start();
include 'conexion.php';

// test rápido (para debug)
// comprobar sesión admin
if (!isset($_SESSION['user_id'])) {
    // 🔹 Si no hay sesión, creamos una temporal (solo para pruebas)
    $_SESSION['user_id'] = 1;
    $_SESSION['rol'] = 'Administrador';
}


// comprobar sesión admin
// comprobar sesión admin
if (!isset($_SESSION['user_id']) || $_SESSION['rol'] !== 'Administrador') {
    // si es petición AJAX, devolver JSON
    if (isset($_REQUEST['action'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
        exit;
    }
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
    echo "<script>Swal.fire({icon:'error',title:'Acceso denegado',text:'Solo administradores',confirmButtonText:'Login'}).then(()=>location.href='login.php');</script>";
    exit;
}


// crear uploads si no existe
$uploadDir = __DIR__ . '/uploads/';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);

// Detectar nombre de columna de imagen existente
$imageCandidates = ['imagen','image','foto','img','image_url'];
$imageCol = null;
$resCols = $conn->query("SHOW COLUMNS FROM platos");
while ($col = $resCols->fetch_assoc()) {
    if (in_array($col['Field'], $imageCandidates)) {
        $imageCol = $col['Field'];
        break;
    }
}
if (!$imageCol) {
    // No existe columna de imagen: la usamos como 'imagen' (sin romper consultas) — pero no hay datos
    $imageCol = 'imagen';
}

// ------------------------------------
// API AJAX (si action presente)
// ------------------------------------
$action = $_REQUEST['action'] ?? null;
if ($action) {
    header('Content-Type: application/json; charset=utf-8');

    // sanitize helper
    function jescape($v) { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }

    // LIST
    if ($action === 'list') {
        $search = $conn->real_escape_string($_GET['search'] ?? '');
        $sql = "SELECT id, nombre, precio, descripcion, estado, COALESCE(`$imageCol`, '') AS imagen
                FROM platos
                WHERE nombre LIKE '%$search%' OR descripcion LIKE '%$search%'
                ORDER BY id DESC LIMIT 1000";
        $r = $conn->query($sql);
        $out = [];
        while ($row = $r->fetch_assoc()) {
            $row['imagen_url'] = $row['imagen'] ? ('uploads/' . $row['imagen']) : null;
            $out[] = $row;
        }
        echo json_encode(['success' => true, 'data' => $out]);
        exit;
    }

    // GET
    if ($action === 'get') {
        $id = intval($_GET['id'] ?? 0);
        $stmt = $conn->prepare("SELECT id, nombre, precio, descripcion, estado, COALESCE(`$imageCol`, '') AS imagen FROM platos WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if ($row) {
            $row['imagen_url'] = $row['imagen'] ? ('uploads/' . $row['imagen']) : null;
            echo json_encode(['success' => true, 'data' => $row]);
        } else echo json_encode(['success' => false, 'message' => 'Plato no encontrado']);
        exit;
    }

    // TOGGLE estado
    if ($action === 'toggle') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("SELECT estado FROM platos WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) { echo json_encode(['success' => false, 'message' => 'No encontrado']); exit; }
        $nuevo = ($row['estado'] === 'Disponible') ? 'Agotado' : 'Disponible';
        $u = $conn->prepare("UPDATE platos SET estado = ? WHERE id = ?");
        $u->bind_param('si', $nuevo, $id);
        $ok = $u->execute();
        echo json_encode(['success' => (bool)$ok, 'estado' => $nuevo]);
        exit;
    }

    // DELETE
    if ($action === 'delete') {
        $id = intval($_POST['id'] ?? 0);
        $stmt = $conn->prepare("SELECT COALESCE(`$imageCol`, '') AS imagen FROM platos WHERE id = ?");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        if ($r && !empty($r['imagen'])) @unlink($uploadDir . $r['imagen']);
        $d = $conn->prepare("DELETE FROM platos WHERE id = ?");
        $d->bind_param('i', $id);
        $ok = $d->execute();
        echo json_encode(['success' => (bool)$ok]);
        exit;
    }

    // SAVE (create/update)
    if ($action === 'save') {
        $id = intval($_POST['id'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $descripcion = trim($_POST['descripcion'] ?? '');
        $estado = in_array($_POST['estado'] ?? 'Disponible', ['Disponible','Agotado']) ? $_POST['estado'] : 'Disponible';

        if ($nombre === '' || $precio <= 0) {
            echo json_encode(['success' => false, 'message' => 'Nombre y precio válidos son obligatorios']);
            exit;
        }

        // imagen subida
        $imagen_name = null;
        if (!empty($_FILES['imagen']['name'])) {
            $ext = pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
            $imagen_name = time() . '_' . bin2hex(random_bytes(4)) . '.' . ($ext ?: 'jpg');
            $target = $uploadDir . $imagen_name;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $target)) {
                echo json_encode(['success' => false, 'message' => 'Error al subir imagen']);
                exit;
            }
            @chmod($target, 0644);
        }

        if ($id > 0) {
            if ($imagen_name) {
                $stmt = $conn->prepare("UPDATE platos SET nombre=?, precio=?, descripcion=?, estado=?, `$imageCol`=? WHERE id=?");
                $stmt->bind_param('sdsssi', $nombre, $precio, $descripcion, $estado, $imagen_name, $id);
            } else {
                $stmt = $conn->prepare("UPDATE platos SET nombre=?, precio=?, descripcion=?, estado=? WHERE id=?");
                $stmt->bind_param('sdssi', $nombre, $precio, $descripcion, $estado, $id);
            }
            $ok = $stmt->execute();
            echo json_encode(['success' => (bool)$ok, 'id' => $id]);
            exit;
        } else {
            if ($imagen_name) {
                $stmt = $conn->prepare("INSERT INTO platos (nombre, precio, descripcion, estado, `$imageCol`) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('sdsss', $nombre, $precio, $descripcion, $estado, $imagen_name);
            } else {
                $stmt = $conn->prepare("INSERT INTO platos (nombre, precio, descripcion, estado) VALUES (?, ?, ?, ?)");
                $stmt->bind_param('sdss', $nombre, $precio, $descripcion, $estado);
            }
            $ok = $stmt->execute();
            $newId = $conn->insert_id;
            echo json_encode(['success' => (bool)$ok, 'id' => $newId]);
            exit;
        }
    }

    echo json_encode(['success' => false, 'message' => 'Acción no soportada']);
    exit;
}

// ------------------------------------
// UI HTML (no action)
// ------------------------------------
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<title>Platos • FastFeast (Admin)</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

<style>
:root{
  --bg:#05060a; --card:#0f1720; --accent:#ffb300; --muted:#9aa4ad; --glass: rgba(255,255,255,0.03);
  --bold-ui: 700;
}
*{box-sizing:border-box}
body{background:linear-gradient(180deg,#07080a,#0b0d10); color:#e9f0f2; font-family:Inter, Poppins, sans-serif; padding-bottom:80px}
.topbar{background:linear-gradient(90deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); padding:14px 22px; border-bottom:1px solid rgba(255,255,255,0.03)}
.brand{font-weight:900; color:var(--accent); letter-spacing:0.6px}
.logo{width:42px;height:42px;object-fit:contain;border-radius:8px}
.searchbox{background:var(--glass); border-radius:12px; padding:10px}
.table-wrap{background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01)); border-radius:12px; padding:14px}
.table thead th{color:var(--accent); font-weight:800; font-size:0.86rem; border:none}
.table tbody td{vertical-align:middle;border-top:1px solid rgba(255,255,255,0.02)}
.img-thumb{width:84px;height:64px;object-fit:cover;border-radius:8px;background:#0b0d10}
.estado{padding:6px 12px;border-radius:999px;font-weight:800}
.Disponible{background:rgba(34,197,94,0.12);color:#22c55e}
.Agotado{background:rgba(239,68,68,0.12);color:#ef4444}
.btn-ghost{background:transparent;border:1px solid rgba(255,255,255,0.04)}
.muted{color:var(--muted)}
/* modal estilos potentes */
.modal-content{background:linear-gradient(180deg,#0e1114,#121417); border-radius:14px; color:#fff; border:none}
.modal-header{border-bottom:0;padding-bottom:6px}
.modal-title{font-weight:900;font-size:1.25rem}
.form-label{font-weight:800}
#modalPlato .form-control, #modalPlato .form-select{background:rgba(255,255,255,0.02); color:#fff; border:1px solid rgba(255,255,255,0.03)}
#previewWrap{height:150px;background:#060708;border-radius:10px}
.preview-img{max-height:100%; max-width:100%; border-radius:8px; display:block}
.icon-btn{width:40px;height:40px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center}
.float-actions{position:fixed; right:26px; bottom:26px; z-index:60}
.float-actions .btn{border-radius:12px; padding:12px 16px; font-weight:800}
.small-muted{font-size:0.86rem;color:var(--muted)}
/* responsive tweaks */
@media (max-width:768px){
  .img-thumb{width:64px;height:48px}
  .brand{font-size:14px}
}
</style>
</head>
<body>

<!-- TOP -->
<div class="topbar d-flex justify-content-between align-items-center">
  <div class="d-flex align-items-center gap-3">
    <?php if (file_exists(__DIR__.'/restaurante.png')): ?>
      <img src="restaurante.png" alt="logo" class="logo">
    <?php else: ?>
      <div style="width:42px;height:42px;border-radius:8px;background:linear-gradient(90deg,#ffb300,#ffca28);display:flex;align-items:center;justify-content:center;font-weight:900;color:#071018">FF</div>
    <?php endif; ?>
    <div>
      <div class="brand">FastFeast</div>
      <div class="small-muted">Panel — Gestión de Platos</div>
    </div>
  </div>

  <div class="d-flex align-items-center gap-2">
    <a href="index.php" class="btn btn-ghost btn-sm text-white"><i class="fa-solid fa-arrow-left"></i> Volver</a>
    <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="fa-solid fa-right-from-bracket"></i> Salir</a>
  </div>
</div>

<!-- CONTENIDO -->
<div class="container mt-4">
  <div class="row align-items-center mb-3">
    <div class="col-md-7">
      <div class="searchbox d-flex gap-2 align-items-center">
        <i class="fa-solid fa-magnifying-glass muted"></i>
        <input id="q" class="form-control bg-transparent border-0 text-white" placeholder="Buscar platos por nombre o descripción...">
        <button id="btnClear" class="btn btn-sm btn-outline-light">Limpiar</button>
      </div>
    </div>
    <div class="col-md-5 text-end">
      <div class="d-inline-flex gap-2">
        <button id="btnExport" class="btn btn-sm btn-outline-secondary">📥 Exportar CSV</button>
        <button id="btnNew" class="btn btn-warning btn-lg"><i class="fa-solid fa-plus"></i> Nuevo Plato</button>
      </div>
    </div>
  </div>

  <div class="table-wrap">
    <div class="table-responsive">
      <table class="table table-borderless text-white align-middle">
        <thead>
          <tr>
            <th style="width:70px">ID</th>
            <th style="width:110px">Imagen</th>
            <th>Nombre</th>
            <th style="width:120px">Precio</th>
            <th>Descripción</th>
            <th style="width:120px">Estado</th>
            <th style="width:160px" class="text-end">Acciones</th>
          </tr>
        </thead>
        <tbody id="tbodyPlatos">
          <tr><td colspan="7" class="text-center muted">Cargando...</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <footer class="mt-4 text-center small-muted">© <?= date('Y') ?> FastFeast</footer>
</div>

<!-- MODAL: Crear / Editar -->
<div class="modal fade" id="modalPlato" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content p-3">
      <div class="modal-header">
        <h5 id="modalTitle" class="modal-title">Nuevo Plato</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="formPlato" enctype="multipart/form-data" autocomplete="off">
        <input type="hidden" name="id" id="plato_id" value="0">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input name="nombre" id="nombre" class="form-control form-control-lg" required style="font-weight:800">
            </div>
            <div class="col-md-3">
              <label class="form-label">Precio (S/)</label>
              <input name="precio" id="precio" type="number" step="0.01" class="form-control form-control-lg" required style="font-weight:800">
            </div>
            <div class="col-md-3">
              <label class="form-label">Estado</label>
              <select name="estado" id="estado" class="form-select form-select-lg" style="font-weight:800">
                <option>Disponible</option>
                <option>Agotado</option>
              </select>
            </div>

            <div class="col-12">
              <label class="form-label">Descripción</label>
              <textarea id="descripcion" name="descripcion" class="form-control" rows="3" style="font-weight:600"></textarea>
            </div>

            <div class="col-md-6">
              <label class="form-label">Imagen (opcional)</label>
              <input id="imagen" name="imagen" type="file" accept="image/*" class="form-control">
              <div class="small-muted mt-1">Formato JPG/PNG. Se guardará en uploads/</div>
            </div>

            <div class="col-md-6">
              <label class="form-label">Preview</label>
              <div id="previewWrap" class="border rounded p-2 d-flex align-items-center justify-content-center">
                <img id="previewImg" src="" alt="Preview" class="preview-img" style="display:none">
                <div id="previewEmpty" class="muted small">No hay imagen seleccionada</div>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-warning" id="saveBtn" style="font-weight:900"><i class="fa-solid fa-floppy-disk"></i> Guardar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Lightbox modal para imagen grande -->
<div class="modal fade" id="lightbox" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content bg-transparent border-0">
      <div class="modal-body p-0 d-flex justify-content-center">
        <img id="lightboxImg" src="" style="max-width:90vw; max-height:80vh; border-radius:10px; box-shadow:0 8px 30px rgba(0,0,0,0.6)">
      </div>
    </div>
  </div>
</div>

<!-- Floating actions -->
<div class="float-actions">
  <button id="btnRefresh" class="btn btn-outline-light mb-2"><i class="fa-solid fa-arrows-rotate"></i> Refrescar</button><br>
  <button id="btnNewF" class="btn btn-warning"><i class="fa-solid fa-plus"></i> Nuevo</button>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
/* ===== UTIL ===== */
const apiUrl = (params={})=>{
  const u = new URL(window.location.href);
  u.searchParams.set('action', params.action || 'list');
  if (params.search !== undefined) u.searchParams.set('search', params.search);
  return u.toString();
};
const q = document.getElementById('q');
const tbody = document.getElementById('tbodyPlatos');
const modalEl = document.getElementById('modalPlato');
const modal = new bootstrap.Modal(modalEl);
const lightbox = new bootstrap.Modal(document.getElementById('lightbox'));
const form = document.getElementById('formPlato');
const previewImg = document.getElementById('previewImg');
const previewEmpty = document.getElementById('previewEmpty');
const saveBtn = document.getElementById('saveBtn');
const previewWrap = document.getElementById('previewWrap');

function escapeHTML(s){ return s ? s.replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])) : ''; }

/* ===== LOAD LIST (debounce) ===== */
let debounce = null;
q.addEventListener('input', ()=> {
  clearTimeout(debounce);
  debounce = setTimeout(()=> loadPlatos(q.value.trim()), 200);
});
document.getElementById('btnClear').addEventListener('click', ()=> { q.value=''; loadPlatos(''); });

document.getElementById('btnNew').addEventListener('click', ()=> openCreate());
document.getElementById('btnNewF').addEventListener('click', ()=> openCreate());
document.getElementById('btnRefresh').addEventListener('click', ()=> loadPlatos());
document.getElementById('btnExport').addEventListener('click', exportCSV);

async function loadPlatos(search='') {
  tbody.innerHTML = `<tr><td colspan="7" class="text-center muted">Cargando...</td></tr>`;
  try {
    const res = await fetch(apiUrl({action:'list', search}));
    const json = await res.json();
    if (!json.success) { tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error cargando</td></tr>`; return; }
    const data = json.data;
    if (data.length === 0) { tbody.innerHTML = `<tr><td colspan="7" class="text-center muted">No hay platos</td></tr>`; return; }
    tbody.innerHTML = '';
    data.forEach(p=>{
      const imgHtml = p.imagen_url ? `<img src="${p.imagen_url}" class="img-thumb" style="cursor:pointer" onclick="viewImg('${p.imagen_url}')">` : `<div class="img-thumb d-flex align-items-center justify-content-center muted"><i class="fa-solid fa-image"></i></div>`;
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td>#${p.id}</td>
        <td>${imgHtml}</td>
        <td style="font-weight:800">${escapeHTML(p.nombre)}</td>
        <td><strong>S/ ${Number(p.precio).toFixed(2)}</strong></td>
        <td>${escapeHTML(p.descripcion || '')}</td>
        <td><span class="estado ${p.estado}">${p.estado}</span></td>
        <td class="text-end">
          <button class="btn btn-sm btn-outline-info icon-btn me-1" title="Editar" onclick="openEdit(${p.id})"><i class="fa-solid fa-pen"></i></button>
          <button class="btn btn-sm btn-outline-danger icon-btn me-1" title="Eliminar" onclick="delPlato(${p.id})"><i class="fa-solid fa-trash"></i></button>
          <button class="btn btn-sm btn-outline-warning icon-btn" title="Agotar/Activar" onclick="toggle(${p.id})"><i class="fa-solid fa-ban"></i></button>
        </td>`;
      tbody.appendChild(tr);
    });
    gsap.from(tbody.querySelectorAll('tr'), {y:10, opacity:0, duration:0.5, stagger:0.03});
  } catch (e) {
    console.error(e);
    tbody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error de red</td></tr>`;
  }
}

/* ===== View image lightbox ===== */
function viewImg(url){
  document.getElementById('lightboxImg').src = url;
  lightbox.show();
}

/* ===== Create / Edit modal handling ===== */
function openCreate(){
  document.getElementById('modalTitle').innerText = 'Nuevo Plato';
  form.reset();
  document.getElementById('plato_id').value = 0;
  previewImg.style.display='none'; previewImg.src=''; previewEmpty.style.display='block';
  modal.show();
}

document.getElementById('imagen').addEventListener('change', e=>{
  const f = e.target.files[0];
  if (!f) { previewImg.style.display='none'; previewEmpty.style.display='block'; return; }
  previewImg.src = URL.createObjectURL(f);
  previewImg.style.display='block'; previewEmpty.style.display='none';
});

async function openEdit(id){
  try {
    const res = await fetch(apiUrl({action:'get'}) + '&id=' + id);
    const json = await res.json();
    if (!json.success) { Swal.fire('Error','No encontrado','error'); return; }
    const p = json.data;
    document.getElementById('modalTitle').innerText = 'Editar Plato';
    document.getElementById('plato_id').value = p.id;
    document.getElementById('nombre').value = p.nombre;
    document.getElementById('precio').value = p.precio;
    document.getElementById('descripcion').value = p.descripcion;
    document.getElementById('estado').value = p.estado || 'Disponible';
    if (p.imagen_url) { previewImg.src = p.imagen_url; previewImg.style.display='block'; previewEmpty.style.display='none'; }
    else { previewImg.style.display='none'; previewEmpty.style.display='block'; }
    modal.show();
  } catch (e) { console.error(e); Swal.fire('Error','Falla de red','error'); }
}

/* ===== Save (create/update) ===== */
form.addEventListener('submit', async (ev)=>{
  ev.preventDefault();
  const fd = new FormData(form);
  fd.append('action','save');
  saveBtn.disabled = true;
  saveBtn.innerHTML = `<i class="fa-solid fa-spinner fa-spin"></i> Guardando...`;
  try {
    const res = await fetch(window.location.pathname + '?action=save', { method:'POST', body: fd });
    const json = await res.json();
    if (json.success) {
      Swal.fire({icon:'success', title:'Guardado', timer:1000, showConfirmButton:false});
      modal.hide();
      loadPlatos(q.value.trim());
    } else {
      Swal.fire('Error', json.message || 'No guardado', 'error');
    }
  } catch (e) {
    console.error(e);
    Swal.fire('Error','Falla de red','error');
  } finally {
    saveBtn.disabled = false;
    saveBtn.innerHTML = `<i class="fa-solid fa-floppy-disk"></i> Guardar`;
  }
});

/* ===== Toggle estado ===== */
async function toggle(id) {
  const c = await Swal.fire({ title:'Cambiar estado', text:'Alternar Disponible/Agotado?', showCancelButton:true, confirmButtonText:'Sí, cambiar' });
  if (!c.isConfirmed) return;
  const fd = new FormData(); fd.append('id', id);
  const res = await fetch(window.location.pathname + '?action=toggle', { method:'POST', body: fd });
  const json = await res.json();
  if (json.success) { Swal.fire({icon:'success', title:'Estado: '+json.estado, timer:900, showConfirmButton:false}); loadPlatos(q.value.trim()); }
  else Swal.fire('Error','No se pudo cambiar','error');
}

/* ===== Delete ===== */
async function delPlato(id) {
  const c = await Swal.fire({ title:'Eliminar plato', text:'Esta acción es irreversible', icon:'warning', showCancelButton:true, confirmButtonText:'Sí, eliminar' });
  if (!c.isConfirmed) return;
  const fd = new FormData(); fd.append('id', id);
  const res = await fetch(window.location.pathname + '?action=delete', { method:'POST', body: fd });
  const json = await res.json();
  if (json.success) { Swal.fire({icon:'success', title:'Eliminado', timer:900, showConfirmButton:false}); loadPlatos(q.value.trim()); }
  else Swal.fire('Error','No se pudo eliminar','error');
}

/* ===== Export CSV (simple) ===== */
async function exportCSV(){
  const res = await fetch(apiUrl({action:'list', search: q.value.trim()}));
  const json = await res.json();
  if (!json.success) { Swal.fire('Error','No se pudo exportar','error'); return; }
  const rows = json.data;
  if (!rows.length) { Swal.fire('Info','No hay datos para exportar','info'); return; }
  let csv = 'id,nombre,precio,descripcion,estado,imagen\n';
  rows.forEach(r=>{
    const line = [r.id, `"${(r.nombre||'').replace(/"/g,'""')}"`, r.precio, `"${(r.descripcion||'').replace(/"/g,'""')}"`, r.estado, (r.imagen||'')].join(',');
    csv += line + '\n';
  });
  const blob = new Blob([csv], {type:'text/csv;charset=utf-8;'});
  const url = URL.createObjectURL(blob);
  const a = document.createElement('a'); a.href = url; a.download = 'platos_export.csv'; document.body.appendChild(a); a.click(); a.remove(); URL.revokeObjectURL(url);
}

/* ===== Inicializar ===== */
/* ===== Inicializar ===== */
loadPlatos();
gsap.from('.topbar', {y:-20, opacity:0, duration:0.7});
gsap.from('.table-wrap', {y:20, opacity:0, duration:0.9, delay:0.15});

</script>

</body>
</html>
