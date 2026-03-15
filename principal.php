<?php
session_start();
include "conexion.php";

// 🔒 Simulación temporal de sesión
if (!isset($_SESSION['nombre'])) {
  $_SESSION['nombre'] = "Luis Solórzano";
  $_SESSION['rol'] = "Administrador";
}

// 🚫 Verificación de rol
if ($_SESSION['rol'] !== 'Administrador') {
  echo "
  <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      Swal.fire({
        icon: 'error',
        title: 'Acceso denegado',
        text: 'Solo los administradores pueden ingresar aquí',
        confirmButtonText: 'Volver al login',
        confirmButtonColor: '#d4af37'
      }).then(() => {
        window.location.href = '../login.php';
      });
    });
  </script>";
  exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>  Panel Administrativo </title>

  <!-- Bootstrap + Iconos + SweetAlert + Fuentes -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- Librería Particles.js -->
  <script src="https://cdn.jsdelivr.net/npm/particles.js@2.0.0/particles.min.js"></script>

  <style>
    body {
      font-family: 'Poppins', sans-serif;
      color: #fff;
      min-height: 100vh;
      margin: 0;
      overflow-x: hidden;
      background: linear-gradient(135deg, #000 0%, #1a1a1a 45%, #000 100%);
      background-size: 400% 400%;
      animation: gradientShift 12s ease infinite;
      position: relative;
    }

    #particles-js {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      z-index: -1;
    }

    @keyframes gradientShift {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    /* HEADER */
    .header {
      text-align: center;
      padding: 80px 20px 40px;
    }

    .header img {
      width: 220px;
      height: 220px;
      border-radius: 50%;
      border: 3px solid #d4af37;
      object-fit: cover;
      box-shadow: 0 0 70px rgba(212,175,55,0.9);
      animation: glowPulse 3s ease-in-out infinite alternate;
      transition: transform 0.6s ease;
    }

    .header img:hover {
      transform: scale(1.08) rotate(3deg);
    }

    @keyframes glowPulse {
      from { box-shadow: 0 0 30px rgba(212,175,55,0.4); }
      to { box-shadow: 0 0 80px rgba(212,175,55,1); }
    }

    .header h1 {
      margin-top: 25px;
      font-weight: 700;
      font-size: 3rem;
      letter-spacing: 2px;
      background: linear-gradient(90deg, #d4af37, #ffeb8a, #d4af37);
      background-size: 200% auto;
      -webkit-background-clip: text;
      color: transparent;
      animation: goldFlow 5s linear infinite;
    }

    @keyframes goldFlow {
      to { background-position: 200% center; }
    }

    .user-info {
      font-size: 1.05rem;
      color: #ddd;
      margin-top: 10px;
    }

    .btn-outline-warning {
      border-color: #d4af37;
      color: #d4af37;
      transition: all 0.3s ease-in-out;
    }

    .btn-outline-warning:hover {
      background: #d4af37;
      color: #111;
      box-shadow: 0 0 20px rgba(212,175,55,0.7);
      transform: scale(1.05);
    }

    /* TARJETAS */
    .card {
      background: rgba(255, 255, 255, 0.05);
      border: 1px solid rgba(212,175,55,0.4);
      border-radius: 25px;
      backdrop-filter: blur(12px);
      transition: all 0.6s ease;
      padding: 40px 20px;
      text-align: center;
      color: #fff;
      position: relative;
      overflow: hidden;
      transform-style: preserve-3d;
    }

    .card::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: conic-gradient(from 180deg at 50% 50%, transparent 0deg, rgba(212,175,55,0.4) 90deg, transparent 360deg);
      animation: rotateGlow 4s linear infinite;
      opacity: 0;
      transition: opacity 0.5s;
    }

    .card:hover::before {
      opacity: 1;
    }

    @keyframes rotateGlow {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    .card:hover {
      transform: translateY(-12px) scale(1.05) rotateX(5deg);
      box-shadow: 0 0 45px rgba(212,175,55,0.6);
    }

    .card i {
      font-size: 3.5rem;
      color: #f1c40f;
      margin-bottom: 20px;
      transition: transform 0.3s ease;
    }

    .card:hover i {
      transform: scale(1.25);
    }

    .card-title {
      font-size: 1.3rem;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    /* Animación de aparición */
    .animate-up {
      opacity: 0;
      transform: translateY(30px);
      animation: fadeUp 0.8s forwards;
    }

    @keyframes fadeUp {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* FOOTER */
    .footer {
      text-align: center;
      margin-top: 70px;
      padding-bottom: 30px;
      font-size: 1rem;
      color: #d4af37;
      letter-spacing: 1px;
    }
  </style>
</head>

<body>
  <!-- Partículas doradas -->
  <div id="particles-js"></div>

  <!-- ALERTA DE BIENVENIDA -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      Swal.fire({
        title: "¡Bienvenido, <?php echo $_SESSION['nombre']; ?>!",
        html: "<b>Has ingresado al panel administrativo</b><br>Gestionalo a tu estilo",
        icon: "success",
        background: "#111",
        color: "#fff",
        confirmButtonColor: "#d4af37",
        timer: 2600,
        showConfirmButton: false
      });
    });
  </script>

  <!-- ENCABEZADO -->
  <div class="header">
    <img src="logo.png" alt="Logo del Restaurante">
    <h1>Panel Administrativo</h1>
    <p class="user-info">
      👤 <?php echo $_SESSION['nombre']; ?> — <strong><?php echo $_SESSION['rol']; ?></strong><br>
      <a href="logout.php" class="btn btn-sm btn-outline-warning mt-3">
        <i class="fa-solid fa-right-from-bracket"></i> Cerrar sesión
      </a>
    </p>
  </div>

  <!-- TARJETAS PRINCIPALES -->
  <div class="container text-center mt-4">
    <div class="row g-4 justify-content-center">
      <?php
      $items = [
        ["panel_pedidos.php", "fa-receipt", "Pedidos"],
        ["panel_clientes.php", "fa-users", "Clientes"],
        ["restaurantes.php", "fa-utensils", "Restaurantes"],
        ["panel_trabajadores.php", "fa-user-tie", "Trabajadores"],
        ["reportes.php", "fa-chart-line", "Reportes"],
        ["stock.php", "fa-bowl-food", "Stock (Platos)"]
      ];

      foreach ($items as $i => $card) {
        echo "
        <div class='col-10 col-sm-6 col-md-4 col-lg-3 animate-up' style='animation-delay:".($i*0.15)."s'>
          <a href='{$card[0]}' class='text-decoration-none text-white'>
            <div class='card'>
              <i class='fa-solid {$card[1]}'></i>
              <h5 class='card-title'>{$card[2]}</h5>
            </div>
          </a>
        </div>";
      }
      ?>
    </div>
  </div>

  <!-- FOOTER -->
  <div class="footer">
    © <?php echo date('Y'); ?> | Panel Administrativo — <strong>Administrador</strong> 🍽️
  </div>

  <script>
  /* 🎇 Configuración de partículas doradas */
  particlesJS("particles-js", {
    "particles": {
      "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
      "color": { "value": "#d4af37" },
      "shape": { "type": "circle" },
      "opacity": { "value": 0.6, "random": true },
      "size": { "value": 3, "random": true },
      "move": { "enable": true, "speed": 1.8, "direction": "none", "out_mode": "out" },
      "line_linked": { "enable": false }
    },
    "interactivity": {
      "detect_on": "canvas",
      "events": {
        "onhover": { "enable": true, "mode": "repulse" },
        "onclick": { "enable": true, "mode": "push" }
      },
      "modes": {
        "repulse": { "distance": 90, "duration": 0.4 },
        "push": { "particles_nb": 3 }
      }
    },
    "retina_detect": true
  });
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
