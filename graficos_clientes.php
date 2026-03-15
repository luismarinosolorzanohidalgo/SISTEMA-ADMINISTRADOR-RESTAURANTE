<?php
include 'conexion.php';
session_start();

// 🔒 Seguridad
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'Administrador') {
  header("Location: login.php");
  exit();
}

// 📈 Obtener clientes registrados por mes
$query = "
  SELECT DATE_FORMAT(fecha_registro, '%Y-%m') AS mes, COUNT(*) AS total
  FROM clientes
  GROUP BY DATE_FORMAT(fecha_registro, '%Y-%m')
  ORDER BY mes ASC
";
$result = $conn->query($query);
$meses = [];
$totales = [];
while ($row = $result->fetch_assoc()) {
  $meses[] = $row['mes'];
  $totales[] = $row['total'];
}

// 🕒 Último cliente agregado
$ultimo = $conn->query("SELECT nombre, fecha_registro FROM clientes ORDER BY fecha_registro DESC LIMIT 1")->fetch_assoc();
$ultimo_nombre = $ultimo['nombre'] ?? 'N/A';
$ultimo_fecha = $ultimo['fecha_registro'] ? date('d/m/Y H:i:s', strtotime($ultimo['fecha_registro'])) : 'N/A';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>👥 Clientes por Mes</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    :root {
      --oro: #c5a64b;
      --oro-claro: #e6cb7f;
      --fondo-claro: #fffdf7;
      --texto-claro: #333;
      --fondo-oscuro: #1e1e1e;
      --texto-oscuro: #f5f5f5;
    }
    body {
      background: var(--fondo-claro);
      font-family: 'Poppins', sans-serif;
      color: var(--texto-claro);
      min-height: 100vh;
      display: flex;
      flex-direction: column;
      transition: background 0.5s, color 0.5s;
    }
    body.dark {
      background: var(--fondo-oscuro);
      color: var(--texto-oscuro);
    }
    h2 {
      color: var(--oro);
      font-weight: 700;
      text-shadow: 0 1px 3px rgba(197,166,75,0.3);
    }
    .volver, .descargar {
      background: linear-gradient(90deg, var(--oro), var(--oro-claro));
      color: #fff;
      border: none;
      border-radius: 30px;
      padding: 10px 22px;
      font-weight: 600;
      transition: 0.3s;
      box-shadow: 0 3px 8px rgba(197,166,75,0.4);
    }
    .volver:hover, .descargar:hover {
      background: linear-gradient(90deg, #b6973e, #d1b762);
      transform: scale(1.05);
    }
    .chart-card {
      background: #fff;
      border-radius: 25px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.1);
      padding: 35px;
      transition: 0.3s;
      animation: fadeIn 1s ease-out;
    }
    body.dark .chart-card {
      background: #2a2a2a;
      box-shadow: 0 10px 30px rgba(255,255,255,0.05);
    }
    .chart-card:hover {
      transform: translateY(-3px);
      box-shadow: 0 12px 35px rgba(0,0,0,0.15);
    }
    footer {
      margin-top: auto;
      text-align: center;
      font-size: 0.9em;
      color: #999;
      padding: 10px;
    }
    .info {
      text-align: center;
      margin-bottom: 25px;
      font-size: 1rem;
      color: #666;
    }
    .info span {
      color: var(--oro);
      font-weight: 600;
    }
    .modo {
      border: none;
      background: transparent;
      font-size: 22px;
      cursor: pointer;
      transition: 0.3s;
    }
    .modo:hover { transform: rotate(20deg); }
    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(15px); }
      to { opacity: 1; transform: translateY(0); }
    }
  </style>
</head>
<body class="container py-4">

  <!-- 🔝 Encabezado -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="m-0">👥 Clientes Registrados por Mes</h2>
    <div class="d-flex gap-2 align-items-center">
      <button class="modo" id="modoToggle" title="Cambiar modo">🌙</button>
      <a href="reportes.php" class="volver">⬅️ Volver</a>
      <button class="descargar" id="btnDescargar">📥 Descargar</button>
    </div>
  </div>

  <!-- 📅 Información adicional -->
  <div class="info">
    Último cliente registrado: <span><?= htmlspecialchars($ultimo_nombre) ?></span><br>
    Fecha y hora: <span><?= $ultimo_fecha ?></span>
  </div>

  <!-- 📊 Gráfico -->
  <div class="chart-card text-center">
    <canvas id="clientesChart"></canvas>
  </div>

  <footer>© <?= date('Y') ?> PowerStreet • Reporte de Clientes</footer>

  <script>
    const ctx = document.getElementById('clientesChart').getContext('2d');
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(197,166,75,0.6)');
    gradient.addColorStop(1, 'rgba(197,166,75,0)');

    const clientesChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode($meses) ?>,
        datasets: [{
          label: 'Nuevos Clientes',
          data: <?= json_encode($totales) ?>,
          borderColor: '#c5a64b',
          backgroundColor: gradient,
          pointBackgroundColor: '#b6973e',
          pointHoverBackgroundColor: '#fff',
          pointBorderColor: '#b6973e',
          borderWidth: 2.5,
          tension: 0.35,
          fill: true
        }]
      },
      options: {
        responsive: true,
        plugins: {
          title: {
            display: true,
            text: 'Crecimiento mensual de clientes registrados',
            color: '#c5a64b',
            font: { size: 18, weight: 'bold' },
            padding: { bottom: 20 }
          },
          tooltip: {
            backgroundColor: '#fff',
            titleColor: '#c5a64b',
            bodyColor: '#333',
            borderColor: '#c5a64b',
            borderWidth: 1,
            displayColors: false,
            callbacks: {
              label: ctx => ` ${ctx.formattedValue} cliente${ctx.formattedValue > 1 ? 's' : ''}`
            }
          },
          legend: { display: false }
        },
        scales: {
          y: {
            beginAtZero: true,
            grid: { color: '#f3e6bb' },
            ticks: { color: '#666', stepSize: 1 }
          },
          x: {
            grid: { display: false },
            ticks: { color: '#666' }
          }
        },
        animation: {
          duration: 1500,
          easing: 'easeOutQuart'
        }
      }
    });

    // 📥 Descargar gráfico como PNG
    document.getElementById('btnDescargar').addEventListener('click', () => {
      const enlace = document.createElement('a');
      enlace.download = 'Clientes_Registrados.png';
      enlace.href = clientesChart.toBase64Image();
      enlace.click();
    });

    // 🌙 Modo oscuro automático o manual
    const modoBtn = document.getElementById('modoToggle');
    const body = document.body;

    // Detectar hora del sistema
    const hora = new Date().getHours();
    if (hora >= 19 || hora <= 6 || window.matchMedia('(prefers-color-scheme: dark)').matches) {
      body.classList.add('dark');
      modoBtn.textContent = '☀️';
    }

    modoBtn.addEventListener('click', () => {
      body.classList.toggle('dark');
      modoBtn.textContent = body.classList.contains('dark') ? '☀️' : '🌙';
    });
  </script>

</body>
</html>
