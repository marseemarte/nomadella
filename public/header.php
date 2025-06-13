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


</head>
<body>
  <header class="bg-gradient p-0">
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: linear-gradient(90deg, #741d41 60%, #b84e6f 100%);">
      <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="./index.php">
          <img src="../img/nomadella_logo.png" alt="Nomadella" class="logo_img me-2" style="height:48px;">
          Nomadella
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Menú">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNavbar">
          <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
            <li class="nav-item"><a class="nav-link" href="index.php">Inicio</a></li>
            <li class="nav-item"><a class="nav-link" href="./paquetes.php">Paquetes</a></li>
            <li class="nav-item"><a class="nav-link" href="#">Contacto</a></li>
          </ul>
          <div class="d-flex align-items-center gap-2">
            <?php if (isset($_SESSION['usuario_id'])): ?>
              <div class="dropdown">
                <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-person-circle"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                  <li><a class="dropdown-item" href="perfil/perfil.php">Mi perfil</a></li>
                  <li><a class="dropdown-item" href="reservas.php">Mis reservas</a></li>
                  <li><hr class="dropdown-divider"></li>
                  <li><a class="dropdown-item text-danger" href="logout.php">Cerrar sesión</a></li>
                </ul>
              </div>
            <?php else: ?>
              <a href="./login.php" class="btn btn-outline-light"><i class="bi bi-person"></i></a>
            <?php endif; ?>
            <a href="./carrito.php" class="btn btn-outline-light position-relative">
              <i class="bi bi-cart3"></i>
            </a>
          </div>
        </div>
      </div>
    </nav>
    
  </header>
