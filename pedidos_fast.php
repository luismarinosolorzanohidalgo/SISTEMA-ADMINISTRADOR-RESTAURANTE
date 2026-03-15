<?php
include 'conexion.php';
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'Administrador') {
  header("Location: login.php");
  exit();
}
?>
<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <title>Pedidos • PowerStreet</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <!-- Bootstrap, Icons, Animate.css, jQuery, SweetAlert2 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    :root {
      --gold-1: #ffb300;
      --gold-2: #ffca28;
      --gold-hover: #ffd54f;
      --dark: #2d3436;
      --muted: #6b7280;
      --light-bg: #fafafa;
      --white: #ffffff;
    }

    /* 🪄 Fondo animado elegante */
    body {
      font-family: 'Poppins', sans-serif;
      color: var(--dark);
      min-height: 100vh;
      background: radial-gradient(circle at top left, #fff8e1, #fff) fixed;
      overflow-x: hidden;
      position: relative;
    }

    body::before {
      content: "";
      position: fixed;
      top: 0;
      left: 0;
      width: 200%;
      height: 200%;
      background: radial-gradient(circle at 20% 20%, rgba(255, 202, 40, 0.15) 0%, transparent 60%),
        radial-gradient(circle at 80% 80%, rgba(255, 179, 0, 0.15) 0%, transparent 60%);
      animation: backgroundMove 12s ease-in-out infinite alternate;
      z-index: -1;
    }

    @keyframes backgroundMove {
      0% {
        transform: translate(0, 0) scale(1);
      }

      100% {
        transform: translate(-10%, -10%) scale(1.1);
      }
    }

    /* 🌟 Top Bar */
    .topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: 30px auto;
      padding: 16px 24px;
      background: rgba(255, 255, 255, 0.85);
      backdrop-filter: blur(12px);
      border-radius: 20px;
      box-shadow: 0 10px 28px rgba(0, 0, 0, 0.08);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .topbar::after {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.6), transparent);
      animation: shimmerBar 4s infinite;
    }

    @keyframes shimmerBar {
      0% {
        left: -100%;
      }

      100% {
        left: 100%;
      }
    }

    .topbar:hover {
      transform: translateY(-4px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }

    /* ✨ Tarjetas vidrio */
    .card-glass {
      position: relative;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.98));
      border-radius: 20px;
      border: 1px solid rgba(255, 255, 255, 0.5);
      box-shadow: 0 12px 30px rgba(16, 24, 40, 0.08);
      padding: 22px;
      overflow: hidden;
      transition: all 0.3s ease;
    }

    .card-glass:hover {
      transform: translateY(-3px);
      box-shadow: 0 18px 40px rgba(16, 24, 40, 0.1);
    }

    .card-glass::before {
      content: "";
      position: absolute;
      top: -50%;
      left: -50%;
      width: 200%;
      height: 200%;
      background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.25), transparent);
      transform: rotate(25deg);
      opacity: 0;
      transition: opacity 0.4s;
      border-radius: inherit;
    }

    .card-glass:hover::before {
      opacity: 1;
      animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
      0% {
        transform: translateX(-50%) rotate(25deg);
      }

      100% {
        transform: translateX(150%) rotate(25deg);
      }
    }

    /* 🟡 Botón dorado */
    .btn-dorado {
      background: linear-gradient(90deg, var(--gold-1), var(--gold-2));
      color: #fff;
      border: none;
      border-radius: 14px;
      padding: 10px 22px;
      font-weight: 600;
      letter-spacing: 0.3px;
      box-shadow: 0 8px 22px rgba(255, 179, 0, 0.3);
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .btn-dorado::after {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.6), transparent);
      transition: 0.6s;
    }

    .btn-dorado:hover::after {
      left: 100%;
    }

    .btn-dorado:hover {
      transform: translateY(-2px);
      box-shadow: 0 12px 30px rgba(255, 202, 40, 0.4);
    }

    /* 🔍 Filtros */
    .filters {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      align-items: center;
      margin-bottom: 18px;
    }

    .filters .form-control,
    .filters .form-select {
      min-width: 170px;
      border-radius: 12px;
      border: 1px solid #e5e7eb;
      box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.03);
      transition: all 0.3s ease;
    }

    .filters .form-control:focus,
    .filters .form-select:focus {
      border-color: var(--gold-1);
      box-shadow: 0 0 0 3px rgba(255, 179, 0, 0.25);
    }

    /* 📊 Tablas */
    .table {
      border-radius: 14px;
      overflow: hidden;
      background: #fff;
    }

    .table thead {
      background: linear-gradient(90deg, var(--gold-1), var(--gold-2));
      color: #fff;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .table tbody tr {
      transition: all 0.25s ease;
    }

    .table tbody tr:hover {
      background: #fff9e6;
      transform: scale(1.002);
      box-shadow: 0 3px 10px rgba(255, 179, 0, 0.1);
    }

    .badge-status {
      padding: 0.5rem 0.75rem;
      border-radius: 999px;
      font-weight: 600;
      font-size: 0.85rem;
      text-transform: capitalize;
    }

    /* Animación para nuevas filas */
    .new-row {
      animation: pulse 1s ease-in-out;
      border-left: 4px solid var(--gold-2);
    }

    @keyframes pulse {
      0% {
        opacity: 0.5;
        transform: translateX(-3px);
      }

      100% {
        opacity: 1;
        transform: translateX(0);
      }
    }

    /* ✨ Botones de acción */
    .action-btn {
      border-radius: 10px;
      padding: 0.4rem 0.65rem;
      font-weight: 600;
      border: none;
      transition: all 0.2s ease;
    }

    .action-btn:hover {
      transform: scale(1.08);
      box-shadow: 0 5px 18px rgba(255, 179, 0, 0.25);
    }

    /* 📱 Responsive */
    @media (max-width: 900px) {

      .filters .form-control,
      .filters .form-select {
        min-width: 130px;
      }

      .topbar {
        flex-direction: column;
        gap: 10px;
        text-align: center;
      }
    }

    /* ✨ Fondo flotante con burbujas doradas */
    .bubble {
      position: fixed;
      bottom: -100px;
      background: radial-gradient(circle, var(--gold-2) 20%, rgba(255, 255, 255, 0));
      border-radius: 50%;
      opacity: 0.25;
      animation: floatUp 18s infinite ease-in;
      z-index: -1;
    }

    @keyframes floatUp {
      0% {
        transform: translateY(0) scale(1);
      }

      100% {
        transform: translateY(-120vh) scale(1.4);
      }
    }
  </style>
</head>

<body>

  <!-- burbujas mágicas -->
  <script>
    for (let i = 0; i < 15; i++) {
      const b = document.createElement('div');
      b.classList.add('bubble');
      b.style.width = b.style.height = `${Math.random() * 80 + 30}px`;
      b.style.left = `${Math.random() * 100}vw`;
      b.style.animationDuration = `${12 + Math.random() * 10}s`;
      document.body.appendChild(b);
    }
  </script>

  <div class="container">
    <div class="topbar animate__animated animate__fadeInDown">
      <div>
        <h3 class="mb-0 fw-bold"><i class="bi bi-box-seam-fill text-warning"></i> Gestión de Pedidos</h3>
        <small class="text-muted">Panel en vivo • Actualización automática ✨</small>
      </div>
      <div>
        <button onclick="location.href='panel_pedidos.php'" class="btn btn-dorado"><i class="bi bi-arrow-left-circle me-1"></i> Volver</button>
      </div>
    </div>

    <!-- filtros -->
    <div class="card-glass animate__animated animate__fadeInUp p-3 mb-3">
      <div class="filters">
        <input id="q" class="form-control" placeholder="🔍 Buscar pedido, cliente o producto...">
        <select id="estadoFilter" class="form-select">
          <option value="">Todos los estados</option>
          <option value="Pendiente">Pendiente</option>
          <option value="En Preparación">En Preparación</option>
          <option value="En proceso">En proceso</option>
          <option value="Completado">Completado</option>
          <option value="Cancelado">Cancelado</option>
          <option value="Rechazado">Rechazado</option>
        </select>
        <input id="fechaInicio" type="date" class="form-control">
        <input id="fechaFin" type="date" class="form-control">
        <button id="limpiar" class="btn btn-outline-secondary"><i class="bi bi-eraser-fill"></i></button>
        <button id="btnRefrescar" class="btn btn-dorado"><i class="bi bi-arrow-repeat"></i></button>
      </div>
    </div>

    <!-- tabla -->
    <div class="card-glass animate__animated animate__fadeIn p-2">
      <div id="tabla-wrap" class="table-responsive">
        <div class="text-center p-4 text-muted"><i class="bi bi-hourglass-split"></i> Cargando pedidos...</div>
      </div>
    </div>
  </div>

  <!-- Modal Detalle -->
  <div class="modal fade" id="modalDetalle" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-light">
          <h5 class="modal-title">Detalle del pedido</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body" id="detalleBody" style="min-height:250px">
          <div class="text-center p-5 text-muted"><i class="bi bi-arrow-repeat animate-spin"></i> Cargando...</div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    let autoRefresh = true;
    let pollingInterval = 6000;
    let lastLoadedIds = {};

    function renderEstadoBadge(estado) {
      const map = {
        'Pendiente': 'bg-warning text-dark',
        'En Preparación': 'bg-secondary text-white',
        'En proceso': 'bg-info text-white',
        'Completado': 'bg-success text-white',
        'Cancelado': 'bg-dark text-white',
        'Rechazado': 'bg-danger text-white'
      };
      return `<span class="badge-status ${map[estado] || 'bg-secondary'}">${estado}</span>`;
    }

    function cargarTabla(extraParams = {}) {
      const q = $('#q').val();
      const estado = $('#estadoFilter').val();
      const fechaInicio = $('#fechaInicio').val();
      const fechaFin = $('#fechaFin').val();
      $.get('pedidos_data.php', {
        q,
        estado,
        fechaInicio,
        fechaFin,
        ...extraParams
      }, function(html) {
        const $wrap = $('#tabla-wrap');
        $wrap.fadeOut(150, function() {
          $wrap.html(html).fadeIn(250);
          $('#tabla-wrap tbody tr').each(function() {
            const id = $(this).data('id');
            if (id && !lastLoadedIds[id]) {
              $(this).addClass('new-row animate__animated animate__fadeInUp');
              lastLoadedIds[id] = true;
            }
          });
        });
      }).fail(() => {
        $('#tabla-wrap').html('<div class="p-4 text-danger text-center"><i class="bi bi-exclamation-triangle"></i> Error al cargar pedidos.</div>');
      });
    }

    function cambiarEstado(pedidoId, nuevoEstado, btn) {
      const tipo = nuevoEstado === 'Completado' ? 'success' : 'error';
      Swal.fire({
        title: tipo === 'success' ? '¿Completar pedido?' : '¿Rechazar pedido?',
        text: tipo === 'success' ? 'El pedido pasará a completado.' : 'Esta acción no se puede deshacer.',
        icon: tipo,
        showCancelButton: true,
        confirmButtonText: 'Sí, confirmar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: tipo === 'success' ? '#28a745' : '#d33',
        background: '#fff8e1'
      }).then((res) => {
        if (!res.isConfirmed) return;
        $(btn).prop('disabled', true).addClass('opacity-75');
        $.post('pedidos_estado.php', {
          id: pedidoId,
          estado: nuevoEstado
        }, () => {
          Swal.fire({
            icon: 'success',
            title: 'Actualizado ✅',
            timer: 1300,
            showConfirmButton: false
          });
          actualizarFila(pedidoId);
        }).fail(() => {
          Swal.fire({
            icon: 'error',
            title: 'Error al actualizar'
          });
          $(btn).prop('disabled', false).removeClass('opacity-75');
        });
      });
    }

  

    function verDetalle(id) {
      const modal = new bootstrap.Modal('#modalDetalle');
      $('#detalleBody').html('<div class="text-center p-5 text-muted"><i class="bi bi-arrow-repeat animate-spin"></i> Cargando...</div>');
      modal.show();
      $.get('pedido_detalle.php', {
          id
        }, html => $('#detalleBody').html(html))
        .fail(() => $('#detalleBody').html('<div class="p-4 text-danger">Error al cargar detalle.</div>'));
    }
    cargarTabla();
    setInterval(function() {
      if (autoRefresh) cargarTabla();
    }, pollingInterval);
    // 🔄 Eventos de filtros y botones
    $('#q, #estadoFilter, #fechaInicio, #fechaFin').on('change keyup', function() {
      cargarTabla();
    });

    $('#limpiar').on('click', function() {
      $('#q').val('');
      $('#estadoFilter').val('');
      $('#fechaInicio').val('');
      $('#fechaFin').val('');
      cargarTabla();
    });

    $('#btnRefrescar').on('click', function() {
      $(this).addClass('animate__rotateIn');
      setTimeout(() => $(this).removeClass('animate__rotateIn'), 800);
      cargarTabla();
    });

    // 💫 Animación de carga suave
    $(window).on('load', function() {
      $('body').css('opacity', '1').hide().fadeIn(600);
    });
  </script>
</body>

</html>