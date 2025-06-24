<?php
include 'conexion.php';
include 'verificar_admin.php';
$id_paquete = intval($_GET['id_paquete']);

// Alojamientos
if (empty($_POST['omitir_alojamiento']) && !empty($_POST['alojamientos'])) {
    foreach ($_POST['alojamientos'] as $id_aloj) {
        $conn->query("INSERT INTO paquete_alojamientos (id_paquete, id_alojamiento) VALUES ($id_paquete, " . intval($id_aloj) . ")");
    }
}
// Vuelos
if (empty($_POST['omitir_vuelo']) && !empty($_POST['vuelos'])) {
    foreach ($_POST['vuelos'] as $id_vuelo) {
        $conn->query("INSERT INTO paquete_vuelos (id_paquete, id_vuelo) VALUES ($id_paquete, " . intval($id_vuelo) . ")");
    }
}
// Autos
if (empty($_POST['omitir_auto']) && !empty($_POST['autos'])) {
    foreach ($_POST['autos'] as $id_auto) {
        $conn->query("INSERT INTO paquete_autos (id_paquete, id_alquiler) VALUES ($id_paquete, " . intval($id_auto) . ")");
    }
}
// Servicios
if (empty($_POST['omitir_servicio']) && !empty($_POST['servicios'])) {
    foreach ($_POST['servicios'] as $id_serv) {
        $conn->query("INSERT INTO paquete_servicios (id_paquete, id_servicio) VALUES ($id_paquete, " . intval($id_serv) . ")");
    }
}

header("Location: paquetes.php?ok=1");
exit;
?>