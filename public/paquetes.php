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
.paquetes-container {display:flex;gap:32px;max-width:1200px;margin:40px auto;}
.filtros-lateral {width:260px;background:#fff;padding:24px 18px 18px 18px;border-radius:12px;box-shadow:0 2px 12px #0001;display:flex;flex-direction:column;}
.filtros-lateral h3 {color:#b84e6f;margin-bottom:18px;}
.filtros-lateral label {font-weight:500;}
.filtros-lateral select, .filtros-lateral input[type="number"] {width:100%;margin-bottom:14px;padding:7px;border-radius:5px;border:1px solid #ccc;}
.filtros-lateral button {width:100%;background:#b84e6f;color:#fff;padding:10px;border:none;border-radius:5px;font-size:1.1em;}
.busqueda-bar {margin:0 auto 24px auto;max-width:900px;display:flex;gap:10px;}
.busqueda-bar input[type="text"] {flex:1;padding:10px;border-radius:6px;border:1px solid #ccc;}
.busqueda-bar button {background:#b84e6f;color:#fff;border:none;padding:10px 22px;border-radius:6px;}
.paquetes-lista {flex:1;}
.paquete-card-lista {background:#fff;border-radius:14px;box-shadow:0 2px 12px #0001;display:flex;gap:22px;align-items:center;margin-bottom:28px;padding:18px 24px;}
.paquete-img {width:170px;height:110px;object-fit:cover;border-radius:10px;}
.paquete-body {flex:1;}
.paquete-title {font-size:1.3em;font-weight:600;color:#b84e6f;}
.paquete-detalles {margin:8px 0 10px 0;color:#444;}
.paquete-precio {font-size:1.1em;color:#b84e6f;font-weight:600;}
.paquete-btn {background:#b84e6f;color:#fff;padding:8px 18px;border:none;border-radius:6px;font-size:1em;cursor:pointer;}
.filtros-etiquetas {margin-top:18px;}
.filtros-etiquetas h4 {margin-bottom:8px;color:#b84e6f;font-size:1em;}
.etiquetas-lista {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 7px;
}
.etiqueta-check {
  width: 100%;           /* Ocupa todo el ancho de la celda */
  box-sizing: border-box;
  white-space: normal;   /* Permite salto de línea si es necesario */
  display: flex;
  align-items: center;
  background: #f3e6ea;
  color: #b84e6f;
  border-radius: 10px;
  padding: 6px 10px;
  font-size: 0.98em;
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
.etiquetas-seleccionadas {
  margin: 10px 0 20px 0;
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}
.etiqueta-tag {
  background: #b84e6f;
  color: #fff;
  border-radius: 8px;
  padding: 4px 12px;
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
@media (max-width:900px) {
  .paquetes-container {flex-direction:column;}
  .filtros-lateral {width:100%;margin-bottom:20px;}
}
</style>


<div id="etiquetas-ajax"></div>

<?php if($etiquetas_seleccionadas): ?>
  <div class="etiquetas-seleccionadas">
    <?php
    $ids = implode(',', array_map('intval', $etiquetas_seleccionadas));
    $sql_etq = $conexion->query("SELECT id_etiqueta, nombre FROM etiquetas WHERE id_etiqueta IN ($ids)");
    while($et = $sql_etq->fetch_assoc()):
    ?>
      <span class="etiqueta-tag">
        <?= htmlspecialchars($et['nombre']) ?>
        <form method="get" style="display:inline;">
          <?php
          // Mantener otros filtros
          foreach(['busqueda','precio_min','precio_max','tipo','destino'] as $f)
            if(isset($_GET[$f])) echo '<input type="hidden" name="'.$f.'" value="'.htmlspecialchars($_GET[$f]).'">';
          // Mantener las demás etiquetas menos esta
          foreach($etiquetas_seleccionadas as $otro_etq)
            if($otro_etq != $et['id_etiqueta'])
              echo '<input type="hidden" name="etiquetas[]" value="'.htmlspecialchars($otro_etq).'">';
          ?>
          <button type="submit" class="remove-etq" title="Quitar etiqueta" tabindex="-1">&times;</button>
        </form>
      </span>
    <?php endwhile; ?>
  </div>
<?php endif; ?>

<div class="paquetes-container">
    
  <aside class="filtros-lateral">
    <form method="get" id="form-filtros">
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
      <button type="submit" style="margin-top:14px;">Buscar</button>
    </form>
  </aside>
  
  <section class="paquetes-lista">
    <div class="busqueda-bar">
      <form style="display:flex;width:100%;" method="get" id="form-busqueda">
        <input type="text" name="busqueda" placeholder="Buscar destino o paquete..." value="<?=htmlspecialchars($busqueda)?>">
        <?php
        // Mantener filtros al buscar
        foreach(['precio_min','precio_max','tipo','destino'] as $f)
          if(isset($_GET[$f])) echo '<input type="hidden" name="'.$f.'" value="'.htmlspecialchars($_GET[$f]).'">';
        // Etiquetas seleccionadas
        if ($etiquetas_seleccionadas) {
          foreach($etiquetas_seleccionadas as $etq) {
            echo '<input type="hidden" name="etiquetas[]" value="'.htmlspecialchars($etq).'">';
          }
        }
        ?>
        <button type="submit">Buscar</button>
      </form>
    </div>
    <div id="etiquetas-ajax"></div>
    <?php if($result->num_rows): ?>
      <?php while($row = $result->fetch_assoc()): ?>
        <div class="paquete-card-lista">
          <img class="paquete-img" src="<?= htmlspecialchars($row['imagen_destacada']) ?>" alt="Imagen paquete">
          <div class="paquete-body">
            <div class="paquete-title"><?= htmlspecialchars($row['nombre']) ?></div>
            <div class="paquete-detalles"><?= htmlspecialchars($row['descripcion']) ?></div>
            <div><b>Destino:</b> <?= htmlspecialchars($row['destino']) ?> | <b>Tipo:</b> <?= htmlspecialchars($row['tipo_paquete']) ?></div>
            <div><b>Fechas:</b> <?= date('d/m/Y', strtotime($row['fecha_inicio'])) ?> al <?= date('d/m/Y', strtotime($row['fecha_fin'])) ?></div>
            <div class="paquete-precio">$<?= number_format($row['precio_base'],2) ?> USD</div>
            <a href="paquetes_info.php?id=<?= $row['id_paquete'] ?>"><button class="paquete-btn">Ver detalles</button></a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>No se encontraron paquetes con esos filtros.</p>
    <?php endif; ?>
  </section>
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