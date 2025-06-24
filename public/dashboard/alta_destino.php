<?php
include 'conexion.php';

$tipo = $_POST['tipo'] ?? '';
$nombre = trim($_POST['nombre'] ?? '');
$id_destino = intval($_POST['id_destino'] ?? 0);

if (!$tipo || !$nombre || !$id_destino) {
    http_response_code(400);
    echo "Faltan datos";
    exit;
}

switch ($tipo) {
    case 'alojamiento':
        $conn->query("INSERT INTO alojamientos (nombre, id_destino) VALUES ('".$conn->real_escape_string($nombre)."', $id_destino)");
        $id = $conn->insert_id;
        break;
    case 'vuelo':
        $conn->query("INSERT INTO vuelos (aerolinea, id_destino) VALUES ('".$conn->real_escape_string($nombre)."', $id_destino)");
        $id = $conn->insert_id;
        break;
    case 'auto':
        $conn->query("INSERT INTO alquiler_autos (proveedor, id_destino) VALUES ('".$conn->real_escape_string($nombre)."', $id_destino)");
        $id = $conn->insert_id;
        break;
    case 'servicio':
        $conn->query("INSERT INTO servicios_adicionales (nombre, id_destino) VALUES ('".$conn->real_escape_string($nombre)."', $id_destino)");
        $id = $conn->insert_id;
        break;
    default:
        http_response_code(400);
        echo "Tipo inválido";
        exit;
}

//agregar a la bitacora
$bitacora = "INSERT INTO bitacora_sistema (id_usuario, accion, descripcion, fecha_hora) 
VALUES (1, 
'Alta de $tipo: $nombre',
 'Se ha registrado un nuevo $tipo: $nombre en el destino con ID $id_destino',
 NOW()
 )";        
$conn->query($bitacora);
if ($conn->error) {
    http_response_code(500);
    echo "Error al insertar: " . $conn->error;
    exit;
}

echo json_encode(['id' => $id, 'nombre' => $nombre]);
?>