<?php
include 'conexion.php';
session_start();

// 🔒 Seguridad
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
  header("Location: login.php");
  exit();
}

// 🧱 Crear columnas necesarias si no existen
$cols = ['stock', 'existencia'];
foreach ($cols as $col) {
  $check = $conn->query("SHOW COLUMNS FROM `platos` LIKE '$col'");
  if ($check && $check->num_rows === 0) {
    $conn->query("ALTER TABLE `platos` ADD COLUMN `$col` INT NOT NULL DEFAULT 0");
  }
}

// 📝 Actualizar stock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_stock') {
  $id = intval($_POST['id']);
  $stock = intval($_POST['stock']);
  $stmt = $conn->prepare("UPDATE platos SET stock = ? WHERE id = ?");
  $stmt->bind_param("ii", $stock, $id);
  $stmt->execute();
  $_SESSION['msg_stock'] = "✅ Stock actualizado correctamente.";
  header("Location: stock.php");
  exit();
}

// 📊 Consultas principales
$productos = $conn->query("SELECT id, nombre, descripcion, precio, categoria, sede, estado, imagen, stock, existencia FROM platos ORDER BY nombre ASC");
$criticos = $conn->query("SELECT COUNT(*) AS c FROM platos WHERE stock <= 3")->fetch_assoc()['c'];
$totalProd = $conn->query("SELECT COUNT(*) AS t FROM platos")->fetch_assoc()['t'];
$totalStock = $conn->query("SELECT SUM(stock) AS s FROM platos")->fetch_assoc()['s'] ?? 0;

// 🔻 Top 5 menor stock
$lowRes = $conn->query("SELECT nombre, stock FROM platos ORDER BY stock ASC LIMIT 5");
$lowNombres = [];
$lowStocks = [];
while ($r = $lowRes->fetch_assoc()) {
  $lowNombres[] = $r['nombre'];
  $lowStocks[] = intval($r['stock']);
}

$msg = $_SESSION['msg_stock'] ?? null;
unset($_SESSION['msg_stock']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>📦 Control de Stock</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    :root { --oro:#c5a64b; --oro-claro:#e6cb7f; --bg:#fffdf7; }
    body { background: var(--bg); font-family:'Poppins',sans-serif; color:#333; }
    h1 { color: var(--oro); font-weight:700; }
    .btn-oro { background: linear-gradient(90deg,var(--oro),var(--oro-claro)); color:#fff; border:none; border-radius:25px; padding:8px 18px; font-weight:600; transition:.3s; }
    .btn-oro:hover { transform: scale(1.05); }
    .card { border-radius:15px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
    .table thead { background:#fff7d1; }
    .table { border:2px solid #f3e1a4; border-radius:15px; overflow:hidden; }
    .table th, .table td { border-color:#f3e1a4 !important; vertical-align:middle; }
    .badge { font-size:0.85rem; padding:6px 10px; border-radius:12px; }
    .stock-bajo { background:#ffb3b3; color:#a60000; }
    .stock-medio { background:#fff1b3; color:#a67c00; }
    .stock-alto { background:#b7ffb3; color:#008a1e; }
    .swal2-confirm { background:linear-gradient(90deg,var(--oro),var(--oro-claro))!important; border:none!important; }
    @media print {
      button, .btn, input, .card button { display:none !important; }
      .card { box-shadow:none !important; }
      body { background:#fff; }
    }
  </style>
</head>
<body class="container py-4">

  <!-- 🧭 Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1>📦 Control de Stock</h1>
    <div class="d-flex gap-2">
      <input type="search" id="search" placeholder="Buscar producto..." class="form-control rounded-pill px-3" style="width:250px;">
      <a href="principal.php" class="btn-oro">⬅️ Volver</a>
    </div>
  </div>

  <!-- ⚠️ Alerta de críticos -->
  <?php if ($criticos > 0): ?>
  <div class="alert alert-danger text-center fw-semibold shadow-sm">
    ⚠️ Hay <?= $criticos ?> producto<?= $criticos>1?'s':'' ?> con stock crítico
  </div>
  <?php endif; ?>

  <!-- 📋 Resumen -->
  <div class="row g-3 mb-4 text-center">
    <div class="col-md-4"><div class="card p-3 border-warning"><h5>Total productos</h5><h4><?= $totalProd ?></h4></div></div>
    <div class="col-md-4"><div class="card p-3 border-warning"><h5>Stock total</h5><h4><?= $totalStock ?></h4></div></div>
    <div class="col-md-4"><div class="card p-3 border-warning"><h5>Críticos</h5><h4 class="text-danger"><?= $criticos ?></h4></div></div>
  </div>

  <!-- 🧾 Tabla -->
  <div class="card p-3 mb-4">
    <div class="table-responsive">
      <table class="table table-bordered align-middle text-center">
        <thead class="fw-bold">
          <tr>
            <th>Imagen</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Existencia</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody id="productosTable">
          <?php while ($p = $productos->fetch_assoc()): ?>
            <?php
              $stock = (int)$p['stock'];
              $existencia = (int)$p['existencia'];
              if ($stock <= 3) $estado = '<span class="badge stock-bajo">Crítico</span>';
              elseif ($stock <= 10) $estado = '<span class="badge stock-medio">Bajo</span>';
              else $estado = '<span class="badge stock-alto">Disponible</span>';
              $img = $p['imagen'] ?: 'uploads/default.png';
            ?>
            <tr data-nombre="<?= strtolower($p['nombre']) ?>">
              <td><img src="<?= $img ?>" style="width:70px;height:70px;object-fit:cover;border-radius:10px"></td>
              <td class="text-start"><?= htmlspecialchars($p['nombre']) ?><div class="small text-muted"><?= $p['descripcion'] ?></div></td>
              <td><?= htmlspecialchars($p['categoria']) ?></td>
              <td>S/ <?= number_format($p['precio'],2) ?></td>
              <td><?= $stock ?></td>
              <td><?= $existencia ?></td>
              <td><?= $estado ?></td>
              <td>
                <button class="btn btn-sm btn-outline-warning btn-edit"
                        data-id="<?= $p['id'] ?>"
                        data-nombre="<?= htmlspecialchars($p['nombre']) ?>"
                        data-stock="<?= $stock ?>">✏️</button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>

    <div class="text-end mt-3">
      <button id="exportExcel" class="btn btn-sm btn-success me-2">📗 Exportar Excel</button>
      <button id="exportPDF" class="btn btn-sm btn-danger">📕 Exportar PDF</button>
    </div>
  </div>

  <!-- 📊 Gráfico -->
  <div class="card p-4 mb-4 border-warning">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="m-0">🩸 Top 5 — Menor Stock</h5>
      <button id="downloadChart" class="btn btn-sm btn-oro">📥 Descargar</button>
    </div>
    <canvas id="lowStockChart" height="120"></canvas>
  </div>

  <!-- ✏️ Modal -->
  <div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <form method="POST">
          <div class="modal-header bg-warning bg-gradient text-white">
            <h5 class="modal-title">Actualizar Stock</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="action" value="update_stock">
            <input type="hidden" name="id" id="modalId">
            <div class="mb-2 fw-semibold" id="modalNombre"></div>
            <div class="mb-3">
              <label class="form-label">Nuevo Stock</label>
              <input type="number" name="stock" id="modalStock" class="form-control" min="0" required>
            </div>
          </div>
          <div class="modal-footer border-0">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn-oro">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="text-center mt-3 text-muted small">© <?= date('Y') ?> PowerStreet • Control de Inventario</footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // 🔍 Buscar
    document.getElementById('search').addEventListener('input', e=>{
      const q = e.target.value.toLowerCase();
      document.querySelectorAll('#productosTable tr').forEach(r=>{
        r.style.display = r.dataset.nombre.includes(q)?'':'none';
      });
    });

    // ✏️ Modal
    const modal = new bootstrap.Modal(document.getElementById('stockModal'));
    document.querySelectorAll('.btn-edit').forEach(b=>{
      b.onclick=()=>{
        document.getElementById('modalId').value=b.dataset.id;
        document.getElementById('modalNombre').textContent=b.dataset.nombre;
        document.getElementById('modalStock').value=b.dataset.stock;
        modal.show();
      };
    });

    // ✅ SweetAlert
    <?php if($msg): ?>
      Swal.fire({ icon:'success', title:'Listo', text:'<?= $msg ?>', confirmButtonText:'OK' });
    <?php endif; ?>

    // 📊 Gráfico
    const ctx=document.getElementById('lowStockChart');
    const lowChart=new Chart(ctx,{
      type:'bar',
      data:{
        labels:<?= json_encode($lowNombres) ?>,
        datasets:[{data:<?= json_encode($lowStocks) ?>,backgroundColor:['#dc3545','#dc3545','#ffc107','#ffc107','#28a745']}]
      },
      options:{plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
    });

    document.getElementById('downloadChart').onclick=()=>{
      const a=document.createElement('a');
      a.href=lowChart.toBase64Image();
      a.download='Top5_Menor_Stock.png';
      a.click();
    };

    // 📗 Exportar Excel
    document.getElementById('exportExcel').onclick=()=>{
      let tabla=document.querySelector('table').outerHTML.replace(/ /g,'%20');
      const a=document.createElement('a');
      a.href='data:application/vnd.ms-excel,'+tabla;
      a.download='Reporte_Stock.xls';
      a.click();
    };

    // 📕 Exportar PDF limpio
    document.getElementById('exportPDF').onclick=()=>window.print();
  </script>
</body>
</html>
