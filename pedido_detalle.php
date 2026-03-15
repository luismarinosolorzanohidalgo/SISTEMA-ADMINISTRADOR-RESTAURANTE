<?php
include 'conexion.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
  echo "<div class='p-3 text-danger'>ID de pedido inválido.</div>";
  exit;
}

// ✅ Datos del pedido y cliente
$q = "
SELECT p.id, p.total, p.fecha, p.estado, c.nombre AS cliente, c.correo, c.telefono, c.direccion
FROM pedidos p
LEFT JOIN clientes c ON p.id_cliente = c.id
WHERE p.id = $id LIMIT 1";
$res = $conn->query($q);

if (!$res || $res->num_rows === 0) {
  echo "<div class='p-3 text-muted'>Pedido no encontrado.</div>";
  exit;
}
$pedido = $res->fetch_assoc();

// ✅ Items del pedido (tabla real: detalle_pedidos)
$items = [];
$q2 = "
SELECT d.cantidad, d.precio_unitario, pl.nombre AS producto, pl.imagen AS img
FROM detalle_pedidos d
LEFT JOIN platos pl ON d.plato_id = pl.id
WHERE d.pedido_id = $id";
$res2 = $conn->query($q2);
if ($res2 && $res2->num_rows > 0) {
  while ($it = $res2->fetch_assoc()) $items[] = $it;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Detalle del Pedido #<?= $pedido['id'] ?> • PowerStreet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background-color: #f9fafc;
      animation: fadeIn 0.8s ease;
    }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(30px); }
      to { opacity: 1; transform: translateY(0); }
    }
    .pedido-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      overflow: hidden;
      padding: 25px;
      transition: 0.3s;
    }
    .pedido-card:hover { transform: translateY(-3px); }
    .pedido-header {
      border-bottom: 2px solid #eee;
      padding-bottom: 15px;
      margin-bottom: 20px;
    }
    .pedido-header h4 {
      font-weight: 700;
      color: #444;
    }
    .pedido-info small {
      color: #888;
    }
    .list-group-item {
      border: none;
      border-bottom: 1px solid #f1f1f1;
      transition: background 0.2s;
    }
    .list-group-item:hover {
      background: #fffbea;
    }
    .badge-estado {
      padding: 6px 12px;
      border-radius: 12px;
      font-size: 0.85rem;
    }
    .bg-pendiente { background: #ffb300; color: #fff; }
    .bg-preparacion { background: #455a64; color: #fff; }
    .bg-listo { background: #2ecc71; color: #fff; }
    .bg-rechazado { background: #e74c3c; color: #fff; }
    .bg-cancelado { background: #9e9e9e; color: #fff; }
    .btn-modern {
      border-radius: 10px;
      font-weight: 600;
      transition: 0.3s;
    }
    .btn-modern:hover {
      transform: scale(1.05);
    }
    .img-thumb {
      width: 75px;
      height: 65px;
      object-fit: cover;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.15);
    }
  </style>
</head>

<body>
<div class="container py-4">
  <div class="pedido-card mx-auto" style="max-width: 900px;">
    <div class="pedido-header d-flex justify-content-between align-items-center">
      <h4>Pedido #<?= $pedido['id'] ?></h4>
      <span class="badge-estado 
        <?= match($pedido['estado']) {
            'Pendiente' => 'bg-pendiente',
            'En Preparación' => 'bg-preparacion',
            'Listo' => 'bg-listo',
            'Rechazado' => 'bg-rechazado',
            default => 'bg-cancelado'
        } ?>">
        <?= htmlspecialchars($pedido['estado']) ?>
      </span>
    </div>

    <div class="pedido-info mb-4">
      <p><strong>👤 Cliente:</strong> <?= htmlspecialchars($pedido['cliente']) ?></p>
      <p><strong>📧 Correo:</strong> <?= htmlspecialchars($pedido['correo']) ?></p>
      <p><strong>📞 Teléfono:</strong> <?= htmlspecialchars($pedido['telefono']) ?></p>
      <p><strong>📍 Dirección:</strong> <?= htmlspecialchars($pedido['direccion']) ?></p>
      <p><strong>🕓 Fecha:</strong> <?= date('d/m/Y H:i', strtotime($pedido['fecha'])) ?></p>
    </div>

    <h5 class="fw-bold mb-3 text-warning"><i class="bi bi-basket"></i> Platos del pedido</h5>
    <div class="list-group mb-4">
      <?php if (count($items) > 0): ?>
        <?php foreach ($items as $it): ?>
          <div class="list-group-item d-flex align-items-center">
            <?php if (!empty($it['img']) && file_exists($it['img'])): ?>
              <img src="<?= htmlspecialchars($it['img']) ?>" class="img-thumb me-3">
            <?php else: ?>
              <div class="bg-light d-flex align-items-center justify-content-center me-3" 
                   style="width:75px;height:65px;border-radius:8px;color:#999">
                <i class="bi bi-image" style="font-size:20px;"></i>
              </div>
            <?php endif; ?>

            <div class="flex-grow-1">
              <div class="fw-bold"><?= htmlspecialchars($it['producto']) ?></div>
              <small class="text-muted">S/ <?= number_format($it['precio_unitario'],2) ?> × <?= $it['cantidad'] ?> unidades</small>
            </div>
            <div class="fw-bold text-end text-success">S/ <?= number_format($it['precio_unitario'] * $it['cantidad'], 2) ?></div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="list-group-item text-center text-muted py-3">
          <i class="bi bi-info-circle"></i> Este pedido no tiene platos registrados.
        </div>
      <?php endif; ?>
    </div>

    <div class="d-flex justify-content-between align-items-center">
      <h4 class="fw-bold text-dark">Total: <span class="text-success">S/ <?= number_format($pedido['total'],2) ?></span></h4>
      <div class="d-flex gap-2">
        <?php if (!in_array($pedido['estado'], ['Completado','Rechazado','Cancelado'])): ?>
          <button class="btn btn-success btn-modern" onclick="cambiarEstado(<?= $pedido['id'] ?>, 'En Preparación')">
            <i class="bi bi-check-circle"></i> Aceptar
          </button>
          <button class="btn btn-danger btn-modern" onclick="cambiarEstado(<?= $pedido['id'] ?>, 'Rechazado')">
            <i class="bi bi-x-circle"></i> Rechazar
          </button>
        <?php endif; ?>
        <button class="btn btn-outline-primary btn-modern" onclick="window.print()"><i class="bi bi-printer"></i> Imprimir</button>
      </div>
    </div>
  </div>
</div>

<script>
function cambiarEstado(id, nuevoEstado) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: 'Cambiar el estado del pedido a "' + nuevoEstado + '"',
    icon: 'question',
    showCancelButton: true,
    confirmButtonColor: '#2ecc71',
    cancelButtonColor: '#e74c3c',
    confirmButtonText: 'Sí, confirmar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post('actualizar_estado.php', { id: id, estado: nuevoEstado }, function(res) {
        if (res.status === 'success') {
          Swal.fire({
            icon: 'success',
            title: '¡Listo!',
            text: res.message,
            showConfirmButton: false,
            timer: 1500
          }).then(() => location.reload());
        } else {
          Swal.fire({ icon: 'error', title: 'Error', text: res.message });
        }
      }, 'json').fail(() => {
        Swal.fire({ icon: 'error', title: 'Error', text: 'No se pudo actualizar el estado.' });
      });
    }
  });
}
</script>

</body>
</html>
