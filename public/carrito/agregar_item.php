<?php
session_start();
include '../conexion.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit('No autenticado');
}

$id_usuario = $_SESSION['usuario_id'];
$tipo_producto = $_POST['tipo_producto'];
$id_producto = intval($_POST['id_producto']);
$cantidad = intval($_POST['cantidad']);
$incluir_auto = isset($_POST['incluir_auto']) ? (bool)$_POST['incluir_auto'] : false;
$servicios = isset($_POST['servicios']) ? json_decode($_POST['servicios'], true) : [];

if ($cantidad < 1) exit('Cantidad inválida');

// Buscar o crear carrito activo
$res = $conexion->query("SELECT id_carrito FROM carritos WHERE id_usuario=$id_usuario AND estado='activo' ORDER BY id_carrito DESC LIMIT 1");
if ($row = $res->fetch_assoc()) {
    $id_carrito = $row['id_carrito'];
} else {
    $conexion->query("INSERT INTO carritos (id_usuario, estado) VALUES ($id_usuario, 'activo')");
    $id_carrito = $conexion->insert_id;
}

// Solo para paquetes turísticos
if ($tipo_producto === 'paquete_turistico') {
    $prod = $conexion->query("SELECT precio_base, cupo_disponible FROM paquetes_turisticos WHERE id_paquete=$id_producto AND activo=1")->fetch_assoc();
    if (!$prod) exit('Paquete no encontrado');
    if ($cantidad > $prod['cupo_disponible']) exit('No hay suficiente cupo');
    
    $precio_base = $prod['precio_base'];
    $subtotal = $precio_base * $cantidad;

    // Sumar precio de auto si se eligió incluir
    if ($incluir_auto) {
        $res_auto = $conexion->query("SELECT precio_por_dia FROM alquiler_autos alq 
            INNER JOIN paquete_autos pa ON pa.id_alquiler = alq.id_alquiler 
            WHERE pa.id_paquete = $id_producto LIMIT 1");
        if ($row_auto = $res_auto->fetch_assoc()) {
            $subtotal += $row_auto['precio_por_dia'] * $cantidad; // Se asume un día de alquiler
        }
    }

    // Sumar precio de servicios adicionales seleccionados
    if (!empty($servicios)) {
        $servicios_ids = implode(',', array_map('intval', $servicios));
        $res_serv = $conexion->query("SELECT precio FROM servicios_adicionales WHERE id_servicio IN ($servicios_ids)");
        while ($row_serv = $res_serv->fetch_assoc()) {
            $subtotal += $row_serv['precio'] * $cantidad;
        }
    }

} else {
    exit('Tipo de producto no soportado');
}

// Ver si ya está en el carrito
$res = $conexion->query("SELECT id_item, cantidad FROM carrito_items WHERE id_carrito=$id_carrito AND tipo_producto='$tipo_producto' AND id_producto=$id_producto");
if ($row = $res->fetch_assoc()) {
    $nueva_cantidad = $row['cantidad'] + $cantidad;
    $nuevo_subtotal = $precio_base * $nueva_cantidad;

    // Recalcular auto y servicios para la nueva cantidad
    if ($incluir_auto) {
        if (isset($row_auto)) {
            $nuevo_subtotal += $row_auto['precio_por_dia'] * $nueva_cantidad;
        }
    }
    if (!empty($servicios)) {
        $res_serv = $conexion->query("SELECT precio FROM servicios_adicionales WHERE id_servicio IN ($servicios_ids)");
        while ($row_serv = $res_serv->fetch_assoc()) {
            $nuevo_subtotal += $row_serv['precio'] * $nueva_cantidad;
        }
    }

    $conexion->query("UPDATE carrito_items SET cantidad=$nueva_cantidad, subtotal=$nuevo_subtotal WHERE id_item={$row['id_item']}");
} else {
    $conexion->query("INSERT INTO carrito_items 
        (id_carrito, tipo_producto, id_producto, cantidad, precio_unitario, subtotal) 
        VALUES ($id_carrito, '$tipo_producto', $id_producto, $cantidad, $precio_base, $subtotal)");
}

echo 'ok';
?>
