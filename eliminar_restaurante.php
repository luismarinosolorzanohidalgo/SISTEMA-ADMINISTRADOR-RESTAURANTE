<?php
include "conexion.php";

$id = intval($_GET['id']);

$stmt = $conn->prepare("SELECT logo FROM restaurantes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$rest = $res->fetch_assoc();

if ($rest && !empty($rest['logo']) && file_exists("uploads/" . $rest['logo'])) {
    unlink("uploads/" . $rest['logo']);
}

$stmt = $conn->prepare("DELETE FROM restaurantes WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

echo "<script>
alert('✅ Restaurante eliminado correctamente');
window.location='crud_restaurantes.php';
</script>";
?>
