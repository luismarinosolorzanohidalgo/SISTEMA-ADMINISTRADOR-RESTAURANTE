<?php
include 'conexion.php';
session_start();

header('Content-Type: application/json');

// --- Seguridad: solo administradores ---
if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
    echo json_encode([
        'success' => false,
        'message' => 'Acceso no autorizado.'
    ]);
    exit();
}

// --- Validar método ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
    exit();
}

// --- Recibir y limpiar datos ---
$nombre = trim($_POST['nombre'] ?? '');
$correo = trim($_POST['correo'] ?? '');
$direccion = trim($_POST['direccion'] ?? '');
$password = trim($_POST['password'] ?? '');
$dni = trim($_POST['dni'] ?? '');
$fecha_nacimiento = trim($_POST['fecha_nacimiento'] ?? '');
$rol = trim($_POST['rol'] ?? 'Empleado');
$sede = trim($_POST['sede'] ?? '');
$latitud = trim($_POST['latitud'] ?? null);
$longitud = trim($_POST['longitud'] ?? null);

// --- Validar campos obligatorios ---
if (empty($nombre) || empty($correo) || empty($password)) {
    echo json_encode([
        'success' => false,
        'message' => 'Faltan campos obligatorios.'
    ]);
    exit();
}

// --- Verificar duplicados por correo o DNI ---
$check = $conn->prepare("SELECT id FROM trabajadores WHERE correo = ? OR dni = ?");
$check->bind_param("ss", $correo, $dni);
$check->execute();
$result = $check->get_result();

if ($result->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Ya existe un trabajador con ese correo o DNI.'
    ]);
    exit();
}

// --- Hashear contraseña ---
$hashed = password_hash($password, PASSWORD_DEFAULT);

// --- Insertar nuevo trabajador ---
$stmt = $conn->prepare("INSERT INTO trabajadores (nombre, correo, telefono, direccion, dni, fecha_nacimiento, rol, sede) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $nombre, $correo, $telefono, $direccion, $dni, $fecha_nacimiento, $_POST['rol'], $_POST['sede']);


if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Trabajador agregado correctamente.'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Error al agregar el trabajador: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
?>
