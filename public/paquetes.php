<?php
include 'header.php';
include 'conexion.php';

// --- Filtros ---
$where = [];
$params = [];
$types = "";

// Buscador
$busqueda = $_GET['busqueda'] ?? '';
if ($busqueda) {
  $where[] = "(nombre LIKE ? OR destino LIKE ?)";
  $params[] = "%$busqueda%";
  $params[] = "%$busqueda%";
  $types .= "ss";
}

// Precio
$precio_min = $_GET['precio_min'] ?? '';
$precio_max = $_GET['precio_max'] ?? '';
if ($precio_min !== '') {
  $where[] = "precio_base >= ?";
  $params[] = $precio_min;
  $types .= "d";
}
if ($precio_max !== '') {
  $where[] = "precio_base <= ?";
  $params[] = $precio_max;
  $types .= "d";
}

// Tipo de paquete
$tipo = $_GET['tipo'] ?? '';
if ($tipo) {
  $where[] = "tipo_paquete = ?";
  $params[] = $tipo;
  $types .= "s";
}

// Destino
$destino = $_GET['destino'] ?? '';
if ($destino) {
  $where[] = "destino = ?";
  $params[] = $destino;
  $types .= "s";
}

// Etiquetas (pueden ser varias)
$etiquetas_seleccionadas = isset($_GET['etiquetas']) && is_array($_GET['etiquetas']) ? array_filter($_GET['etiquetas']) : [];
if ($etiquetas_seleccionadas) {
  foreach ($etiquetas_seleccionadas as $etq) {
    $where[] = "EXISTS (SELECT 1 FROM paquete_etiquetas pe WHERE pe.id_paquete = paquetes_turisticos.id_paquete AND pe.id_etiqueta = ?)";

    $params[] = $etq;
    $types .= "i";
  }
}

$where[] = "activo = 1";
$sql = "SELECT * FROM paquetes_turisticos";
if ($where) $sql .= " WHERE " . implode(" AND ", $where);

$stmt = $conexion->prepare($sql);
if ($params) $stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Obtener etiquetas para mostrar en filtros
$etiquetas = $conexion->query("SELECT * FROM etiquetas ORDER BY nombre");
?>

<div class="paquetes-container">
  <button type="button" id="btn-filtros-toggle" class="btn btn-primary">Mostrar filtros</button>
  <form method="get" id="form-filtros">

    
    <aside class="filtros-lateral">
      <h3>Filtros</h3>
      <label>Precio mínimo:</label>
      <input type="number" name="precio_min" min="0" step="10" value="<?= htmlspecialchars($precio_min) ?>">
      <label>Precio máximo:</label>
      <input type="number" name="precio_max" min="0" step="10" value="<?= htmlspecialchars($precio_max) ?>">
      <label>Tipo de paquete:</label>
      <select name="tipo">
        <option value="">Todos</option>
        <?php
        $tipos = $conexion->query("SELECT DISTINCT tipo_paquete FROM paquetes_turisticos WHERE activo=1");
        while ($t = $tipos->fetch_assoc()):
        ?>
          <option value="<?= $t['tipo_paquete'] ?>" <?= $tipo == $t['tipo_paquete'] ? 'selected' : '' ?>><?= $t['tipo_paquete'] ?></option>
        <?php endwhile; ?>
      </select>
      <label>Destino:</label>
      <select name="destino">
        <option value="">Todos</option>
        <?php
        $destinos = $conexion->query("SELECT DISTINCT destino FROM paquetes_turisticos WHERE activo=1");
        while ($d = $destinos->fetch_assoc()):
        ?>
          <option value="<?= $d['destino'] ?>" <?= $destino == $d['destino'] ? 'selected' : '' ?>><?= $d['destino'] ?></option>
        <?php endwhile; ?>

      </select>
      <button type="submit" class="btn btn-primary" style="margin-top:15px;">Aplicar filtros</button>
      <div class="filtros-etiquetas">
        <h4>Etiquetas</h4>
        <div class="etiquetas-lista">
          <?php
          $etiquetas->data_seek(0);
          while ($et = $etiquetas->fetch_assoc()):
            $checked = in_array($et['id_etiqueta'], $etiquetas_seleccionadas);
          ?>
            <label class="etiqueta-check<?= $checked ? ' selected' : '' ?>">
              <input type="checkbox" name="etiquetas[]" value="<?= $et['id_etiqueta'] ?>" <?= $checked ? 'checked' : '' ?>>
              <?= htmlspecialchars($et['nombre']) ?>
            </label>
          <?php endwhile; ?>
        </div>
      </div>

    </aside>
    <section class="paquetes-lista" style="flex:1;">
      <div class="busqueda-bar">
        <input type="text" name="busqueda" placeholder="Buscar destino o paquete..." value="<?= htmlspecialchars($busqueda) ?>">
        <button type="submit">Buscar</button>
      </div>
      <div id="etiquetas-ajax"></div>
      <?php if ($result->num_rows): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="paquete-card-lista">

            <div class="paquete-body">
              <div class="paquete-title"><?= htmlspecialchars($row['nombre']) ?></div>
              <div class="paquete-detalles"><?= htmlspecialchars($row['descripcion']) ?></div>
              <div><b>Destino:</b> <?= htmlspecialchars($row['destino']) ?> | <b>Tipo:</b> <?= htmlspecialchars($row['tipo_paquete']) ?></div>
              <div><b>Fechas:</b> <?= date('d/m/Y', strtotime($row['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($row['fecha_fin'])) ?></div>
              <div class="paquete-precio">$<?= number_format($row['precio_base'], 2) ?> USD</div>
              <a href="paquetes_info.php?id=<?= $row['id_paquete'] ?>" class="paquete-btn">Ver detalles</a>
            </div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No se encontraron paquetes con esos filtros.</p>
      <?php endif; ?>
    </section>
  </form>
</div>
<?php include 'footer.php'; ?>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const etiquetasChecks = document.querySelectorAll('.etiqueta-check input[type="checkbox"]');
    const etiquetasAjax = document.getElementById('etiquetas-ajax');

    function actualizarEtiquetasSeleccionadas() {
      // Obtén las etiquetas seleccionadas
      const seleccionadas = [];
      etiquetasChecks.forEach(chk => {
        if (chk.checked) {
          seleccionadas.push({
            id: chk.value,
            nombre: chk.parentElement.textContent.trim()
          });
        }
      });

      // Renderiza las etiquetas seleccionadas
      if (seleccionadas.length) {
        etiquetasAjax.innerHTML = '<div class="etiquetas-seleccionadas">' +
          seleccionadas.map(et =>
            `<span class="etiqueta-tag">${et.nombre}<button class="remove-etq" data-id="${et.id}" title="Quitar etiqueta" tabindex="-1">&times;</button></span>`
          ).join('') +
          '</div>';
      } else {
        etiquetasAjax.innerHTML = '';
      }
    }

    // Evento para quitar etiqueta desde el tag
    etiquetasAjax.addEventListener('click', function(e) {
      if (e.target.classList.contains('remove-etq')) {
        const id = e.target.getAttribute('data-id');
        // Desmarca el checkbox correspondiente
        etiquetasChecks.forEach(chk => {
          if (chk.value === id) chk.checked = false;
        });
        actualizarEtiquetasSeleccionadas();
      }
    });

    // Evento para cada checkbox
    etiquetasChecks.forEach(chk => {
      chk.addEventListener('change', actualizarEtiquetasSeleccionadas);
    });

    // Inicializa al cargar
    actualizarEtiquetasSeleccionadas();
  });
  document.addEventListener('DOMContentLoaded', function() {

    const btnToggle = document.getElementById('btn-filtros-toggle');
    const filtros = document.querySelector('.filtros-lateral');

    btnToggle.addEventListener('click', function () {
  filtros.classList.toggle('show');
  btnToggle.textContent = filtros.classList.contains('show') ? 'Ocultar filtros' : 'Mostrar filtros';

  if (filtros.classList.contains('show')) {
    filtros.scrollIntoView({ behavior: 'smooth' });
  }
});

  });
</script>