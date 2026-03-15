<?php
include 'conexion.php';
session_start();

// Seguridad: solo administradores
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
  header("Location: login.php");
  exit();
}

// Consulta de pedidos agrupados por estado
$query = "SELECT estado, COUNT(*) AS cantidad FROM pedidos GROUP BY estado";
$result = $conn->query($query);

$estados = [];
$valores = [];
while ($row = $result->fetch_assoc()) {
  $estados[] = $row['estado'];
  $valores[] = $row['cantidad'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📊 Pedidos por Estado</title>
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
      color: #fff;
    }
    .chart-card {
      background: #fff;
      border-radius: 20px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      padding: 30px;
      max-width: 700px;
      margin: 0 auto;
      transition: 0.3s;
    }
    .chart-card:hover {
      box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    canvas {
      max-height: 400px;
    }
  </style>
</head>
<body class="container py-4">

  <!-- Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0">📊 Pedidos por Estado</h2>
    <div class="d-flex gap-2">
      <a href="reportes.php" class="volver">⬅️ Volver</a>
      <button class="descargar" id="btnDescargar">📥 Descargar Gráfico</button>
    </div>
  </div>

  <!-- Tarjeta del gráfico -->
  <div class="chart-card text-center">
    <canvas id="pedidosChart"></canvas>
  </div>

  <script>
    const ctx = document.getElementById('pedidosChart').getContext('2d');
    const pedidosChart = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($estados) ?>,
        datasets: [{
          data: <?= json_encode($valores) ?>,
          backgroundColor: [
            '#c5a64b',
            '#36a2eb',
            '#ffce56',
            '#ff6384',
            '#4bc0c0',
            '#9966ff'
          ],
          borderWidth: 1,
          hoverOffset: 10
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Distribución de pedidos según estado',
            color: '#c5a64b',
            font: { size: 18, weight: 'bold' },
            padding: { bottom: 20 }
          },
          tooltip: {
            callbacks: {
              label: (context) => {
                const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                const porcentaje = ((context.raw / total) * 100).toFixed(1);
                return `${context.label}: ${context.raw} pedidos (${porcentaje}%)`;
              }
            }
          },
          legend: {
            position: 'bottom',
            labels: { font: { size: 14 } }
          }
        },
        animation: {
          animateRotate: true,
          duration: 1200,
          easing: 'easeOutQuart'
        }
      }
    });

    // Descargar gráfico como imagen
    document.getElementById('btnDescargar').addEventListener('click', () => {
      const enlace = document.createElement('a');
      enlace.download = 'Pedidos_por_Estado.png';
      enlace.href = pedidosChart.toBase64Image();
      enlace.click();
    });
  </script>

</body>
</html>
