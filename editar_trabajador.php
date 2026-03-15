<?php
// editar_trabajador.php
session_start();
include 'conexion.php';

header('Content-Type: application/json');
error_reporting(0);

// --- Seguridad: solo administradores ---
if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
    http_response_code(403);
    echo json_encode([
        'success' => false,
        'message' => 'No autorizado.'
    ]);
    exit;
}

// --- Validar método POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
    exit;
}

// --- Recibir datos ---
$id                = intval($_POST['id'] ?? 0);
$nombre            = trim($_POST['nombre'] ?? '');
$correo            = trim($_POST['correo'] ?? '');
$telefono          = trim($_POST['telefono'] ?? '');
$direccion         = trim($_POST['direccion'] ?? '');
$dni               = trim($_POST['dni'] ?? '');
$fecha_nacimiento  = trim($_POST['fecha_nacimiento'] ?? '');
$rol               = trim($_POST['rol'] ?? '');
$sede              = trim($_POST['sede'] ?? '');

// --- Validación básica ---
if ($id <= 0 || !$nombre || !$correo || !$dni || !$fecha_nacimiento || !$rol || !$sede) {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan datos obligatorios.'
    ]);
    exit;
}

// --- Validar correo ---
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode([
        'success' => false,
        'message' => 'Correo inválido.'
    ]);
    exit;
}

// --- Validar formato de fecha ---
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_nacimiento)) {
    echo json_encode([
        'success' => false,
        'message' => 'Formato de fecha inválido (AAAA-MM-DD).'
    ]);
    exit;
}

// --- Verificar duplicados (correo o DNI) ---
$check = $conn->prepare("SELECT id FROM trabajadores WHERE (correo = ? OR dni = ?) AND id != ?");
$check->bind_param("ssi", $correo, $dni, $id);
$check->execute();
$dup = $check->get_result();
if ($dup->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Ya existe otro trabajador con ese correo o DNI.'
    ]);
    $check->close();
    exit;
}
$check->close();

// --- Actualizar datos ---
$sql = "UPDATE trabajadores 
        SET nombre=?, correo=?, telefono=?, direccion=?, dni=?, fecha_nacimiento=?, rol=?, sede=? 
        WHERE id=?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode([
        'success' => false,
        'message' => 'Error en la consulta: ' . $conn->error
    ]);
    exit;
}

$stmt->bind_param(
    "ssssssssi",
    $nombre,
    $correo,
    $telefono,
    $direccion,
    $dni,
    $fecha_nacimiento,
    $rol,
    $sede,
    $id
);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Trabajador actualizado correctamente.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar: ' . $stmt->error
    ]);
}

$stmt->close();
$conn->close();
exit;
?>
