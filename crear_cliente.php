<?php
include 'conexion.php';
session_start();

header('Content-Type: application/json');

// Verifica sesión y rol
if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
    echo json_encode([
        'success' => false,
        'message' => 'Acceso no autorizado.'
    ]);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO clientes (nombre, correo, telefono, direccion, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nombre, $correo, $telefono, $direccion, $password);

    if ($stmt->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cliente agregado correctamente.'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al agregar el cliente. Verifica los datos.'
        ]);
    }

    $stmt->close();
}
$conn->close();
