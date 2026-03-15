<?php
include 'conexion.php';
header('Content-Type: application/json');

if (!isset($_POST['id'], $_POST['estado'])) {
  http_response_code(400);
  echo json_encode(['status'=>'error','message'=>'Faltan parámetros']);
  exit;
}

$id = intval($_POST['id']);
$estado = $conn->real_escape_string($_POST['estado']);

// Validar estados permitidos (evita valores raros)
$permitidos = ['Pendiente','En Preparación','En proceso','Completado','Cancelado','Rechazado'];
if (!in_array($estado, $permitidos)) {
  http_response_code(400);
  echo json_encode(['status'=>'error','message'=>'Estado inválido']);
  exit;
}

// Actualizar
$stmt = $conn->prepare("UPDATE pedidos SET estado=? WHERE id=?");
$stmt->bind_param('si', $estado, $id);
if ($stmt->execute()) {
  echo json_encode(['status'=>'success','message'=>'Estado actualizado']);
} else {
  http_response_code(500);
  echo json_encode(['status'=>'error','message'=>'Error al actualizar']);
}
$stmt->close();
$conn->close();
?>
