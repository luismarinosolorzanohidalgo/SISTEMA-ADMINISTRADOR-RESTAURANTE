<?php
include 'conexion.php';
session_start();

if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nueva = trim($_POST['nueva_clave']);
    $hash = password_hash($nueva, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE clientes SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $hash, $id);

    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";

    if ($stmt->execute()) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Contraseña actualizada',
                showConfirmButton: false,
                timer: 1500
            }).then(() => window.location='clientes.php');
        </script>";
    } else {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error al actualizar',
                text: 'No se pudo restablecer la contraseña.'
            }).then(() => window.location='clientes.php');
        </script>";
    }

    $stmt->close();
}
$conn->close();
?>
