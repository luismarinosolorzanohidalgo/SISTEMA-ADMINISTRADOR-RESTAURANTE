<?php
include 'conexion.php';
session_start();

// Verificar rol administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
  header("Location: login.php");
  exit();
}

// Filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$estado = $_GET['estado'] ?? '';

// Consulta principal
$query = "
  SELECT p.id, c.nombre AS cliente, p.total, p.estado, p.fecha
  FROM pedidos p
  INNER JOIN clientes c ON p.id_cliente = c.id
  WHERE 1
";

if ($fecha_inicio && $fecha_fin) {
  $query .= " AND DATE(p.fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
}

if ($estado) {
  $query .= " AND p.estado = '$estado'";
}

$query .= " ORDER BY p.fecha DESC";
$result = $conn->query($query);

// Totales
$total_ventas = $conn->query("SELECT SUM(total) AS total FROM pedidos WHERE estado='Entregado'")->fetch_assoc()['total'] ?? 0;
$total_pedidos = $conn->query("SELECT COUNT(*) AS total FROM pedidos")->fetch_assoc()['total'] ?? 0;
$total_clientes = $conn->query("SELECT COUNT(*) AS total FROM clientes")->fetch_assoc()['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📊 Reporte de Ventas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    body { background: #fffdf7; font-family: 'Poppins', sans-serif; }
    .card { border-radius: 20px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); transition: transform 0.2s; cursor: pointer; }
    .card:hover { transform: scale(1.03); }
    .btn-export { border-radius: 30px; }
    h1 { color: #c5a64b; font-weight: 700; }
    .volver { background-color: #c5a64b; color: white; border: none; border-radius: 25px; padding: 10px 20px; }
    .volver:hover { background-color: #b6973e; }
  </style>
</head>
<body class="container py-4">

  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="m-0">📊 Reporte General de Ventas</h1>
    <a href="principal.php" class="volver">⬅️ Volver al Panel</a>
  </div>

  <div class="row text-center mb-4">
    <div class="col-md-4">
      <div class="card p-3 bg-light" onclick="location.href='graficos_ventas.php'">
        <h5>Total de Ventas</h5>
        <h3 class="text-success">S/ <?= number_format($total_ventas, 2) ?></h3>
        <p class="text-muted mb-0">Ver en gráficos 📈</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 bg-light" onclick="location.href='graficos_pedidos.php'">
        <h5>Total de Pedidos</h5>
        <h3 class="text-primary"><?= $total_pedidos ?></h3>
        <p class="text-muted mb-0">Ver en gráficos 📊</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card p-3 bg-light" onclick="location.href='graficos_clientes.php'">
        <h5>Total de Clientes</h5>
        <h3 class="text-warning"><?= $total_clientes ?></h3>
        <p class="text-muted mb-0">Ver en gráficos 👥</p>
      </div>
    </div>
  </div>

  <!-- Filtros -->
  <form method="GET" class="card p-3 mb-4">
    <div class="row align-items-end">
      <div class="col-md-3">
        <label class="form-label">Desde</label>
        <input type="date" name="fecha_inicio" value="<?= $fecha_inicio ?>" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Hasta</label>
        <input type="date" name="fecha_fin" value="<?= $fecha_fin ?>" class="form-control">
      </div>
      <div class="col-md-3">
        <label class="form-label">Estado</label>
        <select name="estado" class="form-select">
          <option value="">Todos</option>
          <option <?= $estado == 'Pendiente' ? 'selected' : '' ?>>Pendiente</option>
          <option <?= $estado == 'En preparación' ? 'selected' : '' ?>>En preparación</option>
          <option <?= $estado == 'En camino' ? 'selected' : '' ?>>En camino</option>
          <option <?= $estado == 'Entregado' ? 'selected' : '' ?>>Entregado</option>
          <option <?= $estado == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
          <option <?= $estado == 'Rechazado' ? 'selected' : '' ?>>Rechazado</option>
        </select>
      </div>
      <div class="col-md-3 text-center">
        <button type="submit" class="btn btn-dark w-100 mb-2">🔍 Filtrar</button>
        <a href="reportes.php" class="btn btn-secondary w-100">🔄 Limpiar</a>
      </div>
    </div>
  </form>

  <!-- Tabla -->
  <div class="card p-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="m-0">📋 Resultados</h5>
      <div>
        <a href="exportar_excel.php?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-success btn-export me-2">📗 Exportar Excel</a>
        <a href="exportar_pdf.php?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-danger btn-export">📕 Exportar PDF</a>
      </div>
    </div>

    <table class="table table-hover align-middle">
      <thead class="table-warning">
        <tr>
          <th>ID</th>
          <th>Cliente</th>
          <th>Total (S/)</th>
          <th>Estado</th>
          <th>Fecha</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td><?= htmlspecialchars($row['cliente']) ?></td>
              <td><?= number_format($row['total'], 2) ?></td>
              <td>
                <?php
                  $color = match($row['estado']) {
                    'Entregado' => 'success',
                    'Pendiente' => 'secondary',
                    'Cancelado' => 'danger',
                    'Rechazado' => 'dark',
                    'En preparación' => 'warning',
                    'En camino' => 'info',
                    default => 'light'
                  };
                ?>
                <span class="badge bg-<?= $color ?>"><?= $row['estado'] ?></span>
              </td>
              <td><?= date("d/m/Y H:i", strtotime($row['fecha'])) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5" class="text-center text-muted">Sin resultados</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

</body>
</html>
