<?php
include 'conexion.php';
session_start();

// 🔹 Solo administradores pueden acceder
if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
    header("Location: login.php");
    exit();
}

// 🔹 Consulta de clientes
$clientes = $conn->query("SELECT * FROM clientes ORDER BY id DESC");
if (!$clientes) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>👥 Gestión de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- ====== Styles mejorados (reemplaza tu <style> existente) ====== -->
    <style>
        /* ===== Tipografía base ===== */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800&display=swap');

        :root {
            --gold-1: #ffb300;
            --gold-2: #ffca28;
            --dark-1: #2d3436;
            --glass: rgba(255, 255, 255, 0.9);
            --glass-strong: rgba(255, 255, 255, 0.98);
        }

        html,
        body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: radial-gradient(circle at 10% 10%, #fffaf0 0%, #fff3e0 30%, #fcf7f2 60%, #f3f5f9 100%);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            overflow-x: hidden !important;
            /* sin scroll lateral */
        }

        /* ===== Contenedor principal ===== */
        .container {
            margin: 48px auto;
            max-width: 1240px;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.88), rgba(255, 255, 255, 0.95));
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 50px rgba(16, 24, 40, 0.08), inset 0 1px 0 rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(6px);
            position: relative;
            z-index: 2;
            transform-origin: center;
        }

        /* entrada suave */
        .container.animate-in {
            animation: cardIn 700ms cubic-bezier(.2, .9, .2, 1) both;
        }

        @keyframes cardIn {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.995);
                filter: blur(2px);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
                filter: blur(0);
            }
        }

        h2 {
            font-size: 1.6rem;
            font-weight: 800;
            color: var(--dark-1);
            margin: 0 0 18px 0;
            letter-spacing: 0.6px;
        }

        /* ===== Top bar: búsqueda y botones ===== */
        .topbar {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .topbar-left {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .search-input {
            min-width: 260px;
            max-width: 420px;
            width: 40%;
            position: relative;
        }

        .search-input input {
            width: 100%;
            padding: 10px 38px 10px 14px;
            border-radius: 12px;
            border: 1px solid rgba(30, 30, 30, 0.06);
            background: rgba(255, 255, 255, 0.95);
            box-shadow: 0 2px 10px rgba(16, 24, 40, 0.04);
            transition: all .22s ease;
        }

        .search-input input:focus {
            outline: none;
            box-shadow: 0 6px 18px rgba(255, 179, 0, 0.12);
            border-color: var(--gold-1);
        }

        .search-input .fa-magnifying-glass {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(20, 20, 20, 0.35);
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--gold-1), var(--gold-2));
            color: #fff;
            border: none;
            padding: 10px 16px;
            border-radius: 12px;
            font-weight: 700;
            box-shadow: 0 8px 30px rgba(255, 179, 0, 0.18);
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 18px 45px rgba(255, 179, 0, 0.22);
        }

        .btn-ghost {
            background: transparent;
            border: 1px solid rgba(30, 30, 30, 0.06);
            padding: 8px 12px;
            border-radius: 10px;
            color: var(--dark-1);
            font-weight: 600;
        }

        /* ===== Tabla: glass + filas elevadas ===== */
        .table-wrap {
            margin-top: 18px;
            border-radius: 14px;
            overflow: hidden;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0.7));
            padding: 12px;
            box-shadow: 0 6px 30px rgba(16, 24, 40, 0.05);
        }

        table.custom-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            font-size: 0.95rem;
            color: #222;
        }

        table.custom-table thead th {
            background: linear-gradient(90deg, var(--dark-1), #424242);
            color: #fff;
            padding: 14px 12px;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.9px;
            border: none;
        }

        table.custom-table tbody tr {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(250, 248, 240, 0.98));
            box-shadow: 0 6px 18px rgba(16, 24, 40, 0.04);
            border-radius: 12px;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        table.custom-table tbody tr td {
            padding: 14px 12px;
            vertical-align: middle;
        }

        table.custom-table tbody tr:hover {
            transform: translateY(-4px);
            box-shadow: 0 14px 40px rgba(255, 179, 0, 0.12);
            background: linear-gradient(180deg, #fffdf6, #fff8ec);
        }

        /* ID small badge */
        .badge-id {
            background: rgba(0, 0, 0, 0.06);
            padding: 6px 10px;
            border-radius: 8px;
            font-weight: 600;
            color: #333;
            font-size: 0.85rem;
        }

        /* Acciones */
        .action-btn {
            border: none;
            padding: 8px 10px;
            margin: 0 4px;
            border-radius: 10px;
            cursor: pointer;
            transition: transform .14s ease;
            font-weight: 700;
        }

        .action-btn:hover {
            transform: translateY(-2px);
        }

        .btn-edit {
            background: linear-gradient(90deg, #46a6ff, #2e8de6);
            color: #fff;
            box-shadow: 0 8px 20px rgba(46, 141, 230, 0.12);
        }

        .btn-volver {
            background: linear-gradient(90deg, #546e7a, #455a64);
            color: white;
            border: none;
            padding: 12px 28px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(84, 110, 122, 0.35);
            transition: all 0.25s ease-in-out;
            position: relative;
            overflow: hidden;
        }

        .btn-volver::before {
            content: '';
            position: absolute;
            top: 0;
            left: -75%;
            width: 50%;
            height: 100%;
            background: rgba(255, 255, 255, 0.2);
            transform: skewX(-20deg);
            transition: all 0.6s ease;
        }

        .btn-volver:hover::before {
            left: 125%;
        }

        .btn-volver:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(84, 110, 122, 0.45);
            background: linear-gradient(90deg, #455a64, #37474f);
        }

        .btn-delete {
            background: linear-gradient(90deg, #ff6b6b, #ef5350);
            color: #fff;
            box-shadow: 0 8px 20px rgba(239, 83, 80, 0.12);
        }

        .btn-reset {
            background: linear-gradient(90deg, var(--gold-1), var(--gold-2));
            color: #fff;
            box-shadow: 0 8px 20px rgba(255, 179, 0, 0.12);
        }

        /* responsive */
        @media (max-width:900px) {
            .search-input {
                width: 55%;
                min-width: 200px;
            }

            table.custom-table thead {
                display: none;
            }

            table.custom-table tbody tr {
                display: block;
                padding: 14px;
            }

            table.custom-table tbody tr td {
                display: flex;
                justify-content: space-between;
                padding: 8px 12px;
                border-bottom: 1px dashed rgba(0, 0, 0, 0.04);
            }

            .table-wrap {
                padding: 8px;
            }
        }

        /* 🎨 Tabla moderna y animada */
        .table {
            border-collapse: separate;
            border-spacing: 0 10px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .table thead th {
            background: linear-gradient(90deg, #2d3436, #424242);
            color: #fff;
            font-weight: 600;
            padding: 15px;
            border: none;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .table tbody tr {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 18px rgba(255, 193, 7, 0.3);
            background: #fffbea;
        }

        .table td {
            padding: 12px 15px;
            border-top: none;
            vertical-align: middle;
            color: #2d3436;
            font-weight: 500;
        }

        .table td:first-child {
            border-top-left-radius: 12px;
            border-bottom-left-radius: 12px;
        }

        .table td:last-child {
            border-top-right-radius: 12px;
            border-bottom-right-radius: 12px;
        }

        /* ✨ Animación de entrada para filas */
        @keyframes fadeRow {
            from {
                opacity: 0;
                transform: translateY(15px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table tbody tr {
            animation: fadeRow 0.4s ease forwards;
        }

        /* 🔘 Botones dentro de la tabla */
        .table .btn {
            border-radius: 20px;
            font-weight: 600;
            padding: 6px 14px;
            transition: 0.3s;
        }

        .table .btn:hover {
            transform: scale(1.1);
        }

        .table .btn-warning {
            background: linear-gradient(90deg, #ffc107, #ffca28);
            color: #212121;
            border: none;
        }

        .table .btn-danger {
            background: linear-gradient(90deg, #ff5252, #ff1744);
            border: none;
        }

        .table .btn-info {
            background: linear-gradient(90deg, #00bcd4, #26c6da);
            border: none;
        }

        .table .btn-warning:hover,
        .table .btn-danger:hover,
        .table .btn-info:hover {
            filter: brightness(1.1);
            box-shadow: 0 0 8px rgba(255, 193, 7, 0.4);
        }


        /* ==== Modal styling adicional (grande y llamativo) ==== */
        .modal-lg .modal-content {
            border-radius: 18px;
            padding: 18px;
        }

        .modal-title {
            font-weight: 800;
            letter-spacing: 0.6px;
        }

        /* ===== efecto shine sutil en headers y botones ===== */
        .shine {
            position: relative;
            overflow: hidden;
        }

        .shine::after {
            content: "";
            position: absolute;
            top: -30%;
            left: -60%;
            width: 40%;
            height: 160%;
            background: linear-gradient(120deg, rgba(255, 255, 255, 0.0), rgba(255, 255, 255, 0.35), rgba(255, 255, 255, 0.0));
            transform: skewX(-20deg);
            transition: all 0.9s ease;
        }

        .btn-primary:hover.shine::after {
            left: 120%;
        }

        /* tiny utilities */
        .muted {
            color: #6b7280;
            font-size: 0.9rem;
        }

        .small {
            font-size: 0.85rem;
            color: #333;
        }
    </style>

    <!-- ===== Canvas for golden particles (place right after body open) ===== -->
    <canvas id="particles-gold" style="position:fixed;inset:0;z-index:0;pointer-events:none;"></canvas>

    <!-- ===== Scripts: particles, gsap entrance, live-search on table ===== -->
    <script>
        // ===== Particles doradas ligeras =====
        (function() {
            const canvas = document.getElementById('particles-gold');
            if (!canvas) return;
            const ctx = canvas.getContext('2d');
            let w = canvas.width = innerWidth;
            let h = canvas.height = innerHeight;
            const colors = ['#FFD54A', '#FFD700', '#FFB300', '#FFE082'];
            const particles = [];
            const N = Math.min(Math.floor(w / 8), 150);

            function rand(min, max) {
                return Math.random() * (max - min) + min;
            }

            for (let i = 0; i < N; i++) {
                particles.push({
                    x: Math.random() * w,
                    y: Math.random() * h,
                    r: rand(0.6, 3),
                    vx: rand(-0.2, 0.2),
                    vy: rand(-0.15, 0.15),
                    c: colors[Math.floor(Math.random() * colors.length)],
                    a: rand(0.35, 0.9)
                });
            }

            function resize() {
                w = canvas.width = innerWidth;
                h = canvas.height = innerHeight;
            }
            addEventListener('resize', () => {
                resize();
            });

            function frame() {
                ctx.clearRect(0, 0, w, h);
                for (const p of particles) {
                    p.x += p.vx;
                    p.y += p.vy;
                    if (p.x < -10) p.x = w + 10;
                    if (p.x > w + 10) p.x = -10;
                    if (p.y < -10) p.y = h + 10;
                    if (p.y > h + 10) p.y = -10;

                    ctx.beginPath();
                    ctx.globalAlpha = p.a;
                    ctx.fillStyle = p.c;
                    ctx.arc(p.x, p.y, p.r, 0, Math.PI * 2);
                    ctx.fill();
                }
                requestAnimationFrame(frame);
            }
            frame();
        })();

        // ===== GSAP-like simple entrance (no external lib required) =====
        (function() {
            const container = document.querySelector('.container');
            if (container) container.classList.add('animate-in');
        })();

        // ===== Live search for table rows (search in all visible columns) =====
        (function() {
            document.addEventListener('DOMContentLoaded', () => {
                const input = document.querySelector('.search-input input');
                if (!input) return;
                const tbody = document.querySelector('table.custom-table tbody');
                if (!tbody) return;
                input.addEventListener('input', (e) => {
                    const q = e.target.value.trim().toLowerCase();
                    Array.from(tbody.rows).forEach(row => {
                        const text = row.innerText.toLowerCase();
                        row.style.display = text.includes(q) ? '' : 'none';
                    });
                });
            });
        })();

        // ===== Utility: open bootstrap modal by id (used by action buttons) =====
        function openModalById(id) {
            const el = document.getElementById(id);
            if (!el) return;
            const modal = new bootstrap.Modal(el);
            modal.show();
        }

        // ===== Small helper to confirm actions with SweetAlert if available =====
        async function confirmAction(title, text, onConfirm) {
            if (window.Swal) {
                const r = await Swal.fire({
                    title: title || '¿Confirmar?',
                    text: text || '',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sí',
                    cancelButtonText: 'Cancelar'
                });
                if (r.isConfirmed && typeof onConfirm === 'function') onConfirm();
            } else {
                if (confirm(text || '¿Estás seguro?'))
                    if (typeof onConfirm === 'function') onConfirm();
            }
        }
    </script>

</head>

<body>

    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>👥 Gestión de Clientes</h2>
            <button class="btn-volver" onclick="window.location.href='panel_clientes.php'">⬅ Volver</button>
        </div>

        <div class="d-flex justify-content-end mb-3">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregar">➕ Nuevo Cliente</button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Dirección</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($c = $clientes->fetch_assoc()): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= htmlspecialchars($c['nombre']) ?></td>
                            <td><?= htmlspecialchars($c['correo']) ?></td>
                            <td><?= htmlspecialchars($c['telefono']) ?></td>
                            <td><?= htmlspecialchars($c['direccion']) ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" onclick='editarCliente(<?= json_encode($c) ?>)'>✏️</button>
                                <button class="btn btn-sm btn-danger" onclick='eliminarCliente(<?= $c["id"] ?>)'>🗑️</button>
                                <button class="btn btn-sm btn-secondary" onclick='abrirModalRestablecer(<?= $c["id"] ?>)'>🔑</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 🟡 MODAL AGREGAR CLIENTE -->
    <div class="modal fade" id="modalAgregar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="crear_cliente.php">
                <div class="modal-header">
                    <img src="restaurante.jpeg" alt="Logo">
                    <h5 class="modal-title mt-5">Agregar Cliente</h5>
                </div>
                <div class="modal-body">
                    <input class="form-control mb-3" name="nombre" placeholder="Nombre completo" required>
                    <input class="form-control mb-3" type="email" name="correo" placeholder="Correo electrónico" required>
                    <input class="form-control mb-3" name="telefono" placeholder="Teléfono" required>
                    <input class="form-control mb-3" name="direccion" placeholder="Dirección" required>
                    <input class="form-control mb-3" type="password" name="password" placeholder="Contraseña" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success w-100 py-2">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🟡 MODAL EDITAR CLIENTE -->
    <div class="modal fade" id="modalEditar" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form class="modal-content" method="POST" action="editar_cliente.php">
                <input type="hidden" name="id" id="editId">
                <div class="modal-header">
                    <img src="restaurante.jpeg" alt="Logo">
                    <h5 class="modal-title mt-5">Editar Cliente</h5>
                </div>
                <div class="modal-body">
                    <input class="form-control mb-3" id="editNombre" name="nombre" placeholder="Nombre" required>
                    <input class="form-control mb-3" id="editCorreo" name="correo" placeholder="Correo" required>
                    <input class="form-control mb-3" id="editTelefono" name="telefono" placeholder="Teléfono" required>
                    <input class="form-control mb-3" id="editDireccion" name="direccion" placeholder="Dirección" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary w-100 py-2">Actualizar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 🟡 MODAL RESTABLECER CONTRASEÑA -->
    <div class="modal fade" id="modalRestablecer" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" method="POST" action="restablecer_password_cliente.php">
                <input type="hidden" name="id" id="resetId">
                <div class="modal-header">
                    <h5 class="modal-title">Restablecer Contraseña</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input class="form-control mb-3" type="password" name="nueva_clave" placeholder="Nueva contraseña" required>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-warning w-100 py-2">Actualizar Contraseña</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editarCliente(c) {
            document.getElementById('editId').value = c.id_cliente;
            document.getElementById('editNombre').value = c.nombre;
            document.getElementById('editCorreo').value = c.correo;
            document.getElementById('editTelefono').value = c.telefono;
            document.getElementById('editDireccion').value = c.direccion;
            new bootstrap.Modal('#modalEditar').show();
        }

        function eliminarCliente(id) {
            Swal.fire({
                title: '¿Eliminar cliente?',
                text: 'Esta acción no se puede deshacer.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then(res => {
                if (res.isConfirmed) {
                    fetch('eliminar_cliente.php?id=' + id)
                        .then(() => Swal.fire('Eliminado', 'Cliente eliminado correctamente', 'success').then(() => location.reload()));
                }
            });
        }

        function abrirModalRestablecer(id) {
            document.getElementById('resetId').value = id;
            new bootstrap.Modal('#modalRestablecer').show();
        }
    </script>
</body>

</html>