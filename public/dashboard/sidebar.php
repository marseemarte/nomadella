<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
$current = basename($_SERVER['PHP_SELF']);

// Para el menú de usuarios
$usuarios_activos = [
    'clientes.php',
    'editar_usuario.php',
    'registro_empleado.user.php',
    'usuarios_desactivados.php'
];

$reservas_activos = [
    'reservas.php',
    'editar_reserva.php',
    'nueva_reserva.php',
    'cancelar_reserva.php'
];

$proveedores_activos = [
    'proveedores.php',
    'proveedor_form.php',
    'editar_proveedor.php'
];

$paquetes_activos = [
    'paquetes.php',
    'nuevo_paquete.php',
    'editar_paquete.php'
];

$destinos_activos = [
    'destinos.php',
    'nuevo_destino.php',
    'editar_destino.php'
];
if (session_status() === PHP_SESSION_NONE) session_start();

?>
<link rel="stylesheet" href="css/apartados.css">
<button id="toggleSidebar" class="btn d-md-none" style="position:fixed;top:10px;left:10px;z-index:1000;background:#b84e6f;color:white;">
  <i class="bi bi-list" style="font-size: 1.5rem;"></i>
</button>
<div class="sidebar d-flex flex-column justify-content-between">
  <div>
    <div class="text-center mb-4">
      <img src="/nomadella/img/nomadella_logotipo.png" alt="Logo Agencia" style="max-width: 80px; margin-bottom: 10px;">
      <h4 style="font-weight:bold; color:#FFF6F8;">Nomadella</h4>
      <small style="color:#6CE0B6;">Panel de Administración</small>
    </div>
    <nav class="nav flex-column">
      <a class="nav-link px-4 py-2 <?= $current == 'dashboard.php' ? 'active' : '' ?>" href="./dashboard.php">
        <i class="bi bi-bar-chart"></i> Dashboard
      </a>
      <a class="nav-link px-4 py-2 <?= in_array($current, $proveedores_activos) == 'proveedores.php' ? 'active' : '' ?>" href="./proveedores.php">
        <i class="bi bi-truck"></i> Proveedores
      </a>
      <a class="nav-link px-4 py-2 <?= in_array($current, $paquetes_activos) == 'paquetes.php' ? 'active' : '' ?>" href="./paquetes.php">
        <i class="bi bi-postcard"></i> Paquetes
      </a>
      <a class="nav-link px-4 py-2 <?= in_array($current, $destinos_activos) == 'destinos.php' ? 'active' : '' ?>" href="./destinos.php">
        <i class="bi bi-geo-alt"></i> Destinos
      </a>
      <a class="nav-link px-4 py-2 <?= in_array($current, $reservas_activos) ? 'active' : '' ?>" href="./reservas.php">
        <i class="bi bi-cart"></i> Reservas
      </a>
      <a class="nav-link px-4 py-2 <?= in_array($current, $usuarios_activos) ? 'active' : '' ?>" href="./clientes.php">
        <i class="bi bi-person"></i> Usuarios
      </a>
      <a class="nav-link px-4 py-2 <?= $current == 'finanzas.php' ? 'active' : '' ?>" href="./finanzas.php">
        <i class="bi bi-bar-chart"></i> Finanzas
      </a>
      <a class="nav-link px-4 py-2 <?= $current == 'movimientos.php' ? 'active' : '' ?>" href="./movimientos.php">
        <i class="bi bi-arrow-left-right"></i> Movimientos
      </a>
      
    </nav>
    <hr style="border-color:#FFF6F8; opacity:0.2;">
    <div class="px-3">
      <div class="mb-2" style="color:#FFF6F8; font-size:0.95em;">
        <i class="bi bi-calendar-event"></i> Hoy: <?= date('d/m/Y') ?>
      </div>
      <div class="mb-2" style="color:#FFF6F8; font-size:0.95em;">
        <i class="bi bi-person-circle"></i> Usuario: <span style="color:#6CE0B6; font-weight:bold;">Admin</span>
      </div>
    </div>
  </div>
  <div class="px-3 pb-3">
    <a href="/nomadella/public/logout.php" class="btn btn-sm w-100" style="background:#5CC7ED; color:#1A001C; font-weight:bold;">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
    <div class="text-center mt-2" style="color:#FFF6F8; font-size:0.85em; opacity:0.7;">
      &copy; <?= date('Y') ?> Nomadella
    </div>
  </div>
</div>
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggleSidebar');
    const sidebar = document.querySelector('.sidebar');

    toggleBtn.addEventListener('click', function () {
      sidebar.classList.toggle('show');
    });
  });
  const mainContent = document.querySelector('.main-content');
toggleBtn.addEventListener('click', function () {
  sidebar.classList.toggle('show');
  mainContent.classList.toggle('shifted');
});
</script>
