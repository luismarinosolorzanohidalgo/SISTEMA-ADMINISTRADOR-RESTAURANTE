<?php
include "conexion.php";

// Obtener ID del restaurante
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener datos actuales del restaurante
$stmt = $conn->prepare("SELECT * FROM restaurantes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$restaurante = $result->fetch_assoc();

if (!$restaurante) {
    die("Restaurante no encontrado");
}

// Actualizar datos al enviar el formulario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = $_POST['nombre'];
    $ruc = $_POST['ruc'];
    $razon_social = $_POST['razon_social'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $correo = $_POST['correo'];
    $sede = $_POST['sede'];
    $logo_actual = $restaurante['logo'];

    // Procesar logo nuevo (si se sube)
    if (!empty($_FILES['logo']['name'])) {
        $nombreLogo = time() . "_" . basename($_FILES['logo']['name']);
        $rutaDestino = "uploads/" . $nombreLogo;

        if (move_uploaded_file($_FILES['logo']['tmp_name'], $rutaDestino)) {
            // Eliminar logo anterior si existe
            if (!empty($logo_actual) && file_exists("uploads/" . $logo_actual)) {
                unlink("uploads/" . $logo_actual);
            }
            $logo_actual = $nombreLogo;
        }
    }

    $sql = "UPDATE restaurantes SET nombre=?, ruc=?, razon_social=?, direccion=?, telefono=?, correo=?, sede=?, logo=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $nombre, $ruc, $razon_social, $direccion, $telefono, $correo, $sede, $logo_actual, $id);

    if ($stmt->execute()) {
        echo "<script>
                alert('✅ Restaurante actualizado correctamente');
                window.location='restaurantes.php';
              </script>";
    } else {
        echo "<script>alert('❌ Error al actualizar el restaurante');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Editar Restaurante</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body {
    background: linear-gradient(135deg, #f9f9f9, #e0e0e0);
    font-family: 'Poppins', sans-serif;
}
.container {
    max-width: 700px;
    background: white;
    margin-top: 40px;
    padding: 30px;
    border-radius: 20px;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}
img.logo-preview {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #d4af37;
}
.btn-guardar {
    background-color: #d4af37;
    color: white;
    font-weight: 600;
    border: none;
}
.btn-guardar:hover {
    background-color: #c19a2b;
}
</style>
</head>
<body>

<div class="container">
    <h3 class="text-center mb-4">✏️ Editar Restaurante</h3>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">Nombre del Restaurante</label>
            <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($restaurante['nombre']) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">RUC</label>
            <input type="text" name="ruc" class="form-control" value="<?= htmlspecialchars($restaurante['ruc']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Razón Social</label>
            <input type="text" name="razon_social" class="form-control" value="<?= htmlspecialchars($restaurante['razon_social']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" name="direccion" class="form-control" value="<?= htmlspecialchars($restaurante['direccion']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="<?= htmlspecialchars($restaurante['telefono']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control" value="<?= htmlspecialchars($restaurante['correo']) ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Sede</label>
            <input type="text" name="sede" class="form-control" value="<?= htmlspecialchars($restaurante['sede']) ?>">
        </div>

        <div class="mb-3 text-center">
            <label class="form-label">Logo Actual</label><br>
            <?php if (!empty($restaurante['logo'])): ?>
                <img src="uploads/<?= htmlspecialchars($restaurante['logo']) ?>" class="logo-preview mb-2">
            <?php else: ?>
                <p class="text-muted">Sin logo</p>
            <?php endif; ?>
            <input type="file" name="logo" class="form-control mt-2">
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-guardar px-5 py-2">Guardar Cambios</button>
        </div>
    </form>
</div>

</body>
</html>
