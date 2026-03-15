<?php
include 'conexion.php';
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Reporte_Ventas.xls");

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$estado = $_GET['estado'] ?? '';

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

$result = $conn->query($query);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Cliente</th><th>Total (S/)</th><th>Estado</th><th>Fecha</th></tr>";
while ($row = $result->fetch_assoc()) {
  echo "<tr>
          <td>{$row['id']}</td>
          <td>{$row['cliente']}</td>
          <td>{$row['total']}</td>
          <td>{$row['estado']}</td>
          <td>{$row['fecha']}</td>
        </tr>";
}
echo "</table>";
