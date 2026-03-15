<?php
session_start();

// 🧩 Simulación de sesión (para pruebas)
if (!isset($_SESSION['nombre'])) {
  $_SESSION['nombre'] = "Luis Solórzano";
  $_SESSION['rol'] = "Ventas";
}

// 🪄 SweetAlert de bienvenida
echo "
<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
<script>
  document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({
      title: '¡Bienvenido al Panel de Pedidos!',
      text: 'Hola " . $_SESSION['nombre'] . ", gestiona tus pedidos con estilo ✨',
      icon: 'success',
      confirmButtonColor: '#d4af37',
      background: '#0d0d0d',
      color: '#fff',
      timer: 2000,
      showConfirmButton: false
    });
  });
</script>
";
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Panel de Pedidos</title>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Iconos y Animaciones -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

  <style>
    :root {
      --gold: #d4af37;
      --gold-light: #ffcc4d;
      --black: #0d0d0d;
      --white: #fff;
    }

    body {
      font-family: 'Poppins', sans-serif;
      background: radial-gradient(circle at center, #111 0%, #000 100%);
      color: var(--white);
      overflow-x: hidden;
      min-height: 100vh;
    }

    /* LOGO ENCABEZADO */
    .header {
      text-align: center;
      margin-top: 60px;
    }

    .header img {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      border: 3px solid var(--gold);
      box-shadow: 0 0 40px rgba(212, 175, 55, 0.8);
      animation: glow 3s infinite alternate;
      object-fit: contain;
    }

    @keyframes glow {
      from { box-shadow: 0 0 15px rgba(212, 175, 55, 0.3); }
      to { box-shadow: 0 0 45px rgba(212, 175, 55, 0.8); }
    }

    .header h1 {
      color: var(--gold);
      font-weight: 700;
      margin-top: 20px;
      text-shadow: 0 0 15px rgba(212,175,55,0.6);
    }

    .header p {
      color: #ccc;
      margin-top: 5px;
      font-size: 1rem;
    }

    /* TARJETAS DE RESTAURANTES */
    .card {
      border: 1px solid rgba(212,175,55,0.4);
      border-radius: 18px;
      background: rgba(255, 255, 255, 0.05);
      box-shadow: 0 8px 25px rgba(0,0,0,0.3);
      transition: all 0.4s ease;
      text-align: center;
      padding: 25px;
      backdrop-filter: blur(10px);
    }

    .card:hover {
      transform: translateY(-8px) scale(1.03);
      box-shadow: 0 0 25px rgba(212,175,55,0.6);
    }

    .card h5 {
      color: var(--gold);
      font-weight: 700;
      margin-bottom: 8px;
    }

    .card p {
      color: #bbb;
      font-size: 0.95rem;
      margin-bottom: 15px;
    }

    .card img {
      width: 80px;
      height: 80px;
      border-radius: 50%;
      border: 2px solid var(--gold);
      object-fit: cover;
      margin-bottom: 10px;
      box-shadow: 0 0 15px rgba(212,175,55,0.5);
      transition: transform 0.3s ease;
    }

    .card img:hover {
      transform: scale(1.08);
    }

    .btn-gestion {
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      color: #000;
      border: none;
      border-radius: 30px;
      padding: 10px 22px;
      transition: all 0.3s ease;
      font-weight: 600;
      letter-spacing: 0.5px;
    }

    .btn-gestion:hover {
      transform: scale(1.08);
      box-shadow: 0 0 25px rgba(212, 175, 55, 0.8);
    }

    /* BOTÓN VOLVER */
    .btn-volver {
      display: inline-block;
      background: linear-gradient(135deg, var(--gold), var(--gold-light));
      color: #000;
      font-weight: 600;
      padding: 12px 26px;
      border-radius: 40px;
      text-decoration: none;
      margin: 50px auto;
      display: block;
      width: fit-content;
      box-shadow: 0 0 20px rgba(212,175,55,0.5);
      transition: all 0.4s ease;
    }

    .btn-volver:hover {
      transform: scale(1.05);
      box-shadow: 0 0 30px rgba(255,204,0,0.7);
    }

    footer {
      text-align: center;
      padding: 15px;
      background: #000;
      color: var(--gold);
      border-top: 2px solid var(--gold);
      margin-top: 40px;
      font-size: 0.9rem;
    }
  </style>
</head>

<body>

  <!-- ENCABEZADO CON LOGO -->
  <div class="header animate__animated animate__fadeInDown">
    <img src="logo.png" alt="Logo Administración">
    <h1>Panel de Pedidos</h1>
    <p>👤 <?php echo $_SESSION['nombre']; ?> — <strong style="color: var(--gold);"><?php echo $_SESSION['rol']; ?></strong></p>
  </div>

  <!-- CONTENIDO PRINCIPAL -->
  <div class="container mt-5">
    <div class="row justify-content-center g-4">

      <?php
      // nombre, descripción, logo, enlace
      $restaurantes = [
        ["FastFeast", "Pedidos de carnes y parrillas gourmet.", "img_restaurantes/fastfeast.png", "pedidos_fast.php"],
        ["SushiWave", "Pedidos de sushi fresco y combinaciones.", "img_restaurantes/sushiwave.png", "pedidos_sushi.php"],
        ["PastaManía", "Pedidos de pasta artesanal italiana.", "img_restaurantes/pastamania.png", "pedidos_pasta.php"],
        ["El Sabor del Mar", "Pedidos de ceviches y mariscos del día.", "img_restaurantes/mar.png", "pedidos_mar.php"],
        ["Dulce Tentación", "Pedidos de postres y café premium.", "img_restaurantes/dulce.png", "pedidos_dulce.php"]
      ];

      $delay = 0.2;
      foreach ($restaurantes as $r) {
        $nombre = $r[0];
        $desc = $r[1];
        $logo = !empty($r[2]) ? $r[2] : "img_restaurantes/default.png";
        $link = $r[3];
        echo "
        <div class='col-md-5 col-lg-4 animate__animated animate__zoomIn' style='animation-delay: {$delay}s'>
          <div class='card'>
            <img src='{$logo}' alt='Logo {$nombre}'>
            <h5>{$nombre}</h5>
            <p>{$desc}</p>
            <button class='btn-gestion' onclick=\"redirigir('{$link}')\">Ver pedidos</button>
          </div>
        </div>";
        $delay += 0.1;
      }
      ?>
    </div>

    <a href='principal.php' class='btn-volver'><i class="fa-solid fa-arrow-left"></i> Volver al Inicio</a>
  </div>

  <footer>© 2025 Administración de Pedidos — Todos los derechos reservados.</footer>

  <!-- SweetAlert Redirección -->
  <script>
    function redirigir(url) {
      Swal.fire({
        title: "Redirigiendo...",
        text: "Por favor espere un momento",
        icon: "info",
        background: "#000",
        color: "#fff",
        showConfirmButton: false,
        timer: 1500,
        didOpen: () => Swal.showLoading()
      });
      setTimeout(() => { window.location.href = url; }, 1600);
    }
  </script>

</body>
</html>
