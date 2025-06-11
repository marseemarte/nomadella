<style>
.sidebar {
  height: 100vh;
  position: fixed;
  top: 0;
  left: 0;
  width: 260px;
  background: linear-gradient(180deg, #750D37 90%,#5CC7ED 120%);
  color: #FFF6F8;
  padding-top: 20px;
  box-shadow: 2px 0 10px rgba(117,13,55,0.08);
  z-index: 100;
}
.sidebar .nav-link {
  color: #FFF6F8;
  font-weight: 500;
  padding: 12px 18px;
  border-radius: 8px;
  margin-bottom: 4px;
  transition: background 0.2s, color 0.2s;
}

#active{
     background: #6CE0B6;
  color: #1A001C !important;
}

.sidebar .nav-link.active,
.sidebar .nav-link:hover {
  background: #6CE0B6;
  color: #1A001C !important;
}
.sidebar hr {
  margin: 18px 0 12px 0;
}
</style>

<div class="sidebar d-flex flex-column justify-content-between">
  <div>
    <div class="text-center mb-4">
      <img src="/nomadella/img/nomadella_logotipo.png" alt="Logo Agencia" style="max-width: 80px; margin-bottom: 10px;">
      <h4 style="font-weight:bold; color:#FFF6F8;">Nomadella</h4>
      <small style="color:#6CE0B6;">Panel de Administración</small>
    </div>
    <nav class="nav flex-column">
      <a class="nav-link px-4 py-2 " id="<?php if (!isset($_GET['active']) || $_GET['active'] == 1) echo 'active'; ?>" href="./dashboard.php?active=1">
        <i class="bi bi-house-door"></i> Dashboard
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 2) echo 'active'; ?>" href="./destinos.php?active=2">
        <i class="bi bi-truck"></i> Proveedores
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 7) echo 'active'; ?>" href="./paquetes.php?active=3">
        <i class="bi bi-postcard"></i> Paquetes
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 3) echo 'active'; ?>" href="./reservas.php?active=3">
        <i class="bi bi-cart"></i> Reservas
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 4) echo 'active'; ?>" href="./clientes.php?active=4">
        <i class="bi bi-person"></i> Usuarios
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 5) echo 'active'; ?>" href="./reportes.php?active=5">
        <i class="bi bi-bar-chart"></i> Finanzas
      </a>
      <a class="nav-link px-4 py-2 " id="<?php if (isset($_GET['active']) && $_GET['active'] == 6) echo 'active'; ?>" href="./configuracion.php?active=6">
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
    <a href="/nomadella/logout.php" class="btn btn-sm w-100" style="background:#5CC7ED; color:#1A001C; font-weight:bold;">
      <i class="bi bi-box-arrow-right"></i> Cerrar sesión
    </a>
    <div class="text-center mt-2" style="color:#FFF6F8; font-size:0.85em; opacity:0.7;">
      &copy; <?= date('Y') ?> Nomadella
    </div>
  </div>
</div>

