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
  <link rel="stylesheet" href="../css/index.css">
  <!-- Glider.js CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.css">
  
  <style>
    .user-menu {position:relative;display:inline-block;}
    .user-dropdown {
      display:none;position:absolute;right:0;top:30px;min-width:160px;
      background:#fff;border:1px solid #eee;border-radius:8px;box-shadow:0 2px 8px #0002;z-index:10;
    }
    .user-menu:hover .user-dropdown {display:block;}
    .user-dropdown a {
      display:block;padding:10px 18px;color:#b84e6f;text-decoration:none;font-size:1em;
    }
    .user-dropdown a:hover {background:#f3e6ea;}
  </style>
</head>
<body>
  <header>
    <a href="./index.php"><div class="logo"><img class="logo_img" src="../img/nomadella_logo.png" alt=""></div></a>
    <nav class="nav-center">
    <ul>
      <li><a href="index.php">Inicio</a></li>
      <li><a href="./paquetes.php">Paquetes</a></li>
      <li><a href="#">Contacto</a></li>
    </ul>
  </nav>
    <div class="header-icons">
      <?php if (isset($_SESSION['usuario_id'])): ?>
        <div class="user-menu">
          <span title="Mi cuenta" style="cursor:pointer;font-size:1.3em;">👤▼</span>
          <div class="user-dropdown">
            <a href="perfil.php">Mi perfil</a>
            <a href="carrito.php">Carrito</a>
            <a href="reservas.php">Mis reservas</a>
            <a href="logout.php" style="color:#b84e6f;">Cerrar sesión</a>
          </div>
        </div>
      <?php else: ?>
        <a href="./login.php"><span title="Usuario">👤</span></a>
      <?php endif; ?>
      <span title="Menú">☰</span>
    </div>
  </header>
