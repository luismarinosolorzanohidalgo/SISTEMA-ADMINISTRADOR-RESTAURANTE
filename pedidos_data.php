<?php
include 'conexion.php';

// Sanitize inputs
$q = trim($conn->real_escape_string($_GET['q'] ?? ''));
$estado = trim($conn->real_escape_string($_GET['estado'] ?? ''));
$fechaInicio = trim($conn->real_escape_string($_GET['fechaInicio'] ?? ''));
$fechaFin = trim($conn->real_escape_string($_GET['fechaFin'] ?? ''));
$single = intval($_GET['single'] ?? 0);

// Base query: join pedidos con clientes
$sql = "SELECT p.id, p.id_cliente, c.nombre AS cliente, p.estado, p.total, p.fecha
        FROM pedidos p
        LEFT JOIN clientes c ON p.id_cliente = c.id
        WHERE 1=1 ";

// filtros
if ($q !== '') {
  // buscar por id pedido, nombre cliente, o total
  $like = "%{$q}%";
  $sql .= " AND (p.id LIKE '{$q}' OR c.nombre LIKE '{$like}' OR p.total LIKE '{$like}')";
}
if ($estado !== '') {
  $sql .= " AND p.estado = '{$estado}'";
}
if ($fechaInicio && $fechaFin) {
  $sql .= " AND DATE(p.fecha) BETWEEN '{$fechaInicio}' AND '{$fechaFin}'";
} elseif ($fechaInicio) {
  $sql .= " AND DATE(p.fecha) >= '{$fechaInicio}'";
} elseif ($fechaFin) {
  $sql .= " AND DATE(p.fecha) <= '{$fechaFin}'";
}

$sql .= " ORDER BY p.fecha DESC";

if ($single > 0) {
  // fetch single row by id (ignore filters)
  $sqlSingle = "SELECT p.id, p.id_cliente, c.nombre AS cliente, p.estado, p.total, p.fecha
                FROM pedidos p
                LEFT JOIN clientes c ON p.id_cliente = c.id
                WHERE p.id = {$single} LIMIT 1";
  $res = $conn->query($sqlSingle);
  if ($res && $row = $res->fetch_assoc()) {
    $estado = $row['estado'];
    $color = 'secondary';
    switch ($estado) {
      case 'Pendiente': $color='warning'; break;
      case 'En Preparación': $color='secondary'; break;
      case 'En proceso': $color='info'; break;
      case 'Completado': $color='success'; break;
      case 'Cancelado': $color='dark'; break;
      case 'Rechazado': $color='danger'; break;
    }
    // output a single <tr>
    echo "<tr id='row-{$row['id']}' data-id='{$row['id']}'>";
    echo "<td>{$row['id']}</td>";
    echo "<td class='fw-semibold'>".htmlspecialchars($row['cliente'])."</td>";
    echo "<td><span class='badge-status badge bg-{$color}'>".htmlspecialchars($row['estado'])."</span></td>";
    echo "<td><b>".number_format($row['total'],2)."</b></td>";
    echo "<td>".date('d/m/Y H:i', strtotime($row['fecha']))."</td>";
    echo "<td class='actions'>";
    if (!in_array($row['estado'], ['Completado','Cancelado','Rechazado'])) {
      echo "<button onclick=\"cambiarEstado({$row['id']},'Completado', this)\" class='btn btn-success btn-sm action-btn me-2'><i class='bi bi-check-circle'></i> Aceptar</button>";
      echo "<button onclick=\"cambiarEstado({$row['id']},'Rechazado', this)\" class='btn btn-danger btn-sm action-btn'><i class='bi bi-x-circle'></i> Rechazar</button>";
    } else {
      echo "<span class='text-muted fst-italic'>—</span>";
    }
    echo " <button onclick='verDetalle({$row['id']})' class='btn btn-outline-primary btn-sm ms-2'><i class='bi bi-eye'></i></button>";
    echo "</td>";
    echo "</tr>";
    exit;
  } else {
    echo ""; exit;
  }
}

// else: fetch full table
$res = $conn->query($sql);
?>
<table class="table table-hover align-middle text-center mb-0">
  <thead>
    <tr>
      <th>ID</th>
      <th>Cliente</th>
      <th>Estado</th>
      <th>Total (S/)</th>
      <th>Fecha</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
<?php
if ($res && $res->num_rows > 0):
  while ($r = $res->fetch_assoc()):
    $estado = $r['estado'];
    $color = 'secondary';
    switch ($estado) {
      case 'Pendiente': $color='warning'; break;
      case 'En Preparación': $color='secondary'; break;
      case 'En proceso': $color='info'; break;
      case 'Completado': $color='success'; break;
      case 'Cancelado': $color='dark'; break;
      case 'Rechazado': $color='danger'; break;
    }
?>
    <tr id="row-<?= $r['id'] ?>" data-id="<?= $r['id'] ?>">
      <td><?= $r['id'] ?></td>
      <td class="fw-semibold"><?= htmlspecialchars($r['cliente']) ?></td>
      <td><span class="badge-status badge bg-<?= $color ?>"><?= htmlspecialchars($r['estado']) ?></span></td>
      <td><b><?= number_format($r['total'],2) ?></b></td>
      <td><?= date('d/m/Y H:i', strtotime($r['fecha'])) ?></td>
      <td class="actions">
        <?php if (!in_array($r['estado'], ['Completado','Cancelado','Rechazado'])): ?>
          <button onclick="cambiarEstado(<?= $r['id'] ?>,'Completado', this)" class="btn btn-success btn-sm action-btn me-2"><i class="bi bi-check-circle"></i> Aceptar</button>
          <button onclick="cambiarEstado(<?= $r['id'] ?>,'Rechazado', this)" class="btn btn-danger btn-sm action-btn"><i class="bi bi-x-circle"></i> Rechazar</button>
        <?php else: ?>
          <span class="text-muted fst-italic">—</span>
        <?php endif; ?>
        <button onclick="verDetalle(<?= $r['id'] ?>)" class="btn btn-outline-primary btn-sm ms-2"><i class="bi bi-eye"></i></button>
      </td>
    </tr>
<?php
  endwhile;
else:
?>
    <tr><td colspan="6" class="text-muted py-4">No se encontraron pedidos.</td></tr>
<?php
endif;
?>
  </tbody>
</table>
<?php
$conn->close();
?>
