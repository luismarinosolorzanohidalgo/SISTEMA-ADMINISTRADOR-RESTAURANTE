<?php
include 'conexion.php';

// Compatibilidad conexión
$conn = $conn ?? $con ?? null;
if (!$conn) die("❌ Error: No hay conexión con la base de datos.");

// Obtener todos los trabajadores
$trabajadores = $conn->query("SELECT * FROM trabajadores ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>👨‍🍳 Panel de Trabajadores</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap, FontAwesome y SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    /* 🌄 Fondo general elegante */
    body {
      background: linear-gradient(135deg, #96a8a8ff, #96a8a8ff);
      font-family: "Poppins", sans-serif;
      color: #333;
      min-height: 100vh;
    }

    /* 📦 Contenedor principal */
    .container {
      max-width: 1200px;
      margin-top: 60px;
      background: #0ae0d6c9;
      border-radius: 20px;
      padding: 30px 40px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      backdrop-filter: blur(8px);
    }

    /* 🔱 Título */
    h2 {
      font-weight: 700;
      color: #2d3436;
      text-transform: uppercase;
      letter-spacing: 1px;
      text-align: center;
      margin-bottom: 30px;
      position: relative;
    }

    h2::after {
      content: "";
      display: block;
      width: 80px;
      height: 3px;
      background: linear-gradient(90deg, #d4af37, #f5d76e);
      margin: 10px auto 0;
      border-radius: 10px;
    }

    /* 📋 Tabla elegante */
    .table {
      border-radius: 15px;
      overflow: hidden;
      background: #0ae0d6c9;
    }

    .table thead th {
      background: linear-gradient(90deg, #d4af37, #f8e473);
      color: #fff;
      font-weight: 600;
      text-transform: uppercase;
      font-size: 14px;
      letter-spacing: 0.5px;
      text-align: center;
    }

    .table tbody tr {
      transition: all 0.3s ease;
    }

    .table tbody tr:hover {
      background-color: rgba(212, 175, 55, 0.15);
      transform: scale(1.01);
    }

    .table td {
      vertical-align: middle;
      text-align: center;
      font-size: 15px;
      color: #444;
    }

    /* 🎨 Botones */
    .btn {
      border-radius: 12px;
      font-weight: 600;
      letter-spacing: 0.4px;
      transition: all 0.3s ease;
    }

    .btn-primary {
      background: linear-gradient(90deg, #d4af37, #f5d76e);
      border: none;
      color: #222;
    }

    .btn-primary:hover {
      background: linear-gradient(90deg, #f5d76e, #d4af37);
      box-shadow: 0 0 10px rgba(212, 175, 55, 0.6);
    }

    .btn-danger {
      background: #e74c3c;
      border: none;
    }

    .btn-danger:hover {
      background: #c0392b;
      box-shadow: 0 0 10px rgba(231, 76, 60, 0.4);
    }

    .btn-success {
      background: #27ae60;
      border: none;
    }

    .btn-success:hover {
      background: #1e8449;
      box-shadow: 0 0 10px rgba(39, 174, 96, 0.4);
    }

    /* 💎 Modales */
    .modal-content {
      border-radius: 18px;
      border: 2px solid #f5d76e;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
      animation: fadeInUp 0.5s ease;
    }

    @keyframes fadeInUp {
      from {
        transform: translateY(40px);
        opacity: 0;
      }

      to {
        transform: translateY(0);
        opacity: 1;
      }
    }

    .modal-header {
      background: linear-gradient(90deg, #d4af37, #f5d76e);
      color: #222;
      border-top-left-radius: 15px;
      border-top-right-radius: 15px;
      text-align: center;
    }

    .form-label {
      font-weight: 600;
      color: #555;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #ccc;
      transition: all 0.3s ease;
    }

    .form-control:focus {
      border-color: #d4af37;
      box-shadow: 0 0 6px rgba(212, 175, 55, 0.4);
    }

    /* 🏷️ Selects personalizados */
    select.form-select {
      border-radius: 10px;
      border: 1px solid #ccc;
    }

    select.form-select:focus {
      border-color: #d4af37;
      box-shadow: 0 0 6px rgba(212, 175, 55, 0.4);
    }

    /* 🖼️ Logo */
    .logo-container {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .logo-container img {
      width: 120px;
      height: auto;
      filter: drop-shadow(0 2px 5px rgba(0, 0, 0, 0.2));
    }

    /* 📱 Responsividad */
    @media (max-width: 768px) {
      .container {
        padding: 20px;
      }

      h2 {
        font-size: 22px;
      }

      .table td {
        font-size: 14px;
      }
    }
  </style>

</head>

<body>
  <div class="container mt-5 p-4 bg-white rounded shadow">
    <!-- ✨ Encabezado con logo y animación -->
<div class="logo-container">
  <img src="logo.png" alt="Logo Empresa" class="animated-logo">
</div>

<h2 class="titulo-animado">Gestión de Trabajadores</h2>


    <div class="d-flex justify-content-between mb-3">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarTrabajador">
        <i class="fa-solid fa-user-plus"></i> Nuevo Trabajador
      </button>
      <a href="panel_trabajadores.php" class="btn btn-secondary">
        <i class="fa-solid fa-arrow-left"></i> Volver
      </a>
    </div>

    <div class="table-responsive">
      <table class="table table-striped text-center align-middle shadow-sm">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Telefono</th>
            <th>DNI</th>
            <th>Fecha Nac.</th>
            <th>Rol</th>
            <th>Sede</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($t = $trabajadores->fetch_assoc()): ?>
            <tr>
              <td><?= $t['id'] ?></td>
              <td><?= htmlspecialchars($t['nombre']) ?></td>
              <td><?= htmlspecialchars($t['correo']) ?></td>
              <td><?= htmlspecialchars($t['telefono']) ?></td>
              <td><?= htmlspecialchars($t['dni'] ?? '-') ?></td>
              <td><?= htmlspecialchars($t['fecha_nacimiento'] ?? '-') ?></td>
              <td><?= htmlspecialchars($t['rol']) ?></td>
              <td><?= htmlspecialchars($t['sede']) ?></td>
              <td>
                <button class="btn btn-sm btn-warning" onclick='editarTrabajador(<?= json_encode($t) ?>)'>
                  <i class="fa-solid fa-pen"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick='eliminarTrabajador(<?= $t["id"] ?>)'>
                  <i class="fa-solid fa-trash"></i>
                </button>
                <button class="btn btn-sm btn-info" onclick='abrirModalPassword(<?= $t["id"] ?>)'>
                  <i class="fa-solid fa-lock"></i>
                </button>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- 🟢 MODAL AGREGAR -->
  <div class="modal fade" id="modalAgregarTrabajador" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content p-3">
        <form id="formAgregarTrabajador">
          <h5 class="modal-title text-center mb-3">➕ Agregar Nuevo Trabajador</h5>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Teléfono</label>
              <input type="text" name="telefono" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Dirección</label>
              <input type="text" name="direccion" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">DNI</label>
              <input type="text" name="dni" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Nacimiento</label>
              <input type="date" name="fecha_nacimiento" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Rol</label>
              <select name="rol" class="form-select" required>
                <option value="Administrador">Administrador</option>
                <option value="Cocinero">Cocinero</option>
                <option value="Cajero">Cajero</option>
                <option value="Repartidor">Repartidor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Sede</label>
              <input type="text" name="sede" class="form-control" placeholder="Ejemplo: Miraflores" required>
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-4">💾 Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- 🟡 MODAL EDITAR -->
  <div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content p-3">
        <form id="formEditarTrabajador">
          <h5 class="modal-title text-center mb-3">✏️ Editar Trabajador</h5>
          <input type="hidden" name="id" id="edit_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" id="edit_nombre" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Correo</label>
              <input type="email" name="correo" id="edit_correo" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Teléfono</label>
              <input type="text" name="telefono" id="edit_telefono" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">Dirección</label>
              <input type="text" name="direccion" id="edit_direccion" class="form-control">
            </div>
            <div class="col-md-6">
              <label class="form-label">DNI</label>
              <input type="text" name="dni" id="edit_dni" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Fecha de Nacimiento</label>
              <input type="date" name="fecha_nacimiento" id="edit_fecha" class="form-control" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Rol</label>
              <select name="rol" id="edit_rol" class="form-select" required>
                <option value="Administrador">Administrador</option>
                <option value="Cocinero">Cocinero</option>
                <option value="Cajero">Cajero</option>
                <option value="Repartidor">Repartidor</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">Sede</label>
              <input type="text" name="sede" id="edit_sede" class="form-control" required>
            </div>
          </div>

          <div class="text-center mt-4">
            <button type="submit" class="btn btn-success px-4">💾 Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- 🔐 MODAL PASSWORD -->
  <!-- 🔐 MODAL CAMBIAR CONTRASEÑA -->
  <div class="modal fade" id="modalPassword" tabindex="-1" aria-labelledby="modalPasswordLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content shadow-lg border-0">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title fw-bold" id="modalPasswordLabel">
            <i class="fa-solid fa-lock me-2"></i> Cambiar Contraseña
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>

        <form id="formPassword" class="p-3">
          <input type="hidden" name="id" id="passId">

          <div class="mb-3">
            <label for="nueva_password" class="form-label fw-semibold">Nueva Contraseña</label>
            <input type="password" class="form-control" name="nueva_password" id="nueva_password" required>
          </div>

          <div class="mb-3">
            <label for="confirmar_password" class="form-label fw-semibold">Confirmar Contraseña</label>
            <input type="password" class="form-control" id="confirmar_password" required>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-success">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- JS -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    /* 🟢 Crear Trabajador */
    $('#formAgregarTrabajador').on('submit', function(e) {
      e.preventDefault();
      fetch('crear_trabajador.php', {
          method: 'POST',
          body: new FormData(this)
        })
        .then(r => r.json())
        .then(d => {
          if (d.success) Swal.fire({
            icon: 'success',
            title: '¡Trabajador agregado!',
            timer: 1500,
            showConfirmButton: false
          }).then(() => location.reload());
          else Swal.fire('Error', d.message, 'error');
        });
    });

    /* ✏️ Cargar datos al editar */
    function editarTrabajador(t) {
      $('#edit_id').val(t.id);
      $('#edit_nombre').val(t.nombre);
      $('#edit_correo').val(t.correo);
      $('#edit_telefono').val(t.telefono || '');
      $('#edit_direccion').val(t.direccion || '');
      $('#edit_dni').val(t.dni || '');
      $('#edit_fecha').val(t.fecha_nacimiento || '');
      $('#edit_rol').val(t.rol);
      $('#edit_sede').val(t.sede);
      new bootstrap.Modal('#modalEditar').show();
    }

    /* 🟡 Editar */
    $(document).on('submit', '#formEditarTrabajador', function(e) {
      e.preventDefault();
      fetch('editar_trabajador.php', {
          method: 'POST',
          body: new FormData(this)
        })
        .then(r => r.json())
        .then(d => {
          if (d.success) Swal.fire({
            icon: 'success',
            title: '¡Actualizado!',
            timer: 1500,
            showConfirmButton: false
          }).then(() => location.reload());
          else Swal.fire('Error', d.message, 'error');
        })
        .catch(() => Swal.fire('Error', 'Respuesta inválida del servidor', 'error'));
    });

    /* 🗑️ Eliminar */
    function eliminarTrabajador(id) {
      Swal.fire({
        title: '¿Eliminar trabajador?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonText: 'Cancelar',
        confirmButtonText: 'Sí, eliminar'
      }).then(res => {
        if (res.isConfirmed) {
          fetch('eliminar_trabajador.php?id=' + id)
            .then(r => r.json())
            .then(d => {
              if (d.success) Swal.fire('Eliminado', 'El trabajador fue eliminado', 'success').then(() => location.reload());
              else Swal.fire('Error', d.message, 'error');
            });
        }
      });
    }

    /* 🔐 Cambiar contraseña */
    function abrirModalPassword(id) {
      document.getElementById('passId').value = id;
      new bootstrap.Modal(document.getElementById('modalPassword')).show();
    }

    document.getElementById('formPassword').addEventListener('submit', function(e) {
      e.preventDefault();

      const nueva = document.getElementById('nueva_password').value;
      const confirmar = document.getElementById('confirmar_password').value;

      if (nueva !== confirmar) {
        Swal.fire({
          icon: 'warning',
          title: 'Las contraseñas no coinciden',
          confirmButtonColor: '#f39c12'
        });
        return;
      }

      fetch('cambiar_password.php', {
          method: 'POST',
          body: new FormData(this)
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Contraseña actualizada correctamente',
              showConfirmButton: false,
              timer: 1500
            });
            bootstrap.Modal.getInstance(document.getElementById('modalPassword')).hide();
            this.reset();
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: data.message || 'No se pudo cambiar la contraseña'
            });
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'Verifica tu conexión al servidor'
          });
        });
    });
  </script>

</body>

</html>