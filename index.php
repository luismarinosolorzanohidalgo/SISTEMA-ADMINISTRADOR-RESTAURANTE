<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Administración</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  <style>
    body {
      background: linear-gradient(135deg, #000000, #1a1a1a);
      color: #f5f5f5;
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      overflow-x: hidden;
    }

    .navbar {
      background-color: #111;
      box-shadow: 0 4px 15px rgba(255, 215, 0, 0.2);
    }

    .navbar-brand {
      color: #FFD700 !important;
      font-weight: 700;
      letter-spacing: 1px;
    }

    .container-panel {
      margin-top: 60px;
    }

    .card {
      background: #111;
      border: 1px solid #FFD700;
      border-radius: 15px;
      transition: all 0.3s ease;
      color: white;
      box-shadow: 0 0 15px rgba(255, 215, 0, 0.1);
    }

    .card:hover {
      transform: translateY(-6px) scale(1.03);
      box-shadow: 0 0 25px rgba(255, 215, 0, 0.5);
    }

    .card i {
      font-size: 40px;
      color: #FFD700;
      margin-bottom: 10px;
    }

    .card-title {
      color: #FFD700;
      font-weight: bold;
      font-size: 20px;
    }

    footer {
      margin-top: 50px;
      text-align: center;
      color: #aaa;
      padding: 20px 0;
    }

    /* Animación suave al aparecer */
    .fade-in {
      opacity: 0;
      transform: scale(0.95);
      animation: fadeZoomIn 0.8s ease forwards;
    }

    @keyframes fadeZoomIn {
      0% {
        opacity: 0;
        transform: scale(0.9);
      }
      100% {
        opacity: 1;
        transform: scale(1);
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container">
      <a class="navbar-brand" href="#"><i class="fas fa-crown me-2"></i>Panel Admin</a>
    </div>
  </nav>

  <!-- Contenido principal -->
  <div class="container container-panel">
    <div class="text-center mb-5 animate__animated animate__fadeInDown">
      <h2 class="fw-bold text-warning">Bienvenido al Panel de Administración</h2>
      <p class="text-secondary">Selecciona una opción para comenzar</p>
    </div>

    <div class="row g-4 justify-content-center">
      <div class="col-12 col-sm-6 col-md-4 col-lg-3 fade-in" style="animation-delay:0.1s">
        <div class="card text-center p-4">
          <i class="fa-solid fa-box"></i>
          <h5 class="card-title">Productos</h5>
          <p class="card-text">Gestiona los productos disponibles.</p>
          <a href="productos.php" class="btn btn-outline-warning">Ir</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 fade-in" style="animation-delay:0.2s">
        <div class="card text-center p-4">
          <i class="fa-solid fa-users"></i>
          <h5 class="card-title">Usuarios</h5>
          <p class="card-text">Administra los usuarios registrados.</p>
          <a href="usuarios.php" class="btn btn-outline-warning">Ir</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 fade-in" style="animation-delay:0.3s">
        <div class="card text-center p-4">
          <i class="fa-solid fa-chart-line"></i>
          <h5 class="card-title">Reportes</h5>
          <p class="card-text">Visualiza estadísticas y reportes.</p>
          <a href="reportes.php" class="btn btn-outline-warning">Ir</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 fade-in" style="animation-delay:0.4s">
        <div class="card text-center p-4">
          <i class="fa-solid fa-truck"></i>
          <h5 class="card-title">Pedidos</h5>
          <p class="card-text">Gestiona los pedidos realizados.</p>
          <a href="pedidos.php" class="btn btn-outline-warning">Ir</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 fade-in" style="animation-delay:0.5s">
        <div class="card text-center p-4">
          <i class="fa-solid fa-percent"></i>
          <h5 class="card-title">Promociones</h5>
          <p class="card-text">Crea y controla promociones activas.</p>
          <a href="promociones.php" class="btn btn-outline-warning">Ir</a>
        </div>
      </div>

      <div class="col-12 col-sm-6 col-md-4 col-lg-3 fade-in" style="animation-delay:0.6s">
        <div class="card text-center p-4">
          <i class="fa-solid fa-store"></i>
          <h5 class="card-title">Tiendas</h5>
          <p class="card-text">Administra las sucursales disponibles.</p>
          <a href="tiendas.php" class="btn btn-outline-warning">Ir</a>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer>
    <p>© 2025 Panel Admin - PowerStreet</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Hace que las tarjetas aparezcan progresivamente al cargar
    document.addEventListener("DOMContentLoaded", () => {
      document.querySelectorAll('.fade-in').forEach((el, index) => {
        el.style.animationDelay = `${index * 0.1}s`;
      });
    });
  </script>
</body>
</html>
