<?php
include 'conexion.php';
session_start();

// --- Seguridad: solo Administradores ---
if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'No autorizado'
    ]);
    exit;
}

// --- Validar método ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Método no permitido'
    ]);
    exit;
}

// --- Recibir y limpiar datos ---
$id = intval($_POST['id'] ?? 0);
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$telefono = trim($_POST['telefono'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');

// --- Validación básica ---
if ($id <= 0 || empty($nombre) || empty($correo)) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Faltan datos obligatorios'
    ]);
    exit;
}

// --- Actualizar datos ---
$stmt = $conn->prepare("UPDATE clientes SET nombre=?, correo=?, telefono=?, direccion=? WHERE id=?");
$stmt->bind_param("ssssi", $nombre, $correo, $telefono, $direccion, $id);

if ($stmt->execute()) {
    echo json_encode([
        'status' => 'success',
        'message' => 'Cliente actualizado correctamente'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error al actualizar cliente: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
