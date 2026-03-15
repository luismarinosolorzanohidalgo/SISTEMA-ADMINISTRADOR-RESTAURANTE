<?php
include 'conexion.php';
session_start();

// Seguridad
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
  header("Location: login.php");
  exit();
}

// Filtros opcionales
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$estado = $_GET['estado'] ?? '';

$query = "
  SELECT p.id, c.nombre AS cliente, p.total, p.estado, p.fecha
  FROM pedidos p
  INNER JOIN clientes c ON p.id_cliente = c.id
  WHERE 1
";
if ($fecha_inicio && $fecha_fin) $query .= " AND DATE(p.fecha) BETWEEN '$fecha_inicio' AND '$fecha_fin'";
if ($estado) $query .= " AND p.estado = '$estado'";
$query .= " ORDER BY p.fecha DESC";
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📄 Reporte de Ventas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { font-family:'Poppins',sans-serif; background:#fffdf7; margin:30px; }
    h2 { color:#c5a64b; font-weight:700; text-align:center; }
    table { width:100%; border-collapse:collapse; margin-top:20px; }
    th, td { border:1px solid #ccc; padding:8px; text-align:center; }
    th { background:#f9e8b2; }
    tr:nth-child(even){background:#fdf8e6;}
    .botones { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
    .btn-oro {
      background:#c5a64b; color:white; border:none;
      border-radius:25px; padding:10px 20px; text-decoration:none;
    }
    .btn-oro:hover { background:#b6973e; color:white; }
  </style>
</head>
<body>

  <div class="botones">
    <a href="reportes.php" class="btn-oro">⬅️ Volver</a>
    <button class="btn-oro" id="descargarBtn">📥 Descargar PDF</button>
  </div>

  <h2>📊 Reporte de Ventas</h2>
  <p class="text-center text-muted mb-4">
    <?= $fecha_inicio && $fecha_fin ? "Del $fecha_inicio al $fecha_fin" : "Todos los registros" ?>
    <?= $estado ? " | Estado: $estado" : "" ?>
  </p>

  <div id="contenido">
    <table>
      <thead>
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
              <td><?= $row['estado'] ?></td>
              <td><?= date("d/m/Y H:i", strtotime($row['fecha'])) ?></td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr><td colspan="5">No hay datos disponibles</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <script>
    // 💾 Descarga el contenido como PDF usando impresión en segundo plano
    document.getElementById("descargarBtn").addEventListener("click", () => {
      const nombreArchivo = "Reporte_Ventas.pdf";
      const contenido = document.getElementById("contenido").innerHTML;
      const ventana = window.open('', '', 'width=900,height=700');
      ventana.document.write(`
        <html>
          <head>
            <title>${nombreArchivo}</title>
            <style>
              body { font-family:'Poppins',sans-serif; }
              table { width:100%; border-collapse:collapse; }
              th, td { border:1px solid #ccc; padding:6px; text-align:center; }
              th { background:#f9e8b2; }
              tr:nth-child(even){background:#fdf8e6;}
              h2{text-align:center;color:#c5a64b;}
            </style>
          </head>
          <body>
            <h2>📊 Reporte de Ventas</h2>
            ${contenido}
          </body>
        </html>
      `);
      ventana.document.close();
      ventana.focus();
      // Abre el diálogo nativo del navegador para guardar/descargar como PDF
      ventana.print();
    });
  </script>

</body>
</html>
