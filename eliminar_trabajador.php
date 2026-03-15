<?php
include 'conexion.php';
header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['id']) || empty($_GET['id'])) {
  echo json_encode(['success' => false, 'message' => 'ID inválido']);
  exit;
}

$id = intval($_GET['id']);

// Verificar si existe el trabajador
$stmt = $conn->prepare("SELECT id FROM trabajadores WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
  echo json_encode(['success' => false, 'message' => 'Trabajador no encontrado']);
  exit;
}

// Eliminar trabajador
$del = $conn->prepare("DELETE FROM trabajadores WHERE id = ?");
$del->bind_param("i", $id);

if ($del->execute()) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
}

$del->close();
$conn->close();
?>
