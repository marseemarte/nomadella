<?php
date_default_timezone_set('America/Argentina/Buenos_Aires');
$current = basename($_SERVER['PHP_SELF']);

// Para el menú de usuarios
$usuarios_activos = [
    'clientes.php',
    'editar_usuario.php',
    'registro_empleado.php',
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

?>
<link rel="stylesheet" href="/nomadella/css/apartados.css">
<div class="sidebar d-flex flex-column justify-content-between">
  <div>
    <div class="text-center mb-4">
      <img src="/nomadella/img/nomadella_logotipo.png" alt="Logo Agencia" style="max-width: 80px; margin-bottom: 10px;">
      <h4 style="font-weight:bold; color:#FFF6F8;">Nomadella</h4>
      <small style="color:#6CE0B6;">Panel de Administración</small>
    </div>
    <nav class="nav flex-column">
      <a class="nav-link px-4 py-2 <?= $current == 'dashboard.php' ? 'active' : '' ?>" href="./dashboard.php">
        <i class="bi bi-house-door"></i> Dashboard
      </a>
      <a class="nav-link px-4 py-2 <?= in_array($current, $proveedores_activos) == 'proveedores.php' ? 'active' : '' ?>" href="./proveedores.php">
        <i class="bi bi-truck"></i> Proveedores
      </a>
      <a class="nav-link px-4 py-2 <?= in_array($current, $paquetes_activos) == 'paquetes.php' ? 'active' : '' ?>" href="./paquetes.php">
        <i class="bi bi-postcard"></i> Paquetes
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