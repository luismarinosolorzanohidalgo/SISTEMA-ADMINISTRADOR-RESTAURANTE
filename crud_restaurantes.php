<?php
include "conexion.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Administración de Restaurantes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
body {
    background: linear-gradient(135deg, #fff8e1, #f9f9f9);
    font-family: 'Poppins', sans-serif;
}
.container {
    margin-top: 40px;
}
.card {
    border: none;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.08);
}
.table {
    vertical-align: middle;
}
th {
    background-color: #d4af37 !important;
    color: white;
    text-align: center;
}
.logo {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #d4af37;
}
.btn-agregar {
    background-color: #d4af37;
    color: white;
    font-weight: 600;
    border: none;
}
.btn-agregar:hover {
    background-color: #c19a2b;
}
.modal-header {
    background-color: #d4af37;
    color: white;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
.btn-editar {
    color: #fff;
    background-color: #17a2b8;
    border: none;
    padding: 6px 12px;
    border-radius: 8px;
}
.btn-eliminar {
    color: #fff;
    background-color: #dc3545;
    border: none;
    padding: 6px 12px;
    border-radius: 8px;
}
</style>
</head>
<body>

<div class="container">
    <div class="card p-4">
        <h3 class="text-center mb-4">🍽️ Administración de Restaurantes</h3>

        <!-- Botón para abrir modal de agregar -->
        <div class="text-end mb-3">
            <button class="btn btn-agregar" data-bs-toggle="modal" data-bs-target="#modalAgregar">+ Agregar Restaurante</button>
        </div>

        <!-- Tabla -->
        <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead>
                <tr>
                    <th>Logo</th>
                    <th>Nombre</th>
                    <th>RUC</th>
                    <th>Razón Social</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Sede</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT * FROM restaurantes ORDER BY id DESC");
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td>
                        <?php if (!empty($row['logo'])): ?>
                            <img src="uploads/<?= htmlspecialchars($row['logo']) ?>" class="logo">
                        <?php else: ?>
                            <span class="text-muted">Sin logo</span>
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($row['nombre']) ?></td>
                    <td><?= htmlspecialchars($row['ruc']) ?></td>
                    <td><?= htmlspecialchars($row['razon_social']) ?></td>
                    <td><?= htmlspecialchars($row['direccion']) ?></td>
                    <td><?= htmlspecialchars($row['telefono']) ?></td>
                    <td><?= htmlspecialchars($row['correo']) ?></td>
                    <td><?= htmlspecialchars($row['sede']) ?></td>
                    <td>
                        <a href="editar_restaurante.php?id=<?= $row['id'] ?>" class="btn-editar">Editar</a>
                        <button class="btn-eliminar" onclick="eliminarRestaurante(<?= $row['id'] ?>)">Eliminar</button>
                    </td>
                </tr>
            <?php
                endwhile;
            else:
                echo "<tr><td colspan='9' class='text-muted'>No hay restaurantes registrados</td></tr>";
            endif;
            ?>
            </tbody>
        </table>
        </div>
    </div>
</div>

<!-- Modal Agregar Restaurante -->
<div class="modal fade" id="modalAgregar" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-lg">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Agregar Restaurante</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <form method="POST" enctype="multipart/form-data" action="guardar_restaurante.php">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">RUC</label>
                    <input type="text" name="ruc" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Razón Social</label>
                    <input type="text" name="razon_social" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Dirección</label>
                    <input type="text" name="direccion" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Teléfono</label>
                    <input type="text" name="telefono" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Correo</label>
                    <input type="email" name="correo" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Sede</label>
                    <input type="text" name="sede" class="form-control">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Logo</label>
                    <input type="file" name="logo" class="form-control">
                </div>
            </div>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-agregar px-4 py-2">Guardar</button>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function eliminarRestaurante(id) {
    Swal.fire({
        title: '¿Eliminar restaurante?',
        text: "Esta acción no se puede deshacer.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d4af37',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location = 'eliminar_restaurante.php?id=' + id;
        }
    });
}
</script>

</body>
</html>
