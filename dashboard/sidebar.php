<link rel="stylesheet" href="/nomadella/css/apartados.css">
<div class="sidebar d-flex flex-column justify-content-between">
  <div>
    <div class="text-center mb-4">
      <img src="/nomadella/img/nomadella_logotipo.png" alt="Logo Agencia" style="max-width: 80px; margin-bottom: 10px;">
      <h4 style="font-weight:bold; color:#FFF6F8;">Nomadella</h4>
      <small style="color:#6CE0B6;">Panel de Administración</small>
    </div>
    <nav class="nav flex-column">
      <a class="nav-link px-4 py-2 " id="<?php if (!isset($_GET['active']) || $_GET['active'] == 1) echo 'active'; ?>" href="./dashboard.php">
        <i class="bi bi-house-door"></i> Dashboard
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 2) echo 'active'; ?>" href="./proveedores.php">
        <i class="bi bi-truck"></i> Proveedores
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 7) echo 'active'; ?>" href="./paquetes.php">
        <i class="bi bi-postcard"></i> Paquetes
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 3) echo 'active'; ?>" href="./reservas.php">
        <i class="bi bi-cart"></i> Reservas
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 4) echo 'active'; ?>" href="./clientes.php">
        <i class="bi bi-person"></i> Usuarios
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 5) echo 'active'; ?>" href="./reportes.php">
        <i class="bi bi-bar-chart"></i> Finanzas
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 6) echo 'active'; ?>" href="./configuracion.php">
        <i class="bi bi-gear"></i> Configuración
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