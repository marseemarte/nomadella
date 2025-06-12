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
</head>
<body>
  <header>
    <div class="logo"><img class="logo_img" src="../img/nomadella_logo.png" alt=""></div>
    <nav class="nav-center">
    <ul>
      <li><a href="#">Inicio</a></li>
      <li><a href="#">Paquetes</a></li>
      <li><a href="#">Contacto</a></li>
    </ul>
  </nav>
    <div class="header-icons">
      <span title="Usuario">👤</span>
      <span title="Menú">☰</span>
    </div>
  </header>

  <div class="buscador">
    <input type="text" placeholder="Busca tu destino soñado...">
  </div>

  <!-- Carrusel de Paquetes Destacados -->
  <section class="paquetes-section">
    <h2>Paquetes Turísticos Destacados</h2>
    <div class="glider-contain">
      <button class="glider-prev">&#8592;</button>
      <div class="glider">
        <div class="paquete-card">
          <img class="paquete-img" src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=600&q=80" alt="Paquete Playa">
          <div class="paquete-body">
            <div class="paquete-title">Playa Paraíso Cancún</div>
            <div class="paquete-detalles">
              5 días / 4 noches<br>
              Salida desde CDMX
            </div>
            <div class="paquete-servicios">
              <span class="paquete-servicio">🏨 Hotel 4*</span>
              <span class="paquete-servicio">✈️ Vuelo incluido</span>
              <span class="paquete-servicio">🍽️ Desayuno buffet</span>
              <span class="paquete-servicio">🚗 Traslados aeropuerto</span>
            </div>
            <div class="paquete-precio">$12,990 USD</div>
            <button class="paquete-btn">Ver detalles</button>
          </div>
        </div>
        <div class="paquete-card">
          <img class="paquete-img" src="https://images.unsplash.com/photo-1464983953574-0892a716854b?auto=format&fit=crop&w=600&q=80" alt="Paquete Montaña">
          <div class="paquete-body">
            <div class="paquete-title">Aventura en la Sierra</div>
            <div class="paquete-detalles">
              4 días / 3 noches<br>
              Salida desde Guadalajara
            </div>
            <div class="paquete-servicios">
              <span class="paquete-servicio">🏕️ Cabaña premium</span>
              <span class="paquete-servicio">🚌 Transporte terrestre</span>
              <span class="paquete-servicio">🔥 Fogata y actividades</span>
              <span class="paquete-servicio">🍳 Desayuno incluido</span>
            </div>
            <div class="paquete-precio">$8,500 USD</div>
            <button class="paquete-btn">Ver detalles</button>
          </div>
        </div>
        <div class="paquete-card">
          <img class="paquete-img" src="https://images.unsplash.com/photo-1507089947368-19c1da9775ae?auto=format&fit=crop&w=600&q=80" alt="Paquete Ciudad">
          <div class="paquete-body">
            <div class="paquete-title">Escapada Cultural CDMX</div>
            <div class="paquete-detalles">
              3 días / 2 noches<br>
              Salida desde Monterrey
            </div>
            <div class="paquete-servicios">
              <span class="paquete-servicio">🏨 Hotel céntrico</span>
              <span class="paquete-servicio">✈️ Vuelo redondo</span>
              <span class="paquete-servicio">🎟️ Tour museos</span>
              <span class="paquete-servicio">🍽️ Desayuno</span>
            </div>
            <div class="paquete-precio">$7,200 USD</div>
            <button class="paquete-btn">Ver detalles</button>
          </div>
        </div>
        <div class="paquete-card">
          <img class="paquete-img" src="https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=600&q=80" alt="Paquete Familiar">
          <div class="paquete-body">
            <div class="paquete-title">Diversión en Familia</div>
            <div class="paquete-detalles">
              6 días / 5 noches<br>
              Salida desde Querétaro
            </div>
            <div class="paquete-servicios">
              <span class="paquete-servicio">🏨 Resort familiar</span>
              <span class="paquete-servicio">🚌 Transporte privado</span>
              <span class="paquete-servicio">🎡 Acceso a parque</span>
              <span class="paquete-servicio">🍔 Todo incluido</span>
            </div>
            <div class="paquete-precio">$15,800 USD</div>
            <button class="paquete-btn">Ver detalles</button>
          </div>
        </div>
      </div>
      <button class="glider-next">&#8594;</button>
      <div role="tablist" class="glider-dots"></div>
    </div>
  </section>
  <!-- Filtros avanzados -->
<!-- Filtros avanzados -->
<section class="filtros-section">
  <form class="filtros-form">
    <div>
      <label for="precio">Precio:</label>
      <input type="range" id="precio" name="precio" min="200" max="4000" step="50" value="1500" oninput="precioOutput.value = precio.value">
      <output id="precioOutput">1500</output> USD
    </div>
    <div>
      <label for="duracion">Duración:</label>
      <select id="duracion" name="duracion">
        <option value="">Cualquier duración</option>
        <?php for($i=1;$i<=10;$i++): ?>
          <option value="<?=$i?>"><?=$i?> día<?=($i>1?'s':'')?></option>
        <?php endfor; ?>
      </select>
    </div>
    <div>
      <label for="salida">Salida desde:</label>
      <select id="salida" name="salida">
        <option value="">Cualquier ciudad</option>
        <option>Buenos Aires</option>
        <option>Córdoba</option>
        <option>Mendoza</option>
        <option>Rosario</option>
      </select>
    </div>
    <div>
      <label for="tipo">Tipo de viaje:</label>
      <select id="tipo" name="tipo">
        <option value="">Todos</option>
        <option>Playa</option>
        <option>Ciudad</option>
        <option>Montaña</option>
        <option>Aventura</option>
        <option>Familiar</option>
      </select>
    </div>
    <button type="submit" class="filtros-btn">Filtrar</button>
  </form>
  <p style="font-size:0.95em;color:#b84e6f;margin-top:8px;">
    * Todos los precios están expresados en dólares estadounidenses (USD). Consultá por formas de pago en pesos argentinos.
  </p>
</section>
<section class="opiniones-section">
  <h2>Opiniones de nuestros viajeros</h2>
  <div class="opiniones-grid">
    <div class="opinion-card">
      <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Cliente" class="opinion-avatar">
      <div class="opinion-body">
        <div class="opinion-nombre">María G.</div>
        <div class="opinion-estrellas">★★★★★</div>
        <div class="opinion-texto">“Estuve en Cancún y todo fue como lo prometieron. ¡Volvería a reservar con Nomadella!”</div>
      </div>
    </div>
    <div class="opinion-card">
      <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Cliente" class="opinion-avatar">
      <div class="opinion-body">
        <div class="opinion-nombre">Carlos P.</div>
        <div class="opinion-estrellas">★★★★☆</div>
        <div class="opinion-texto">“La atención fue excelente y el hotel superó mis expectativas. ¡Gracias!”</div>
      </div>
    </div>
    <div class="opinion-card">
      <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Cliente" class="opinion-avatar">
      <div class="opinion-body">
        <div class="opinion-nombre">Lucía R.</div>
        <div class="opinion-estrellas">★★★★★</div>
        <div class="opinion-texto">“Viajamos en familia y los niños la pasaron increíble. Todo muy organizado.”</div>
      </div>
    </div>
  </div>
</section>


  <footer>
    <div>
      <p>&copy; Nomadella 2025</p>
    </div>
    <div>
      <p>Términos | Política | FAQ</p>
    </div>
  </footer>

  <!-- Carrusel JS -->
  <script src="https://cdn.jsdelivr.net/npm/glider-js@1/glider.min.js"></script>
  <script>
    window.addEventListener('load', function(){
      new Glider(document.querySelector('.glider'), {
        slidesToShow: 1,
        slidesToScroll: 1,
        draggable: true,
        dots: '.glider-dots',
        arrows: {
          prev: '.glider-prev',
          next: '.glider-next'
        },
        responsive: [
          {
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
</body>
</html>
