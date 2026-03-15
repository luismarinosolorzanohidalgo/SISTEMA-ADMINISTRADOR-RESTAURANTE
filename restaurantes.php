<?php
session_start();
include "conexion.php";

// --- AGREGAR ---
if (isset($_POST['agregar'])) {
  $nombre = $_POST['nombre'];
  $direccion = $_POST['direccion'];
  $correo = $_POST['correo'];
  $logo = "";

  if (!empty($_FILES['logo']['name'])) {
    $nombre_logo = time() . "_" . basename($_FILES['logo']['name']);
    $ruta = "uploads/" . $nombre_logo;
    move_uploaded_file($_FILES['logo']['tmp_name'], $ruta);
    $logo = $nombre_logo;
  }

  $sql = "INSERT INTO restaurantes (nombre, direccion, correo, logo) VALUES ('$nombre', '$direccion', '$correo', '$logo')";
  $conn->query($sql);
  header("Location: restaurantes.php?accion=agregado");
  exit;
}

// --- EDITAR ---
if (isset($_POST['editar'])) {
  $id = $_POST['id'];
  $nombre = $_POST['nombre'];
  $direccion = $_POST['direccion'];
  $correo = $_POST['correo'];

  if (!empty($_FILES['logo']['name'])) {
    $nombre_logo = time() . "_" . basename($_FILES['logo']['name']);
    $ruta = "uploads/" . $nombre_logo;
    move_uploaded_file($_FILES['logo']['tmp_name'], $ruta);
    $conn->query("UPDATE restaurantes SET logo='$nombre_logo' WHERE id=$id");
  }

  $conn->query("UPDATE restaurantes SET nombre='$nombre', direccion='$direccion', correo='$correo' WHERE id=$id");
  header("Location: restaurantes.php?accion=editado");
  exit;
}

// --- ELIMINAR ---
if (isset($_POST['eliminar'])) {
  $id = $_POST['id'];
  $conn->query("DELETE FROM restaurantes WHERE id=$id");
  header("Location: restaurantes.php?accion=eliminado");
  exit;
}

// --- CONSULTAR ---
$resultado = $conn->query("SELECT * FROM restaurantes ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Gestión de Restaurantes</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body {
      background: linear-gradient(180deg, #fffefb, #fff8e1);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      animation: fadeIn 0.7s ease;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(15px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* HEADER */
    .navbar {
      background: linear-gradient(90deg, #f1d67a, #eac45c, #f5d27b);
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
    }

    .navbar-brand img {
      width: 110px;
      height: 110px;
      border-radius: 50%;
      border: 3px solid #fff;
      box-shadow: 0 0 25px #d4af37;
    }

    .navbar-brand span {
      font-size: 2rem;
      color: #4a3f00;
      font-weight: 700;
      text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
    }

    /* BOTONES */
    .btn-add {
      background-color: #d4af37;
      color: white;
      font-weight: 600;
      border-radius: 30px;
      padding: 12px 25px;
      transition: 0.3s;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }

    .btn-add:hover {
      background-color: #b7932c;
      transform: scale(1.07);
    }

    /* TABLA */
    .table {
      background: #fff;
      border-radius: 15px;
      box-shadow: 0 6px 25px rgba(0, 0, 0, 0.08);
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .table-hover tbody tr:hover {
      background-color: #fff7e6;
      transform: scale(1.01);
      transition: all 0.2s ease-in-out;
    }

    .table td img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      object-fit: cover;
      border: 2px solid #d4af37;
      box-shadow: 0 0 10px rgba(212, 175, 55, 0.5);
    }

    /* ICONOS */
    .icon-btn {
      border: none;
      background: none;
      cursor: pointer;
      font-size: 22px;
      margin: 0 8px;
      transition: 0.3s;
    }

    .icon-btn.edit {
      color: #007bff;
    }

    .icon-btn.delete {
      color: #dc3545;
    }

    .icon-btn:hover {
      transform: scale(1.3);
    }

    /* MODAL */
    .modal-content {
      border-radius: 20px;
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }

    .btn-volver {
      background-color: #dbb923ff;
      color: #fff;
      border-radius: 30px;
      padding: 12px 25px;
      font-weight: 600;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      transition: all 0.3s ease;
    }

    .btn-volver:hover {
      background-color: #222;
      transform: scale(1.07);
    }


    /* FOOTER */
    footer {
      margin-top: 40px;
      text-align: center;
      color: #777;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>
  <!-- NAV -->
  <nav class="navbar navbar-light px-4 py-3">
    <a class="navbar-brand d-flex align-items-center" href="#">
      <img src="logo.png" alt="Logo">
      <span class="ms-3">Panel de Restaurantes</span>
    </a>
  </nav>

  <div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold text-dark">
        <i class="fa-solid fa-utensils me-2 text-warning"></i>Gestión de Restaurantes
      </h2>

      <div class="d-flex gap-2">
        <button class="btn-volver" onclick="window.location.href='principal.php'">
          <i class="fa-solid fa-arrow-left me-2"></i>Volver
        </button>

        <button class="btn-add" data-bs-toggle="modal" data-bs-target="#modalAgregar">
          <i class="fa-solid fa-plus me-2"></i>Nuevo Restaurante
        </button>
      </div>
    </div>


    <div class="table-responsive">
      <table class="table table-hover align-middle text-center">
        <thead class="table-warning">
          <tr>
            <th>Logo</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Correo</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $resultado->fetch_assoc()): ?>
            <tr>
              <td><img src="uploads/<?= $row['logo'] ?>" alt="Logo"></td>
              <td><?= $row['nombre'] ?></td>
              <td><?= $row['direccion'] ?></td>
              <td><?= $row['correo'] ?></td>
              <td>
                <button class="icon-btn edit" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $row['id'] ?>"><i class="fa-solid fa-pen"></i></button>
                <form method="POST" style="display:inline;">
                  <input type="hidden" name="id" value="<?= $row['id'] ?>">
                  <button type="submit" name="eliminar" class="icon-btn delete"><i class="fa-solid fa-trash"></i></button>
                </form>
              </td>
            </tr>

            <!-- MODAL EDITAR -->
            <div class="modal fade" id="modalEditar<?= $row['id'] ?>" tabindex="-1">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST" enctype="multipart/form-data">
                    <div class="modal-header bg-warning text-dark">
                      <h5 class="modal-title">Editar Restaurante</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <input type="hidden" name="id" value="<?= $row['id'] ?>">
                      <div class="mb-3">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="<?= $row['nombre'] ?>" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Dirección</label>
                        <input type="text" name="direccion" class="form-control" value="<?= $row['direccion'] ?>" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Correo</label>
                        <input type="email" name="correo" class="form-control" value="<?= $row['correo'] ?>" required>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" name="editar" class="btn btn-success">Guardar</button>
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- MODAL AGREGAR -->
  <div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form method="POST" enctype="multipart/form-data">
          <div class="modal-header bg-warning text-dark">
            <h5 class="modal-title">Nuevo Restaurante</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nombre</label>
              <input type="text" name="nombre" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Dirección</label>
              <input type="text" name="direccion" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Correo</label>
              <input type="email" name="correo" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Logo</label>
              <input type="file" name="logo" class="form-control">
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" name="agregar" class="btn btn-warning">Agregar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <footer class="mt-5">
    <p>© 2025 Sistema de Gestión de Restaurantes</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      // Confirmación elegante
      document.querySelectorAll("form button[name='eliminar']").forEach(btn => {
        btn.addEventListener("click", function(e) {
          e.preventDefault();
          const form = this.closest("form");
          const row = form.closest("tr");

          Swal.fire({
            title: "¿Estás seguro?",
            text: "Esta acción no se puede deshacer.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d4af37",
            cancelButtonColor: "#d33",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar"
          }).then((result) => {
            if (result.isConfirmed) {
              row.style.transition = "all 0.5s ease";
              row.style.opacity = "0";
              row.style.transform = "scale(0.95)";
              setTimeout(() => form.submit(), 500);
            }
          });
        });
      });

      // SweetAlert según acción
      const urlParams = new URLSearchParams(window.location.search);
      const accion = urlParams.get('accion');
      if (accion) {
        let mensaje = "";
        if (accion === "agregado") mensaje = "Restaurante agregado correctamente.";
        if (accion === "editado") mensaje = "Cambios guardados correctamente.";
        if (accion === "eliminado") mensaje = "Restaurante eliminado correctamente.";

        Swal.fire({
          icon: "success",
          title: "¡Éxito!",
          text: mensaje,
          confirmButtonColor: "#d4af37",
          timer: 2500,
          showConfirmButton: false
        });

        // Limpiar URL para evitar repetición
        window.history.replaceState({}, document.title, "restaurantes.php");
      }
    });
  </script>
</body>

</html>