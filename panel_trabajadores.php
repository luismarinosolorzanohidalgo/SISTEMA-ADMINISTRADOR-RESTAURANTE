<?php
session_start();

// 🔒 Simulación de sesión (para pruebas)
if (!isset($_SESSION['nombre'])) {
    $_SESSION['nombre'] = "Luis Solórzano";
    $_SESSION['rol'] = "Trabajador";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Panel Dorado | PowerStreet</title>

<!-- Recursos -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

<style>
body {
  font-family: 'Poppins', sans-serif;
  background: radial-gradient(circle at top left, #0d0d0d, #000000, #1a1a1a);
  color: #fff;
  overflow-x: hidden;
  min-height: 100vh;
  position: relative;
}

/* Partículas doradas */
.particles {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  z-index: -1;
  overflow: hidden;
}
.particles span {
  position: absolute;
  background: #d4af37;
  border-radius: 50%;
  box-shadow: 0 0 15px #d4af37;
  opacity: 0.3;
  animation: flotar 18s linear infinite;
}
@keyframes flotar {
  0% { transform: translateY(120vh) scale(0.8); opacity: .4; }
  100% { transform: translateY(-10vh) scale(0); opacity: 0; }
}

/* Navbar */
.navbar {
  background: rgba(15, 15, 15, 0.6);
  backdrop-filter: blur(12px);
  border-bottom: 2px solid #d4af37;
  box-shadow: 0 0 30px rgba(212,175,55,0.4);
}
.navbar-brand {
  font-weight: 700;
  color: #d4af37 !important;
  display: flex;
  align-items: center;
}
.navbar-brand img {
  width: 60px;
  height: 60px;
  margin-right: 10px;
  border-radius: 50%;
  background: #111;
  box-shadow: 0 0 25px rgba(212,175,55,0.8);
  animation: brillo 2.5s infinite alternate;
}
@keyframes brillo {
  0% { box-shadow: 0 0 15px rgba(212,175,55,0.6); }
  100% { box-shadow: 0 0 35px rgba(255,215,0,0.9); }
}

/* Tarjetas doradas */
.card {
  border: 1px solid rgba(212,175,55,0.4);
  border-radius: 20px;
  background: linear-gradient(160deg, rgba(40,40,40,0.9), rgba(20,20,20,1));
  box-shadow: 0 0 20px rgba(212,175,55,0.1);
  transition: all 0.5s ease;
  position: relative;
  overflow: hidden;
}
.card::before {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: conic-gradient(from 0deg, #d4af37, transparent, #d4af37, transparent);
  animation: girar 10s linear infinite;
  opacity: 0.1;
  z-index: 0;
}
@keyframes girar { to { transform: rotate(360deg); } }

.card-content {
  position: relative;
  z-index: 2;
  padding: 30px 20px;
  text-align: center;
}
.card:hover {
  transform: translateY(-10px) scale(1.05);
  box-shadow: 0 0 35px rgba(212,175,55,0.6);
  border-color: #d4af37;
}

.logo-card {
  width: 90px;
  height: 90px;
  border-radius: 50%;
  padding: 10px;
  background: rgba(255,255,255,0.1);
  box-shadow: 0 0 20px rgba(212,175,55,0.4);
  transition: transform .5s ease, box-shadow .5s ease;
}
.card:hover .logo-card {
  transform: scale(1.1) rotate(6deg);
  box-shadow: 0 0 35px rgba(212,175,55,0.8);
}

.card h5 {
  color: #ffd700;
  font-weight: 700;
  margin-top: 15px;
}
.card p {
  color: #d6c47a;
  font-size: 0.9rem;
}

/* Botones */
.btn-animado {
  background: linear-gradient(135deg, #d4af37, #b8860b);
  color: #000;
  border: none;
  border-radius: 30px;
  padding: 10px 25px;
  transition: all 0.35s ease;
  position: relative;
  overflow: hidden;
  font-weight: 600;
}
.btn-animado:hover {
  color: #fff;
  transform: scale(1.07);
  box-shadow: 0 0 20px rgba(212,175,55,0.8);
}

/* Botón volver */
.btn-volver {
  background: linear-gradient(135deg, #d4af37, #b8860b);
  color: #000;
  border-radius: 50px;
  padding: 12px 28px;
  font-weight: 600;
  text-decoration: none;
  box-shadow: 0 0 15px rgba(212,175,55,0.6);
  transition: all 0.4s ease;
  position: fixed;
  bottom: 40px;
  left: 50%;
  transform: translateX(-50%);
  z-index: 10;
}
.btn-volver:hover {
  transform: translateX(-50%) scale(1.08);
  background: linear-gradient(135deg, #ffd700, #cfa92f);
  box-shadow: 0 0 30px rgba(212,175,55,0.9);
}

/* Footer */
footer {
  background: #0a0a0a;
  color: #d4af37;
  text-align: center;
  padding: 15px;
  border-top: 1px solid #d4af37;
  margin-top: 70px;
}
</style>
</head>

<body>
<div class="particles"></div>

<nav class="navbar navbar-expand-lg px-4">
  <a class="navbar-brand" href="#">
    <img src="logo.png" alt="Logo"> <!-- 🔸 reemplaza con tu logo -->
    PowerStreet
  </a>
  <div class="ms-auto text-warning">
    👷 <?php echo $_SESSION['nombre']; ?> • <span class="text-light"><?php echo $_SESSION['rol']; ?></span>
  </div>
</nav>

<div class="container py-5 text-center">
  <h1 class="fw-bold text-warning mb-3 animate__animated animate__fadeInDown">Panel Dorado</h1>
  <p class="text-light mb-5">Bienvenido al panel de trabajadores de PowerStreet ⚡</p>

  <div class="row justify-content-center g-4">
    <?php
    $restaurantes = [
      ["FastFeast", "Carnes y parrillas premium.", "img_restaurantes/restaurante.png", "fastfeast.php"],
      ["SushiWave", "Rolls & Sashimis fusión peruana.", "img_restaurantes/sushiwave.png", "sushiwave.php"],
      ["PastaManía", "Pasta italiana artesanal.", "img_restaurantes/pastamania.png", "pastamania.php"],
      ["El Sabor del Mar", "Ceviches y mariscos frescos.", "img_restaurantes/sabor_mar.png", "sabor_mar.php"],
      ["Dulce Tentación", "Postres y café premium.", "img_restaurantes/dulce.png", "dulce.php"]
    ];
    $delay = 0.2;
    foreach ($restaurantes as $r) {
      echo "
      <div class='col-md-4 col-lg-3 animate__animated animate__zoomIn' style='animation-delay: {$delay}s'>
        <div class='card'>
          <div class='card-content'>
            <img src='{$r[2]}' alt='{$r[0]}' class='logo-card'>
            <h5>{$r[0]}</h5>
            <p>{$r[1]}</p>
            <a href='{$r[3]}' class='btn-animado'>Ver más</a>
          </div>
        </div>
      </div>";
      $delay += 0.15;
    }
    ?>
  </div>
</div>

<a href="principal.php" class="btn-volver animate__animated animate__fadeInUp">
  <i class="fa-solid fa-arrow-left"></i>&nbsp;Volver
</a>

<footer>
  <small>© 2025 PowerStreet • Todos los derechos reservados</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  Swal.fire({
    title: '👑 Bienvenido al Panel Dorado',
    text: `Hola <?php echo $_SESSION['nombre']; ?>, tu espacio está listo.`,
    icon: 'success',
    background: '#111',
    color: '#d4af37',
    confirmButtonColor: '#d4af37',
    showClass: { popup: 'animate__animated animate__fadeInDown animate__faster' },
    hideClass: { popup: 'animate__animated animate__fadeOutUp animate__faster' }
  });

  const particlesContainer = document.querySelector('.particles');
  for (let i = 0; i < 60; i++) {
    const span = document.createElement('span');
    span.style.left = Math.random() * 100 + '%';
    span.style.width = span.style.height = Math.random() * 6 + 3 + 'px';
    span.style.animationDelay = Math.random() * 15 + 's';
    span.style.animationDuration = 10 + Math.random() * 15 + 's';
    particlesContainer.appendChild(span);
  }
});
</script>
</body>
</html>
