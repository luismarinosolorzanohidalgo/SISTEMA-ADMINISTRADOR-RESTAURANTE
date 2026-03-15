<?php
session_start();
include "conexion.php";

// Sanitización
function limpiar($data)
{
  return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Token CSRF
if (empty($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Obtener roles
$roles = [];
$res = $conn->query("SELECT DISTINCT rol FROM trabajadores WHERE rol IS NOT NULL AND rol != '' ORDER BY rol ASC");
if ($res && $res->num_rows > 0) {
  while ($r = $res->fetch_assoc()) {
    $roles[] = $r['rol'];
  }
}

$msg = $msg_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) die("CSRF inválido.");

  $nombre = limpiar($_POST['nombre']);
  $correo = limpiar($_POST['correo']);
  $password = $_POST['password'];
  $rol = limpiar($_POST['rol']);
  $direccion = limpiar($_POST['direccion']);

  if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    $msg = "Correo no válido.";
    $msg_type = "error";
  } elseif (strlen($password) < 6) {
    $msg = "La contraseña debe tener al menos 6 caracteres.";
    $msg_type = "error";
  } else {
    $check = $conn->prepare("SELECT id FROM trabajadores WHERE correo=?");
    $check->bind_param("s", $correo);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $msg = "Este correo ya está registrado.";
      $msg_type = "error";
    } else {
      $hash = password_hash($password, PASSWORD_DEFAULT);
      $stmt = $conn->prepare("INSERT INTO trabajadores (nombre, correo, password, rol, direccion) VALUES (?,?,?,?,?)");
      $stmt->bind_param("sssss", $nombre, $correo, $hash, $rol, $direccion);

      if ($stmt->execute()) {
        $msg = "Usuario registrado correctamente.";
        $msg_type = "success";
      } else {
        $msg = "Error al registrar: " . $conn->error;
        $msg_type = "error";
      }
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Registro | Sistema Administrativo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js"></script>

  <style>
    :root {
      --primary: #5ac8fa;
      /* Celeste brillante */
      --accent: #a0e9ff;
      /* Celeste pastel */
      --highlight: #ffffff;
      /* Blanco */
      --glass: rgba(255, 255, 255, 0.25);
      /* Vidrio claro */
      --glass-border: rgba(255, 255, 255, 0.45);
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      background: radial-gradient(circle at 40% 20%, #dff8ff, #cfe7ff, #b2d4ff);
      font-family: 'Poppins', sans-serif;
      color: #003049;
      height: 100vh;
      overflow: hidden;
    }

    canvas#bg {
      position: fixed;
      inset: 0;
      z-index: -1;
    }

    /* Loader */
    #loader {
      position: fixed;
      inset: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      flex-direction: column;
      background: linear-gradient(135deg, #e0f7ff, #f2fbff);
      color: #0077b6;
      font-weight: 700;
      z-index: 9999;
    }

    #loader .ring {
      width: 85px;
      height: 85px;
      border: 6px solid rgba(90, 200, 250, 0.25);
      border-top: 6px solid var(--primary);
      border-radius: 50%;
      animation: spin 1s linear infinite;
      margin-bottom: 12px;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    /* Tarjeta de registro */
    .register-card {
      width: 620px;
      background: var(--glass);
      backdrop-filter: blur(20px);
      border-radius: 25px;
      padding: 45px;
      text-align: center;
      border: 2px solid var(--glass-border);
      box-shadow: 0 0 55px rgba(255, 255, 255, 0.8);
      transform: translateY(30px);
      opacity: 0;
      animation: fadeIn 1.2s ease forwards;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Logo */
    .logo {
      width: 130px;
      height: 130px;
      border-radius: 50%;
      margin: 0 auto 25px;
      background: rgba(255, 255, 255, 0.6);
      border: 3px solid var(--highlight);
      box-shadow: 0 0 25px rgba(255, 255, 255, 0.85);
      display: flex;
      justify-content: center;
      align-items: center;
      animation: pulseLogo 3s ease-in-out infinite;
      backdrop-filter: blur(10px);
    }

    @keyframes pulseLogo {

      0%,
      100% {
        transform: scale(1);
        opacity: 1;
      }

      50% {
        transform: scale(1.05);
        opacity: 0.85;
      }
    }

    .logo img {
      width: 90px;
      height: 90px;
      border-radius: 50%;
      object-fit: contain;
    }

    h3 {
      color: #0077b6;
      font-weight: 700;
      text-transform: uppercase;
      text-shadow: 0 0 10px rgba(0, 119, 182, 0.4);
      margin-bottom: 30px;
    }

    label {
      font-weight: 600;
      color: #023e8a;
      margin-bottom: 6px;
    }

    .form-control {
      border-radius: 12px;
      background: rgba(255, 255, 255, 0.55);
      border: 1px solid rgba(255, 255, 255, 0.7);
      color: #023e8a;
      padding: 12px;
      font-size: 15px;
      transition: all 0.3s ease;
      backdrop-filter: blur(8px);
    }

    .form-control:focus {
      border-color: var(--primary);
      box-shadow: 0 0 14px rgba(90, 200, 250, 0.7);
      background: rgba(255, 255, 255, 0.75);
    }

    .btn-register {
      border: none;
      border-radius: 12px;
      background: linear-gradient(135deg, #5ac8fa, #a0e9ff);
      padding: 14px;
      color: #003049;
      font-weight: 700;
      text-transform: uppercase;
      box-shadow: 0 0 25px rgba(160, 233, 255, 0.9);
      transition: all .3s ease;
    }

    .btn-register:hover {
      transform: scale(1.05);
      background: linear-gradient(135deg, #a0e9ff, #5ac8fa);
      color: #001d3d;
      box-shadow: 0 0 35px rgba(90, 200, 250, 1);
    }

    .footer-text a {
      color: #0077b6;
      text-decoration: none;
      font-weight: 600;
    }

    .footer-text a:hover {
      color: #00b4d8;
    }

    .btn-volver {
      position: fixed;
      bottom: 25px;
      left: 25px;
      background: linear-gradient(135deg, #5ac8fa, #a0e9ff);
      border: none;
      border-radius: 50px;
      padding: 12px 25px;
      font-weight: 600;
      color: #003049;
      box-shadow: 0 0 20px rgba(160, 233, 255, 0.9);
      transition: all .3s ease;
    }

    .btn-volver:hover {
      transform: scale(1.12);
      box-shadow: 0 0 35px rgba(90, 200, 250, 1);
    }
  </style>

</head>

<body>
  <div id="loader">
    <div class="ring"></div>
    Cargando registro...
  </div>

  <canvas id="bg"></canvas>

  <div class="d-flex align-items-center justify-content-center vh-100">
    <div class="register-card" id="registerBox">
      <div class="logo"><img src="logo.png" alt="Logo"></div>
      <h3>Registro de Trabajador</h3>

      <form method="POST" onsubmit="return validarFormulario();">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

        <div class="mb-3 text-start">
          <label>Nombre completo</label>
          <input type="text" name="nombre" class="form-control" required>
        </div>

        <div class="mb-3 text-start">
          <label>Correo electrónico</label>
          <input type="email" name="correo" class="form-control" required>
        </div>

        <div class="mb-3 text-start">
          <label>Contraseña</label>
          <input type="password" id="password" name="password" class="form-control" required>
        </div>

        <div class="mb-3 text-start">
          <label>Rol</label>
          <select name="rol" class="form-control" required>
            <option value="">Selecciona un rol</option>
            <?php foreach ($roles as $rol): ?>
              <option value="<?= htmlspecialchars($rol) ?>"><?= ucfirst(htmlspecialchars($rol)) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="mb-3 text-start">
          <label>Dirección</label>
          <input type="text" name="direccion" class="form-control" required>
        </div>

        <button type="submit" class="btn-register w-100 mt-3">
          <i class="fa-solid fa-user-plus me-1"></i> Registrar
        </button>

        <div class="footer-text mt-3">
          ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </div>
      </form>
    </div>
  </div>

  <a href="login.php" class="btn-volver"><i class="fa-solid fa-arrow-left me-1"></i> Volver</a>

  <script>
    // Loader + animación entrada
    window.addEventListener("load", () => {
      gsap.to("#loader", {
        opacity: 0,
        duration: 1,
        onComplete: () => document.getElementById("loader").style.display = "none"
      });
      gsap.to("#registerBox", {
        opacity: 1,
        y: 0,
        duration: 1.2,
        ease: "power2.out"
      });
    });

    // Fondo animado partículas doradas-celestes
    const canvas = document.getElementById("bg");
    const ctx = canvas.getContext("2d");
    canvas.width = window.innerWidth;
    canvas.height = window.innerHeight;

    const particles = Array.from({
      length: 120
    }, () => ({
      x: Math.random() * canvas.width,
      y: Math.random() * canvas.height,
      r: Math.random() * 2 + 1,
      dx: (Math.random() - .5) * 0.6,
      dy: (Math.random() - .5) * 0.6,
      color: Math.random() > .5 ? "rgba(228,181,68,0.8)" : "rgba(0,188,212,0.8)"
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

    // Validación
    function validarFormulario() {
      const pass = document.getElementById("password").value;
      if (pass.length < 6) {
        Swal.fire({
          icon: "warning",
          title: "Contraseña débil",
          text: "Debe tener al menos 6 caracteres.",
          confirmButtonColor: "#e4b544",
          background: "#0a0e1a",
          color: "#fff"
        });
        return false;
      }
      return true;
    }

    <?php if (!empty($msg)): ?>
      Swal.fire({
        icon: "<?= $msg_type ?>",
        title: "<?= ucfirst($msg_type) ?>",
        text: "<?= $msg ?>",
        confirmButtonColor: "#e4b544",
        background: "#0a0e1a",
        color: "#fff"
      });
    <?php endif; ?>
  </script>
</body>

</html>