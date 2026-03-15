<?php
include 'conexion.php';
header('Content-Type: application/json');

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id = $_POST['id'];
  $nueva = password_hash($_POST['nueva_password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("UPDATE trabajadores SET password=? WHERE id=?");
  $stmt->bind_param("si", $nueva, $id);

  if ($stmt->execute()) {
    $response = ['success' => true, 'message' => 'Contraseña actualizada correctamente'];
  } else {
    $response = ['success' => false, 'message' => 'Error al actualizar la contraseña'];
  }

  echo json_encode($response);
}
?>
