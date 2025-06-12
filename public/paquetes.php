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
<style>
/* --- CONTENEDOR PRINCIPAL --- */
body {
  background: #f8f8f4;
  margin: 0;
  font-family: 'Montserrat', Arial, sans-serif;
}

.paquetes-container {
  display: flex;
  gap: 40px;
  max-width: 1400px;
  margin: 48px auto 0 auto;
  justify-content: center;
  align-items: flex-start;
}

/* --- FILTROS LATERALES --- */
.filtros-lateral {
  width: 320px;
  background: #fff;
  padding: 32px 22px 22px 22px;
  border-radius: 16px;
  box-shadow: 0 2px 16px #0001;
  display: flex;
  flex-direction: column;
  gap: 18px;
}

.filtros-lateral h3 {
  color: #b84e6f;
  margin-bottom: 18px;
  font-size: 1.35em;
}

.filtros-lateral label {
  font-weight: 500;
  margin-bottom: 4px;
}

.filtros-lateral select,
.filtros-lateral input[type="number"] {
  width: 100%;
  margin-bottom: 14px;
  padding: 9px;
  border-radius: 6px;
  border: 1px solid #ccc;
  font-size: 1em;
}

.filtros-lateral button {
  width: 100%;
  background: #b84e6f;
  color: #fff;
  padding: 12px;
  border: none;
  border-radius: 6px;
  font-size: 1.1em;
}

.filtros-etiquetas {
  margin-top: 18px;
}
.filtros-etiquetas h4 {
  margin-bottom: 8px;
  color: #b84e6f;
  font-size: 1.08em;
}
.etiquetas-lista {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
}
.etiqueta-check {
  width: 100%;
  box-sizing: border-box;
  white-space: normal;
  display: flex;
  align-items: center;
  background: #f3e6ea;
  color: #b84e6f;
  border-radius: 10px;
  padding: 7px 12px;
  font-size: 1em;
  cursor: pointer;
  border: none;
  min-width: 0;
  min-height: 0;
  transition: background 0.2s, color 0.2s;
  word-break: break-word;
  font-weight: 500;
}
.etiqueta-check input[type="checkbox"] {
  margin-right: 7px;
  accent-color: #b84e6f;
}
.etiqueta-check.selected, .etiqueta-check:hover {
  background: #b84e6f;
  color: #fff;
}

/* --- BUSQUEDA --- */
.busqueda-bar {
  margin: 0 auto 32px auto;
  max-width: 700px;
  display: flex;
  gap: 12px;
  width: 100%;
  justify-content: center;
}
.busqueda-bar input[type="text"] {
  flex: 1;
  padding: 12px;
  border-radius: 8px;
  border: 1px solid #ccc;
  font-size: 1.08em;
}
.busqueda-bar button {
  background: #b84e6f;
  color: #fff;
  border: none;
  padding: 12px 32px;
  border-radius: 8px;
  font-size: 1.08em;
  font-weight: 600;
}

/* --- LISTADO DE PAQUETES --- */
.paquetes-lista {
  flex: 1;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-width: 400px;
  max-width: 800px;
  width: 100%;
}

.paquete-card-lista {
  background: #fff;
  border-radius: 18px;
  box-shadow: 0 2px 16px #0001;
  display: flex;
  gap: 36px;
  align-items: center;
  margin-bottom: 38px;
  padding: 32px 40px;
  width: 100%;
  max-width: 700px;
  min-height: 180px;
  transition: box-shadow 0.2s;
  box-sizing: border-box;
}

.paquete-img {
  width: 210px;
  height: 140px;
  object-fit: cover;
  border-radius: 12px;
  flex-shrink: 0;
  background: #eee;
}

.paquete-body {
  flex: 1;
  min-width: 0;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.paquete-title {
  font-size: 1.5em;
  font-weight: 700;
  color: #b84e6f;
  margin-bottom: 8px;
}

.paquete-detalles {
  margin: 8px 0 10px 0;
  color: #444;
  font-size: 1.08em;
}

.paquete-precio {
  font-size: 1.18em;
  color: #fff;
  background: linear-gradient(90deg, #b84e6f 60%, #741d41 100%);
  padding: 14px 0;
  border-radius: 12px;
  text-align: center;
  font-family: 'Montserrat', Arial, sans-serif;
  font-weight: 700;
  margin: 18px 0 14px 0;
  letter-spacing: 1px;
  box-shadow: 0 2px 8px rgba(116,29,65,0.08);
  width: 100%;
}

.paquete-btn {
  background: #b84e6f;
  color: #fff;
  padding: 12px 0;
  border: none;
  border-radius: 8px;
  font-size: 1.1em;
  cursor: pointer;
  text-align: center;
  text-decoration: none;
  display: block;
  font-weight: 700;
  width: 100%;
  margin-top: 8px;
  transition: background 0.2s;
}
.paquete-btn:hover {
  background: #741d41;
}

/* --- ETIQUETAS SELECCIONADAS --- */
.etiquetas-seleccionadas {
  margin: 16px 0 24px 0;
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  justify-content: flex-start;
}
.etiqueta-tag {
  background: #b84e6f;
  color: #fff;
  border-radius: 8px;
  padding: 6px 16px;
  font-size: 1em;
  display: flex;
  align-items: center;
  gap: 4px;
}
.etiqueta-tag .remove-etq {
  background: none;
  border: none;
  color: #fff;
  font-size: 1.1em;
  margin-left: 4px;
  cursor: pointer;
  line-height: 1;
}

/* --- RESPONSIVE --- */
@media (max-width:1200px) {
  .paquetes-container {flex-direction:column;align-items:center;}
  .filtros-lateral {width:100%;max-width:500px;margin-bottom:28px;}
  .paquetes-lista {max-width:100%;}
  .paquete-card-lista {max-width:100%;}
}
@media (max-width:800px) {
  .paquete-card-lista {flex-direction:column;align-items:stretch;padding:18px 8px;}
  .paquete-img {width:100%;height:160px;}
  .paquete-body {align-items:stretch;}
}
@media (max-width:600px) {
  .filtros-lateral {padding:12px 4vw;}
  .paquete-card-lista {padding:12px 2vw;}
  .busqueda-bar {flex-direction:column;gap:8px;}
}
</style>






<div class="paquetes-container">
  <form method="get" id="form-filtros" style="display:flex;gap:32px;max-width:1200px;margin:40px auto;">
    <aside class="filtros-lateral">
      <h3>Filtros</h3>
      <label>Precio mínimo:</label>
      <input type="number" name="precio_min" min="0" step="10" value="<?=htmlspecialchars($precio_min)?>">
      <label>Precio máximo:</label>
      <input type="number" name="precio_max" min="0" step="10" value="<?=htmlspecialchars($precio_max)?>">
      <label>Tipo de paquete:</label>
      <select name="tipo">
        <option value="">Todos</option>
        <?php
        $tipos = $conexion->query("SELECT DISTINCT tipo_paquete FROM paquetes_turisticos WHERE activo=1");
        while($t = $tipos->fetch_assoc()):
        ?>
        <option value="<?=$t['tipo_paquete']?>" <?=$tipo==$t['tipo_paquete']?'selected':''?>><?=$t['tipo_paquete']?></option>
        <?php endwhile; ?>
      </select>
      <label>Destino:</label>
      <select name="destino">
        <option value="">Todos</option>
        <?php
        $destinos = $conexion->query("SELECT DISTINCT destino FROM paquetes_turisticos WHERE activo=1");
        while($d = $destinos->fetch_assoc()):
        ?>
        <option value="<?=$d['destino']?>" <?=$destino==$d['destino']?'selected':''?>><?=$d['destino']?></option>
        <?php endwhile; ?>
      </select>
      <div class="filtros-etiquetas">
        <h4>Etiquetas</h4>
        <div class="etiquetas-lista">
          <?php
          $etiquetas->data_seek(0);
          while($et = $etiquetas->fetch_assoc()):
            $checked = in_array($et['id_etiqueta'], $etiquetas_seleccionadas);
          ?>
            <label class="etiqueta-check<?= $checked ? ' selected' : '' ?>">
              <input type="checkbox" name="etiquetas[]" value="<?=$et['id_etiqueta']?>" <?= $checked ? 'checked' : '' ?>>
              <?= htmlspecialchars($et['nombre']) ?>
            </label>
          <?php endwhile; ?>
        </div>
      </div>
    </aside>
    <section class="paquetes-lista" style="flex:1;">
      <div class="busqueda-bar">
        <input type="text" name="busqueda" placeholder="Buscar destino o paquete..." value="<?=htmlspecialchars($busqueda)?>">
        <button type="submit">Buscar</button>
      </div>
      <div id="etiquetas-ajax"></div>
      <?php if($result->num_rows): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="paquete-card-lista">
           
            <div class="paquete-body">
              <div class="paquete-title"><?= htmlspecialchars($row['nombre']) ?></div>
              <div class="paquete-detalles"><?= htmlspecialchars($row['descripcion']) ?></div>
              <div><b>Destino:</b> <?= htmlspecialchars($row['destino']) ?> | <b>Tipo:</b> <?= htmlspecialchars($row['tipo_paquete']) ?></div>
              <div><b>Fechas:</b> <?= date('d/m/Y', strtotime($row['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($row['fecha_fin'])) ?></div>
              <div class="paquete-precio">$<?= number_format($row['precio_base'],2) ?> USD</div>
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
</script>