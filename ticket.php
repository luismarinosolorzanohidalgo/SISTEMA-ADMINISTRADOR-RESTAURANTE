<?php
include 'conexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo "<div style='color:red;text-align:center;margin-top:50px;'>ID de pedido inválido.</div>";
    exit;
}

// Datos del pedido y cliente
$query = "
SELECT p.id, p.total, p.fecha, 
       c.nombre AS cliente, c.correo, c.telefono, 
       s.nombre AS sede
FROM pedidos p
LEFT JOIN clientes c ON p.id_cliente = c.id
LEFT JOIN sedes s ON c.sede_id = s.id
WHERE p.id = $id
";
$result = $conn->query($query);
if (!$result || $result->num_rows === 0) {
    echo "<div style='color:red;text-align:center;margin-top:50px;'>Pedido no encontrado.</div>";
    exit;
}
$pedido = $result->fetch_assoc();
?>
<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Ticket Pedido #<?= $pedido['id'] ?> • Restaurante</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

<style>
body {
    font-family: 'Poppins', sans-serif;
    background: #0b0b1e;
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 40px;
    min-height: 100vh;
}

/* ===================== TICKET ===================== */
.ticket-container {
    background: linear-gradient(160deg,#8e2de2,#ff416c);
    padding: 30px;
    border-radius: 25px;
    width: 420px;
    box-shadow: 0 20px 50px rgba(0,0,0,0.7);
    position: relative;
    animation: slideFade 1s ease-out forwards;
    overflow: hidden;
    border: 3px dashed rgba(255,255,255,0.2);
}
@keyframes slideFade {
    from { opacity: 0; transform: translateY(60px) rotateX(20deg);}
    to { opacity: 1; transform: translateY(0) rotateX(0);}
}

/* ========== HEADER ========== */
.ticket-header {
    text-align: center;
    margin-bottom: 20px;
}
.ticket-header img {
    width: 100px;
    margin-bottom: 10px;
    border-radius: 20px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.6);
    animation: logoSpin 2s infinite linear;
}
@keyframes logoSpin {
    0% { transform: rotate(0deg);}
    50% { transform: rotate(10deg) scale(1.05);}
    100% { transform: rotate(0deg);}
}
.ticket-header h2 {
    font-weight: 800;
    color: #fff;
    text-shadow: 0 0 15px rgba(255,255,255,0.5);
    margin-bottom: 5px;
}
.ticket-header p {
    font-weight: 500;
    color: #fff;
    margin: 0;
}

/* ========== BODY ========== */
.ticket-body {
    background: rgba(0,0,0,0.2);
    color: #fff;
    border-radius: 20px;
    padding: 25px;
    box-shadow: inset 0 0 20px rgba(255,255,255,0.1);
    margin-bottom: 20px;
    position: relative;
    overflow: hidden;
}
.ticket-body p {
    margin: 10px 0;
    font-weight: 600;
}

/* Total con glow animado */
.ticket-body .total {
    font-size: 1.7rem;
    font-weight: 900;
    text-align: right;
    margin-top: 15px;
    color: #fffc00;
    text-shadow: 0 0 10px #fffc00, 0 0 20px #fffc00, 0 0 30px #fffc00;
    animation: neonGlow 1.5s infinite alternate;
}
@keyframes neonGlow {
    from { text-shadow: 0 0 10px #fffc00,0 0 20px #fffc00;}
    to { text-shadow: 0 0 20px #fffc00,0 0 40px #fffc00,0 0 60px #fffc00;}
}

/* ===================== PERFORACIÓN ===================== */
.ticket-container::before, .ticket-container::after {
    content: '';
    position: absolute;
    left: 50%;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #0b0b1e;
    box-shadow: 0 0 0 3px #fff inset;
}
.ticket-container::before { top: -10px; }
.ticket-container::after { bottom: -10px; }

/* ===================== ESPACIO QR ===================== */
.qr-container {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 15px;
    width: 140px;
    height: 140px;
    border: 3px dashed #fff;
    border-radius: 15px;
    margin-left: auto;
    margin-right: auto;
    animation: qrGlow 1.5s infinite alternate;
    color: #fff;
    font-weight: 700;
}
@keyframes qrGlow {
    from { box-shadow: 0 0 10px #fff, 0 0 20px #ff416c;}
    to { box-shadow: 0 0 20px #fff, 0 0 40px #ff416c;}
}
.qr-placeholder {
    text-align: center;
    font-size: 0.85rem;
    color: #fff;
    opacity: 0.7;
}

/* ===================== BOTÓN ===================== */
.btn-print {
    display: block;
    margin: 15px auto 0;
    background: linear-gradient(45deg,#ff416c,#8e2de2);
    color: #fff;
    font-weight: 800;
    padding: 12px 28px;
    border-radius: 50px;
    text-decoration: none;
    transition: all 0.3s;
    box-shadow: 0 8px 25px rgba(255,65,108,0.6);
}
.btn-print:hover {
    transform: scale(1.1) rotate(-2deg);
    box-shadow: 0 15px 40px rgba(255,65,108,0.8);
}
</style>
</head>
<body>

<div class="ticket-container">
    <div class="ticket-header">
        <img src="restaurante.png" alt="Restaurante Logo">
        <h2>Restaurante</h2>
        <p>Ticket de Pedido #<?= $pedido['id'] ?></p>
        <p><?= date("d/m/Y H:i", strtotime($pedido['fecha'])) ?></p>
    </div>

    <div class="ticket-body">
        <p><strong>Cliente:</strong> <?= htmlspecialchars($pedido['cliente']) ?></p>
        <p><strong>Correo:</strong> <?= htmlspecialchars($pedido['correo']) ?></p>
        <p><strong>Teléfono:</strong> <?= htmlspecialchars($pedido['telefono']) ?></p>
        <p><strong>Sede:</strong> <?= htmlspecialchars($pedido['sede'] ?? 'Sin asignar') ?></p>
        <hr style="border-color:#fff;">
        <p class="total">Total: S/. <?= number_format($pedido['total'],2) ?></p>

        <div class="qr-container">
            <div class="qr-placeholder">TU QR AQUÍ</div>
        </div>
    </div>

    <a href="#" class="btn-print" onclick="window.print()"><i class="fas fa-print me-2"></i> Imprimir Ticket</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
