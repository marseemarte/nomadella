<?php
session_start();
include 'header.php';
include 'conexion.php';

if (!isset($_SESSION['usuario_id'])) {
  $id_usuario = 0;
  $res_notif = null;
} else {
  $id_usuario = $_SESSION['usuario_id'];
  
  $sql_notif = "SELECT * FROM notificaciones 
                WHERE id_usuario = $id_usuario AND leido = 0 
                ORDER BY fecha DESC LIMIT 1";
  $res_notif = $conexion->query($sql_notif);
}
?>

<div class="hero-section">
  <?php if ($res_notif && $notif = $res_notif->fetch_assoc()): ?>
    <div class="alert 
    <?= $notif['tipo'] == 'confirmada' ? 'alert-success' : ($notif['tipo'] == 'pendiente' ? 'alert-warning' : ($notif['tipo'] == 'cambio_fecha' ? 'alert-info' : 'alert-danger')) ?>"
      role="alert">
      <?= htmlspecialchars($notif['mensaje']) ?>
      <?php if ($notif['tipo'] == 'cambio_fecha'): ?>
        <button class="btn btn-success btn-sm ms-3" onclick="confirmarCambioFecha(<?= $notif['id'] ?>)">Aceptar cambio</button>
        <button class="btn btn-danger btn-sm ms-3" onclick="rechazarCambioFecha(<?= $notif['id'] ?>)">Rechazar cambio</button>
      <?php endif; ?>
      <button class="btn-close float-end" onclick="marcarLeido(<?= $notif['id'] ?>)"></button>
    </div>
<?php endif; ?>


  <h1>Descubrí tu próximo destino</h1>
  <form class="buscador-form" method="get" action="paquetes.php">
    <input type="text" name="busqueda" placeholder="Busca tu destino soñado..." class="form-control border-0 shadow-none">
    <button type="submit">Buscar</button>
  </form>
</div>

<!-- Carrusel de Paquetes Destacados -->
<section class="container my-5">
  <h2 class="text-center mb-4" style="color:#741d41;">Paquetes Turísticos Destacados</h2>

  <!-- WRAPPER nuevo para controlar flechas -->
  <div class="swiper-container-wrapper position-relative">
    <div class="swiper mySwiper">
      <div class="swiper-wrapper">
        <?php

        $sql = "SELECT * FROM paquetes_turisticos WHERE activo = 1 LIMIT 9";
        $result = $conexion->query($sql);
        while ($row = $result->fetch_assoc()):
        ?>
          <div class="swiper-slide">
            <div class="card shadow-sm" style="min-height:200px;">
              <div class="card-body justify-content-between d-flex flex-column">
                <h5 class="card-title" style="color:#b84e6f;"><?= htmlspecialchars($row['nombre']) ?></h5>
                <p class="card-text"><?= htmlspecialchars($row['descripcion']) ?></p>
                <div class="fw-bold mb-2" style="color:#741d41;">$<?= number_format($row['precio_base'], 2) ?> USD</div>
                <a href="./paquetes_info.php?id=<?= $row['id_paquete'] ?>" class="btn btn-primary w-100" style="background:linear-gradient(90deg,#741d41 60%,#b84e6f 100%);border:none;">Ver detalles</a>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>

      <!-- Paginación -->
      <br>
      <br>
      <div class="swiper-pagination mt-4"></div>
    </div>

    <!-- Flechas por fuera del slider -->
    <div class="swiper-button-prev"></div>
    <div class="swiper-button-next"></div>
  </div>
</section>



<section class=" container justify-content-center text-center my-5 cta-section ">
  <h2 class="mb-4" style="color:#741d41;">¿Listo para tu próxima aventura?</h2>
  <h2>¡Reserva tu aventura hoy!</h2>
  <a href="./paquetes.php" class="btn btn-primary">Ver todos los paquetes</a>
</section>

<!-- Promociones -->
<section class="container my-5">
  <h2 class="text-center mb-4" style="color:#741d41;">Promociones Exclusivas</h2>
  <?php
    $hoy = date('Y-m-d');
    $sql_promos = "SELECT * FROM promociones 
                   WHERE activo = 1 
                   AND fecha_inicio <= '$hoy' 
                   AND fecha_fin >= '$hoy'
                   ORDER BY fecha_inicio DESC";
    $res_promos = $conexion->query($sql_promos);
    $promos = [];
    while ($row = $res_promos->fetch_assoc()) {
      $promos[] = $row;
    }
  ?>
  <?php if (count($promos) > 0): ?>
    <div class="swiper promoSwiper">
      <div class="swiper-wrapper text-center">
        <?php foreach ($promos as $promo): ?>
          <div class="swiper-slide d-flex justify-content-center">
            <div class="card shadow-sm text-center" style="width:350px;background:linear-gradient(90deg,#f8e1e7 60%,#e0f7fa 100%);border:2px solid #b84e6f; height: 200px;">
              <div class="card-body d-flex flex-column justify-content-between">
                <h4 class="card-title mb-2" style="color:#b84e6f;">
                  <i class="bi bi-stars"></i> <?= htmlspecialchars($promo['nombre']) ?>
                </h4>
                <p class="card-text mb-2"><?= htmlspecialchars($promo['descripcion']) ?></p>
                <div class="mb-2">
                  <span class="badge bg-success fs-5">
                    <?= intval($promo['descuento_porcentaje']) ?>% OFF
                  </span>
                </div>
                <small class="text-muted">
                  Válido del <?= date('d/m/Y', strtotime($promo['fecha_inicio'])) ?>
                  al <?= date('d/m/Y', strtotime($promo['fecha_fin'])) ?>
                </small>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <!-- Swiper Pagination & Navigation -->
      <div class="swiper-pagination mt-3"></div>
      <div class="swiper-button-prev"></div>
      <div class="swiper-button-next"></div>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center">No hay promociones activas en este momento.</div>
  <?php endif; ?>
</section>

<!-- Preguntas Frecuentes (FAQ) -->
<section class="container my-5">
  <h2 class="text-center mb-4" style="color:#741d41;">Preguntas Frecuentes</h2>
  <div class="accordion" id="faqAccordion">
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq1">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
          ¿Cómo reservo un paquete turístico?
        </button>
      </h2>
      <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faq1" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Elegí tu paquete favorito, hacé clic en "Reservar" y seguí los pasos. ¡Es rápido y seguro!
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq2">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
          ¿Puedo pagar en cuotas?
        </button>
      </h2>
      <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faq2" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Sí, aceptamos pagos en cuotas con tarjetas seleccionadas. Consultá las opciones al momento de reservar.
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq3">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
          ¿Qué pasa si necesito cancelar o cambiar mi viaje?
        </button>
      </h2>
      <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faq3" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          Podés gestionar cambios o cancelaciones desde tu cuenta o contactando a nuestro equipo de atención al cliente.
        </div>
      </div>
    </div>
    <div class="accordion-item">
      <h2 class="accordion-header" id="faq4">
        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
          ¿Incluyen seguro de viaje?
        </button>
      </h2>
      <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faq4" data-bs-parent="#faqAccordion">
        <div class="accordion-body">
          La mayoría de nuestros paquetes incluyen seguro de viaje. Verificá los detalles en la descripción de cada paquete.
        </div>
      </div>
    </div>
  </div>
</section>

<?php include 'footer.php'; ?>
<!-- Carrusel JS -->
<script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
<script>
  window.addEventListener('load', function() {
    new Glider(document.querySelector('.glider'), {
      slidesToShow: 1,
      slidesToScroll: 1,
      draggable: true,
      dots: '.glider-dots',
      arrows: {
        prev: '.glider-prev',
        next: '.glider-next'
      },
      responsive: [{
          breakpoint: 700,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1
          }
        },
        {
          breakpoint: 1000,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1
          }
        }
      ]
    });
  });
</script>
<script>
  var swiper = new Swiper(".mySwiper", {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".swiper-button-next",
      prevEl: ".swiper-button-prev",
    },
    breakpoints: { // Responsive
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      }
    }
  });
  var promoSwiper = new Swiper(".promoSwiper", {
    slidesPerView: 1,
    spaceBetween: 30,
    loop: true,
    pagination: {
      el: ".promoSwiper .swiper-pagination",
      clickable: true,
    },
    navigation: {
      nextEl: ".promoSwiper .swiper-button-next",
      prevEl: ".promoSwiper .swiper-button-prev",
    },
    breakpoints: {
      768: {
        slidesPerView: 2,
      },
      1024: {
        slidesPerView: 3,
      }
    }
  });
  function marcarLeido(id) {
    fetch('notificacion_leer.php?id=' + id)
        .then(() => location.reload());
}
function confirmarCambioFecha(id) {
    fetch('notificacion_confirmar_fecha.php?id=' + id + '&accion=aceptar')
        .then(() => location.reload());
}
function rechazarCambioFecha(id) {
    fetch('notificacion_confirmar_fecha.php?id=' + id + '&accion=rechazar')
        .then(() => location.reload());
}
</script>
</body>

</html>