<?php
include 'conexion.php';
session_start();

// Verificación de sesión
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
  header("Location: login.php");
  exit();
}

// Consulta de ventas por mes
$query = "
  SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(total) AS total
  FROM pedidos
  WHERE estado='Entregado'
  GROUP BY DATE_FORMAT(fecha, '%Y-%m')
  ORDER BY mes ASC
";
$res = $conn->query($query);
$meses = [];
$totales = [];
while ($r = $res->fetch_assoc()) {
  $meses[] = $r['mes'];
  $totales[] = $r['total'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📈 Reporte de Ventas por Mes</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: #fffdf7;
      font-family: 'Poppins', sans-serif;
      color: #333;
    }
    h2 {
      color: #c5a64b;
      font-weight: 700;
    }
    .volver, .descargar {
      background: linear-gradient(90deg, #c5a64b, #e6cb7f);
      color: #fff;
      border: none;
      border-radius: 30px;
      padding: 10px 22px;
      text-decoration: none;
      font-weight: 600;
      transition: 0.3s;
      box-shadow: 0 3px 8px rgba(197,166,75,0.4);
    }
    .volver:hover, .descargar:hover {
      background: linear-gradient(90deg, #b6973e, #d1b762);
      transform: translateY(-1px);
    }
    .chart-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      padding: 30px;
      transition: 0.3s;
    }
    .chart-card:hover {
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    canvas {
      max-height: 420px;
      margin-top: 10px;
    }
  </style>
</head>
<body class="container py-4">

  <!-- Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0">📈 Ventas por Mes</h2>
    <div class="d-flex gap-2">
      <a href="reportes.php" class="volver">⬅️ Volver</a>
      <button class="descargar" id="btnDescargar">📥 Descargar Gráfico</button>
    </div>
  </div>

  <!-- Tarjeta del gráfico -->
  <div class="chart-card">
    <canvas id="grafico"></canvas>
  </div>

  <script>
    const ctx = document.getElementById('grafico').getContext('2d');
    const ventasChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: <?= json_encode($meses) ?>,
        datasets: [{
          label: 'Total de Ventas (S/)',
          data: <?= json_encode($totales) ?>,
          backgroundColor: 'rgba(197,166,75,0.8)',
          borderColor: '#b6973e',
          borderWidth: 1,
          borderRadius: 6,
          hoverBackgroundColor: '#e6cb7f'
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Evolución de Ventas por Mes',
            font: { size: 18, weight: 'bold' },
            color: '#c5a64b',
            padding: { top: 10, bottom: 20 }
          },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'S/ ' + context.formattedValue;
              }
            }
          },
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { callback: value => 'S/ ' + value }
          },
          x: {
            grid: { display: false }
          }
        },
        animation: {
          duration: 1200,
          easing: 'easeOutQuart'
        }
      }
    });

    // Descargar gráfico como imagen
    document.getElementById('btnDescargar').addEventListener('click', () => {
      const enlace = document.createElement('a');
      enlace.download = 'Reporte_Ventas.png';
      enlace.href = ventasChart.toBase64Image();
      enlace.click();
    });
  </script>

</body>
</html>
