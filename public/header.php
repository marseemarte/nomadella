<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nomadella - Paquetes Turísticos</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700&family=Roboto:wght@400;500&display=swap" rel="stylesheet">
  <!-- <link rel="stylesheet" href="../css/index.css"> -->
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"
/>
  <link rel="stylesheet" href="../css/index.css">
<base href="/pruebaasda/nomadella/public/">
</head>
<body>
  <header class="bg-gradient p-0">
  <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, #741d41 60%, #b84e6f 100%);">
    <div class="container-fluid d-flex justify-content-between align-items-center">

      <!-- Logo a la izquierda -->
      <a class="navbar-brand d-flex align-items-center me-auto" href="./index.php">
        <img src="../img/nomadella_logo.png" alt="Nomadella" class="logo_img" style="height:48px;">
      </a>

      <!-- Menú centrado -->
      <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
        <ul class="navbar-nav mb-2 mb-lg-0 text-center">
          <li class="nav-item"><a class="nav-link px-3" href="index.php">Inicio</a></li>
          <li class="nav-item"><a class="nav-link px-3" href="./paquetes.php">Paquetes</a></li>
          <li class="nav-item"><a class="nav-link px-3" href="#">Contacto</a></li>
        </ul>
      </div>

      <!-- Íconos a la derecha -->
      <div class="d-flex align-items-center gap-2 ms-auto">
        <?php if (isset($_SESSION['usuario_id'])): ?>
          <div class="dropdown">
            <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-person-circle"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <li><a class="dropdown-item" href="perfil/perfil.php">Mi perfil</a></li>
              <li><a class="dropdown-item" href="perfil/reservas.php">Mis reservas</a></li>
              <?php if (isset($_SESSION['rol']) && in_array($_SESSION['rol'], [1,2])): ?>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-primary fw-bold" href="/nomadella/dashboard/dashboard.php">
                  <i class="bi bi-speedometer2"></i> Ingresar al Dashboard
                </a></li>
              <?php endif; ?>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-danger" href="logout.php">Cerrar sesión</a></li>
            </ul>
          </div>
        <?php else: ?>
          <a href="./login.php" class="btn btn-outline-light"><i class="bi bi-person"></i></a>
        <?php endif; ?>
        <a href="./carrito.php" class="btn btn-outline-light position-relative">
          <i class="bi bi-cart3"></i>
          <span id="carrito-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:0.8em;display:none;">
            0
          </span>
        </a>
      </div>

    </div>
  </nav>
</header>

