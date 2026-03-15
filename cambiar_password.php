<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['id']) || empty($_POST['nueva_password'])) {
  echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
  exit;
}

$id = intval($_POST['id']);
$nueva_password = $_POST['nueva_password'];

// Cifrar la contraseña (bcrypt)
$hash = password_hash($nueva_password, PASSWORD_BCRYPT);

// Actualizar contraseña
$stmt = $conn->prepare("UPDATE trabajadores SET password = ? WHERE id = ?");
$stmt->bind_param("si", $hash, $id);

if ($stmt->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Error al actualizar contraseña']);
}

$stmt->close();
$conn->close();
?>
