<?php
session_start();
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $correo   = trim($_POST['correo']);
  $password = trim($_POST['password']);

  $sql = "SELECT * FROM trabajadores WHERE correo = ?";
  $stmt = $conn->prepare($sql);
  $stmt->bind_param("s", $correo);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($row = $result->fetch_assoc()) {
    if ($row['password'] === md5($password) || password_verify($password, $row['password'])) {
      if ($row['rol'] === "Administrador") {

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['nombre']  = $row['nombre'];
        $_SESSION['rol']     = $row['rol'];
        $_SESSION['sede']    = $row['sede'];

        header("Location: principal.php");
        exit;
      } else {
        $error = "Acceso denegado. Solo administradores.";
      }
    } else {
      $error = "Contraseña incorrecta.";
    }
  } else {
    $error = "Usuario no encontrado.";
  }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Acceso | Panel Administrativo</title>

  <!-- Bootstrap / Iconos -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

  <!-- Librerías -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

  <!-- ======================== ESTILO LIQUID GLASS LIGHT ======================== -->
  <style>
    :root {
      --primary: #4ac4ff;          /* Celeste neon suave */
      --accent: #ffdb70;           /* Amarillo pastel */
      --glass: rgba(255, 255, 255, 0.25);
    }

    body {
      background: radial-gradient(circle at 50% 50%, #dff7ff, #c4e1ff, #9fc6ff);
      height: 100vh;
      color: #1a1a1a;
      font-family: 'Poppins', sans-serif;
      overflow: hidden;
    }

    /* Loader */
    #loader {
      position: fixed;
      inset: 0;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      background: #ffffff;
      color: var(--primary);
      font-weight: 600;
      letter-spacing: 1px;
      z-index: 9999;
    }

    #loader .ring {
      width: 95px;
      height: 95px;
      border: 6px solid rgba(74,196,255,0.3);
      border-top: 6px solid var(--primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 14px;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    /* Fondo partículas */
    canvas#bg {
      position: fixed;
      inset: 0;
      z-index: -1;
    }

    /* Tarjeta */
    .login-card {
      width: 520px;
      background: var(--glass);
      backdrop-filter: blur(30px) saturate(180%);
      border-radius: 25px;
      padding: 55px 50px;
      border: 1px solid rgba(255, 255, 255, 0.35);
      box-shadow: 0 0 55px rgba(74,196,255,0.4);
      text-align: center;
      opacity: 0;
      transform: translateY(30px);
      animation: fadeIn 1.5s ease forwards;
    }

    @keyframes fadeIn {
      to { opacity: 1; transform: translateY(0); }
    }

    /* Logo */
    .logo {
      width: 140px;
      height: 140px;
      margin: 0 auto 30px;
      border-radius: 50%;
      background: radial-gradient(circle, #ffffff, #e8f8ff);
      display: flex;
      align-items: center;
      justify-content: center;
      border: 3px solid var(--primary);
      box-shadow: 0 0 25px rgba(74,196,255,0.6);
      animation: pulseLogo 3.5s ease-in-out infinite;
    }

    @keyframes pulseLogo {
      0%, 100% { transform: scale(1); box-shadow: 0 0 25px rgba(74,196,255,0.6); }
      50% { transform: scale(1.06); box-shadow: 0 0 45px rgba(255,219,112,0.7); }
    }

    .logo img {
      width: 100px;
      height: 100px;
      border-radius: 50%;
      object-fit: contain;
    }

    h4 {
      font-weight: 700;
      text-transform: uppercase;
      color: #2a2a2a;
      letter-spacing: 1px;
      margin-bottom: 30px;
      text-shadow: 0 0 10px rgba(74,196,255,0.4);
    }

    .form-control {
      border-radius: 10px;
      background: rgba(255, 255, 255, 0.55);
      border: 1px solid rgba(0,0,0,0.15);
      color: #2a2a2a;
      padding: 12px;
      transition: all .3s ease;
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 15px var(--primary);
      background: rgba(255, 255, 255, 0.75);
    }

    .btn-login {
      border-radius: 10px;
      background: linear-gradient(135deg, var(--primary), var(--accent));
      border: none;
      color: #000;
      font-weight: 700;
      padding: 13px;
      text-transform: uppercase;
      letter-spacing: 1px;
      box-shadow: 0 0 25px rgba(74,196,255,0.5);
      transition: all .3s ease;
    }

    .btn-login:hover {
      background: linear-gradient(135deg, var(--accent), var(--primary));
      transform: scale(1.05);
      box-shadow: 0 0 40px rgba(74,196,255,0.8);
    }

    .footer-text {
      margin-top: 25px;
      color: #333;
      font-size: 14px;
    }

    .footer-text a {
      color: var(--primary);
      text-decoration: none;
      font-weight: 600;
    }

    .footer-text a:hover {
      color: #007bff;
    }
  </style>
</head>

<body>

  <!-- Loader -->
  <div id="loader">
    <div class="ring"></div>
    Iniciando Sistema...
  </div>

  <!-- Partículas -->
  <canvas id="bg"></canvas>

  <!-- Caja del login -->
  <div class="d-flex align-items-center justify-content-center vh-100">
    <div class="login-card" id="loginBox">

      <div class="logo">
        <img src="logo.png" alt="Logo del sistema">
      </div>

      <h4>Acceso al Panel Administrativo</h4>

      <form method="POST">
        <div class="mb-3">
          <input type="email" name="correo" class="form-control" placeholder="Correo electrónico" required>
        </div>
        <div class="mb-3">
          <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
        </div>

        <button type="submit" class="btn btn-login w-100">
          Ingresar <i class="fa-solid fa-right-to-bracket ms-1"></i>
        </button>
      </form>

      <div class="footer-text mt-3">
        ¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a>
      </div>

      <div class="footer-text mt-2">
        © 2025 • Sistema Administrativo • <strong>ADMINISTRADOR</strong>
      </div>

    </div>
  </div>


  <!-- ====================== JAVASCRIPT ====================== -->
  <script>
    /* Loader animado */
    window.addEventListener("load", () => {
      gsap.to("#loader", { opacity: 0, duration: 1, onComplete: () => {
        document.getElementById("loader").style.display = "none";
      }});
      gsap.to("#loginBox", { opacity: 1, y: 0, duration: 1.2, ease: "power2.out" });
    });

    /* Fondo partículas */
    const canvas = document.getElementById("bg");
    const ctx = canvas.getContext("2d");

    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const particles = Array.from({ length: 120 }, () => ({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      r: Math.random() * 2 + 1,
      dx: (Math.random() - 0.5) * 0.6,
      dy: (Math.random() - 0.5) * 0.6,
      color: Math.random() > 0.5 ? "rgba(74,196,255,0.8)" : "rgba(255,219,112,0.7)"
    }));

    function animate() {
      ctx.clearRect(0, 0, canvas.width, canvas.height);

      particles.forEach(p => {
        ctx.beginPath();
        ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
        ctx.fillStyle = p.color;
        ctx.fill();

        p.x += p.dx;
        p.y += p.dy;

        if (p.x < 0 || p.x > canvas.width) p.dx *= -1;
        if (p.y < 0 || p.y > canvas.height) p.dy *= -1;
      });

      requestAnimationFrame(animate);
    }

    animate();

    window.addEventListener("resize", () => {
      canvas.width = window.innerWidth;
      canvas.height = window.innerHeight;
    });

    /* Error de login */
    <?php if (!empty($error)): ?>
      Swal.fire({
        icon: "error",
        title: "Acceso denegado",
        text: "<?php echo $error; ?>",
        confirmButtonColor: "#4ac4ff",
        background: "#ffffff",
        color: "#1a1a1a"
      });
    <?php endif; ?>
  </script>

</body>
</html>
