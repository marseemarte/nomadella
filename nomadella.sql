-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 12-06-2025 a las 03:01:35
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `nomadella`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alojamientos`
--

CREATE TABLE `alojamientos` (
  `id_alojamiento` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `telefono` varchar(50) DEFAULT NULL,
  `email_contacto` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alojamientos`
--

INSERT INTO `alojamientos` (`id_alojamiento`, `nombre`, `direccion`, `ciudad`, `categoria`, `descripcion`, `telefono`, `email_contacto`) VALUES
(1, 'Hotel Los Andes', 'Av. Libertador 1234', 'El Calafate', '4 estrellas', 'Confort y vistas a los glaciares.', '2901-123456', 'info@losandes.com'),
(2, 'EcoHotel Caribe', 'Calle Sol 432', 'Punta Cana', '5 estrellas', 'Todo incluido con actividades acuáticas.', '809-8765432', 'reservas@ecocaribe.com'),
(3, 'Hostal Europa', 'Rue des Fleurs 21', 'París', '3 estrellas', 'Ideal para mochileros.', '+33 1 23456789', 'contact@hostaleuropa.fr');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquiler_autos`
--

CREATE TABLE `alquiler_autos` (
  `id_alquiler` int(11) NOT NULL,
  `proveedor` varchar(150) DEFAULT NULL,
  `tipo_vehiculo` varchar(100) DEFAULT NULL,
  `ubicacion_retiro` varchar(150) DEFAULT NULL,
  `ubicacion_entrega` varchar(150) DEFAULT NULL,
  `precio_por_dia` decimal(10,2) DEFAULT NULL,
  `condiciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alquiler_autos`
--

INSERT INTO `alquiler_autos` (`id_alquiler`, `proveedor`, `tipo_vehiculo`, `ubicacion_retiro`, `ubicacion_entrega`, `precio_por_dia`, `condiciones`) VALUES
(1, 'Hertz', 'SUV', 'Aeropuerto El Calafate', 'Aeropuerto El Calafate', 80.00, 'Seguro básico incluido.'),
(2, 'Avis', 'Convertible', 'Aeropuerto Punta Cana', 'Aeropuerto Punta Cana', 100.00, 'GPS y aire acondicionado incluidos.'),
(3, 'Sixt', 'Sedán', 'Aeropuerto CDG París', 'Centro de París', 90.00, 'Kilometraje ilimitado.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_sistema`
--

CREATE TABLE `bitacora_sistema` (
  `id_evento` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `accion` varchar(200) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha_hora` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora_sistema`
--

INSERT INTO `bitacora_sistema` (`id_evento`, `id_usuario`, `accion`, `descripcion`, `fecha_hora`) VALUES
(1, 11, 'Inicio de sesión', 'El usuario 11 inició sesión', '2025-06-10 18:25:02'),
(2, 12, 'Compra realizada', 'El usuario 12 realizó una orden', '2025-06-10 18:25:02'),
(3, 13, 'Edición de perfil', 'El usuario 13 actualizó su email', '2025-06-10 18:25:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos`
--

CREATE TABLE `carritos` (
  `id_carrito` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `estado` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carritos`
--

INSERT INTO `carritos` (`id_carrito`, `id_usuario`, `fecha_creacion`, `estado`) VALUES
(1, 11, '2025-06-10 18:15:29', 'activo'),
(2, 12, '2025-06-10 18:15:29', 'activo'),
(3, 13, '2025-06-10 18:15:29', 'activo'),
(4, 14, '2025-06-10 18:15:29', 'activo'),
(5, 15, '2025-06-10 18:15:29', 'activo'),
(6, 16, '2025-06-10 18:15:29', 'activo'),
(7, 17, '2025-06-10 18:15:29', 'activo'),
(8, 18, '2025-06-10 18:15:29', 'activo'),
(9, 19, '2025-06-10 18:15:29', 'activo'),
(10, 20, '2025-06-10 18:15:29', 'activo'),
(11, 11, '2025-06-10 18:17:53', 'activo'),
(12, 12, '2025-06-10 18:17:53', 'activo'),
(13, 13, '2025-06-10 18:17:53', 'activo'),
(14, 14, '2025-06-10 18:17:53', 'activo'),
(15, 15, '2025-06-10 18:17:53', 'activo'),
(16, 16, '2025-06-10 18:17:53', 'activo'),
(17, 17, '2025-06-10 18:17:53', 'activo'),
(18, 18, '2025-06-10 18:17:53', 'activo'),
(19, 19, '2025-06-10 18:17:53', 'activo'),
(20, 20, '2025-06-10 18:17:53', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_items`
--

CREATE TABLE `carrito_items` (
  `id_item` int(11) NOT NULL,
  `id_carrito` int(11) DEFAULT NULL,
  `tipo_producto` varchar(50) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito_items`
--

INSERT INTO `carrito_items` (`id_item`, `id_carrito`, `tipo_producto`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 3, 'paquete_turistico', 1, 1, 950.00, 950.00),
(2, 3, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(3, 4, 'paquete_turistico', 3, 2, 1850.00, 3700.00),
(4, 4, 'paquete_turistico', 1, 1, 950.00, 950.00),
(5, 4, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(6, 5, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(7, 5, 'paquete_turistico', 3, 1, 1850.00, 1850.00),
(8, 6, 'paquete_turistico', 1, 2, 950.00, 1900.00),
(9, 6, 'paquete_turistico', 3, 1, 1850.00, 1850.00),
(10, 7, 'paquete_turistico', 1, 1, 950.00, 950.00),
(11, 7, 'paquete_turistico', 2, 2, 1200.00, 2400.00),
(12, 8, 'paquete_turistico', 3, 1, 1850.00, 1850.00),
(13, 8, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(14, 9, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(15, 9, 'paquete_turistico', 1, 1, 950.00, 950.00),
(16, 9, 'paquete_turistico', 3, 1, 1850.00, 1850.00),
(17, 10, 'paquete_turistico', 3, 2, 1850.00, 3700.00),
(18, 10, 'paquete_turistico', 1, 1, 950.00, 950.00),
(19, 11, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(20, 11, 'paquete_turistico', 3, 1, 1850.00, 1850.00),
(21, 12, 'paquete_turistico', 1, 1, 950.00, 950.00),
(22, 12, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(23, 12, 'paquete_turistico', 3, 1, 1850.00, 1850.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios_paquetes`
--

CREATE TABLE `comentarios_paquetes` (
  `id_comentario` int(11) NOT NULL,
  `id_paquete` int(11) DEFAULT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `texto` text DEFAULT NULL,
  `puntuacion` smallint(6) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comentarios_paquetes`
--

INSERT INTO `comentarios_paquetes` (`id_comentario`, `id_paquete`, `id_usuario`, `texto`, `puntuacion`, `fecha`) VALUES
(1, 1, 1, 'Increíble experiencia, los paisajes fueron espectaculares.', 5, '2025-06-10 18:09:22'),
(2, 2, 2, 'Muy buen servicio, aunque el clima no ayudó mucho.', 4, '2025-06-10 18:09:22'),
(3, 3, 1, 'Europa es mágica, pero el tour fue muy apretado.', 3, '2025-06-10 18:09:22'),
(4, 1, 11, 'Una experiencia inolvidable. Repetiría sin dudar.', 5, '2025-06-10 18:25:02'),
(5, 2, 12, 'Excelente atención y servicios. Muy recomendable.', 4, '2025-06-10 18:25:02'),
(6, 3, 13, 'Europa es maravillosa, pero el tour fue apurado.', 3, '2025-06-10 18:25:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

CREATE TABLE `etiquetas` (
  `id_etiqueta` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etiquetas`
--

INSERT INTO `etiquetas` (`id_etiqueta`, `nombre`, `descripcion`) VALUES
(1, 'Familiar', 'Ideal para familias con niños.'),
(2, 'Todo Incluido', 'Alojamiento, comidas y actividades incluidas.'),
(3, 'Aventura', 'Actividades extremas y deportes al aire libre.'),
(4, 'Exclusivo', 'Experiencia premium para pocos pasajeros.'),
(5, 'Cultural', 'Enfocado en cultura, historia y arte.'),
(6, 'Relax', 'Enfoque en descanso, spa y bienestar.'),
(7, 'Romántico', 'Escapadas en pareja con servicios especiales.'),
(8, 'Naturaleza', 'Contacto directo con entornos naturales.'),
(9, 'Crucero', 'Viaje marítimo de varios días.'),
(10, 'Nieve', 'Destinos de ski y deportes de invierno.'),
(11, 'Playa', 'Sol, arena y mar como protagonistas.'),
(12, 'Montaña', 'Excursiones en zonas de alta montaña.'),
(13, 'Safari', 'Observación de fauna salvaje en hábitat natural.'),
(14, 'Gastronómico', 'Experiencias culinarias y degustaciones.'),
(15, 'Económico', 'Alternativas accesibles para todos los presupuestos.'),
(16, 'Lujo', 'Alta gama de servicios y hospedajes premium.'),
(17, 'Bienestar', 'Programas de spa, yoga y relajación integral.'),
(18, 'City Tour', 'Recorridos por las principales ciudades del mundo.'),
(19, 'Fotográfico', 'Diseñado para capturar las mejores fotos.'),
(20, 'Ecoturismo', 'Respetuoso con el medio ambiente y sostenible.'),
(21, 'Religioso', 'Visitas a lugares de interés espiritual.'),
(22, 'Deportivo', 'Eventos y prácticas de deportes en vivo.'),
(23, 'Estudio', 'Programas de idiomas, talleres o universidades.'),
(24, 'Aventura Extrema', 'Rafting, escalada, paracaidismo, etc.'),
(25, 'Festival', 'Paquetes orientados a eventos culturales y musicales.'),
(26, 'Business', 'Viajes corporativos, conferencias y networking.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

CREATE TABLE `ordenes` (
  `id_orden` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `fecha_orden` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `total` decimal(12,2) DEFAULT NULL,
  `estado` varchar(20) DEFAULT NULL,
  `medio_pago` varchar(100) DEFAULT NULL,
  `datos_facturacion` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ordenes`
--

INSERT INTO `ordenes` (`id_orden`, `id_usuario`, `fecha_orden`, `total`, `estado`, `medio_pago`, `datos_facturacion`) VALUES
(1, 1, '2025-06-10 18:09:22', 1900.00, 'Confirmada', 'Tarjeta de Crédito', 'Juan Pérez - DNI 12345678 - Av. Siempre Viva 123'),
(2, 2, '2025-06-11 03:58:44', 1200.00, 'Cancelada', 'Transferencia Bancaria', 'Ana López - CUIT 27-23456789-0 - Calle Falsa 456'),
(4, 14, '2025-06-01 13:00:00', 1200.00, 'confirmada', 'Tarjeta', 'Pedro Díaz - DNI 30123456 - Calle Falsa 123'),
(5, 15, '2025-06-02 15:30:00', 2400.00, 'confirmada', 'Transferencia', 'Lucía Torres - DNI 30234567 - Av. Siempre Viva 742'),
(6, 16, '2025-06-03 12:15:00', 1850.00, 'pendiente', 'Mercado Pago', 'Matías Rojas - CUIT 27-11223344-5 - Calle B'),
(7, 17, '2025-06-04 17:45:00', 1900.00, 'confirmada', 'Tarjeta', 'Valeria Silva - DNI 33445566 - Calle A'),
(8, 18, '2025-06-05 14:20:00', 1200.00, 'cancelada', 'Efectivo', 'Diego Fernández - DNI 34566778 - Calle C'),
(9, 19, '2025-06-11 18:10:59', 3700.00, 'Cancelada', 'Transferencia', 'Julia Herrera - CUIT 20-44556677-1 - Calle D'),
(10, 20, '2025-06-07 11:30:00', 950.00, 'confirmada', 'Crédito', 'Nicolás Pérez - DNI 45677889 - Calle E');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_items`
--

CREATE TABLE `orden_items` (
  `id_item` int(11) NOT NULL,
  `id_orden` int(11) DEFAULT NULL,
  `tipo_producto` varchar(50) DEFAULT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `precio_unitario` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden_items`
--

INSERT INTO `orden_items` (`id_item`, `id_orden`, `tipo_producto`, `id_producto`, `cantidad`, `precio_unitario`, `subtotal`) VALUES
(1, 1, 'paquete_turistico', 1, 2, 950.00, 1900.00),
(2, 2, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(5, 4, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(6, 5, 'paquete_turistico', 2, 2, 1200.00, 2400.00),
(7, 6, 'paquete_turistico', 3, 1, 1850.00, 1850.00),
(8, 7, 'paquete_turistico', 1, 2, 950.00, 1900.00),
(9, 8, 'paquete_turistico', 2, 1, 1200.00, 1200.00),
(10, 9, 'paquete_turistico', 3, 1, 1850.00, 1850.00),
(11, 9, 'paquete_turistico', 1, 1, 950.00, 950.00),
(12, 9, 'paquete_turistico', 2, 1, 900.00, 900.00),
(13, 10, 'paquete_turistico', 1, 1, 950.00, 950.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes_turisticos`
--

CREATE TABLE `paquetes_turisticos` (
  `id_paquete` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio_base` decimal(12,2) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `cupo_disponible` int(11) DEFAULT NULL,
  `destino` varchar(150) DEFAULT NULL,
  `tipo_paquete` varchar(100) DEFAULT NULL,
  `imagen_destacada` varchar(200) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetes_turisticos`
--

INSERT INTO `paquetes_turisticos` (`id_paquete`, `nombre`, `descripcion`, `precio_base`, `fecha_inicio`, `fecha_fin`, `cupo_disponible`, `destino`, `tipo_paquete`, `imagen_destacada`, `activo`) VALUES
(1, 'Aventura en Patagonia', 'Una semana de trekking, glaciares y naturaleza.', 950.00, '2025-12-01', '2025-12-08', 15, 'Patagonia', 'Aventura', 'patagonia.jpg', 1),
(2, 'Relax en el Caribe', 'Resort all-inclusive con actividades acuáticas.', 1200.00, '2025-07-15', '2025-07-22', 30, 'Punta Cana', 'Playa', 'caribe.jpg', 1),
(3, 'Turismo Cultural en Europa', 'Recorrido por las capitales europeas.', 1850.00, '2025-09-05', '2025-09-20', 20, 'Europa', 'Cultural', 'europa.jpg', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_alojamientos`
--

CREATE TABLE `paquete_alojamientos` (
  `id` int(11) NOT NULL,
  `id_paquete` int(11) DEFAULT NULL,
  `id_alojamiento` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_alojamientos`
--

INSERT INTO `paquete_alojamientos` (`id`, `id_paquete`, `id_alojamiento`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_autos`
--

CREATE TABLE `paquete_autos` (
  `id` int(11) NOT NULL,
  `id_paquete` int(11) DEFAULT NULL,
  `id_alquiler` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_autos`
--

INSERT INTO `paquete_autos` (`id`, `id_paquete`, `id_alquiler`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_etiquetas`
--

CREATE TABLE `paquete_etiquetas` (
  `id` int(11) NOT NULL,
  `id_paquete` int(11) NOT NULL,
  `id_etiqueta` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_etiquetas`
--

INSERT INTO `paquete_etiquetas` (`id`, `id_paquete`, `id_etiqueta`) VALUES
(1, 1, 3),
(2, 1, 4),
(3, 1, 2),
(4, 1, 8),
(5, 1, 10),
(6, 1, 24),
(7, 2, 1),
(8, 2, 2),
(9, 2, 6),
(10, 2, 7),
(11, 2, 11),
(12, 2, 16),
(13, 2, 14),
(14, 2, 17),
(15, 3, 5),
(16, 3, 18),
(17, 3, 13),
(18, 3, 19),
(19, 3, 20),
(20, 3, 22);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_servicios`
--

CREATE TABLE `paquete_servicios` (
  `id` int(11) NOT NULL,
  `id_paquete` int(11) DEFAULT NULL,
  `id_servicio` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_servicios`
--

INSERT INTO `paquete_servicios` (`id`, `id_paquete`, `id_servicio`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_vuelos`
--

CREATE TABLE `paquete_vuelos` (
  `id` int(11) NOT NULL,
  `id_paquete` int(11) DEFAULT NULL,
  `id_vuelo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_vuelos`
--

INSERT INTO `paquete_vuelos` (`id`, `id_paquete`, `id_vuelo`) VALUES
(1, 1, 1),
(2, 2, 2),
(3, 3, 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

CREATE TABLE `promociones` (
  `id_promocion` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `descuento_porcentaje` decimal(5,2) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `promociones`
--

INSERT INTO `promociones` (`id_promocion`, `nombre`, `descripcion`, `descuento_porcentaje`, `fecha_inicio`, `fecha_fin`, `activo`) VALUES
(1, 'Promo Verano', '10% en paquetes al Caribe', 10.00, '2025-06-01', '2025-08-31', 1),
(2, 'Europa Week', '15% para reservas en junio', 15.00, '2025-06-01', '2025-06-15', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `nombre_rol` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `nombre_rol`) VALUES
(1, 'superadministrador'),
(2, 'administrador'),
(3, 'cliente');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios_adicionales`
--

CREATE TABLE `servicios_adicionales` (
  `id_servicio` int(11) NOT NULL,
  `nombre` varchar(200) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` varchar(100) DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios_adicionales`
--

INSERT INTO `servicios_adicionales` (`id_servicio`, `nombre`, `descripcion`, `tipo`, `precio`) VALUES
(1, 'Excursión glacial', 'Caminata sobre el glaciar Perito Moreno.', 'Actividad', 120.00),
(2, 'Spa en resort', 'Masajes y tratamientos de relax.', 'Bienestar', 150.00),
(3, 'Tour histórico', 'Visita guiada por castillos y museos en Europa.', 'Cultural', 80.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `apellido` varchar(100) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `contraseña` varchar(255) DEFAULT NULL,
  `telefono` int(30) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `rol` int(11) DEFAULT 4,
  `estado` varchar(20) DEFAULT 'activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `email`, `contraseña`, `telefono`, `fecha_registro`, `rol`, `estado`) VALUES
(1, 'Super', 'Admin', 'super@admin.com', '1234', 0, '2025-06-10 18:30:45', 1, 'activo'),
(2, 'Ana', 'Admin', 'ana@admin.com', '1234', 0, '2025-06-10 18:30:45', 2, 'activo'),
(3, 'Luis', 'Admin', 'luis@admin.com', '1234', 0, '2025-06-10 18:30:45', 2, 'activo'),
(11, 'Cliente4', 'Apellido', 'cliente4@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(12, 'Cliente5', 'Apellido', 'cliente5@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(13, 'Cliente6', 'Apellido', 'cliente6@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(14, 'Cliente7', 'Apellido', 'cliente7@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(15, 'Cliente8', 'Apellido', 'cliente8@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(16, 'Cliente9', 'Apellido', 'cliente9@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(17, 'Cliente10', 'Apellido', 'cliente10@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(18, 'Cliente11', 'Apellido', 'cliente11@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(19, 'Cliente12', 'Apellido', 'cliente12@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(20, 'Cliente13', 'Apellido', 'cliente13@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(21, 'Cliente14', 'Apellido', 'cliente14@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(22, 'Cliente15', 'Apellido', 'cliente15@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(23, 'Cliente16', 'Apellido', 'cliente16@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(24, 'Cliente17', 'Apellido', 'cliente17@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(25, 'Cliente18', 'Apellido', 'cliente18@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(26, 'Cliente19', 'Apellido', 'cliente19@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo'),
(27, 'Cliente20', 'Apellido', 'cliente20@correo.com', '1234', 0, '2025-06-10 18:30:45', 3, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelos`
--

CREATE TABLE `vuelos` (
  `id_vuelo` int(11) NOT NULL,
  `codigo_vuelo` varchar(50) DEFAULT NULL,
  `aerolinea` varchar(100) DEFAULT NULL,
  `origen` varchar(100) DEFAULT NULL,
  `destino` varchar(100) DEFAULT NULL,
  `fecha_salida` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `fecha_llegada` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `precio_base` decimal(12,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vuelos`
--

INSERT INTO `vuelos` (`id_vuelo`, `codigo_vuelo`, `aerolinea`, `origen`, `destino`, `fecha_salida`, `fecha_llegada`, `precio_base`) VALUES
(1, 'AR1234', 'Aerolíneas Argentinas', 'Buenos Aires', 'El Calafate', '2025-12-01 11:00:00', '2025-12-01 14:00:00', 350.00),
(2, 'PC5678', 'LATAM', 'Buenos Aires', 'Punta Cana', '2025-07-15 10:00:00', '2025-07-15 16:30:00', 750.00),
(3, 'AF9876', 'Air France', 'Buenos Aires', 'París', '2025-09-05 21:00:00', '2025-09-06 12:00:00', 1200.00);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alojamientos`
--
ALTER TABLE `alojamientos`
  ADD PRIMARY KEY (`id_alojamiento`);

--
-- Indices de la tabla `alquiler_autos`
--
ALTER TABLE `alquiler_autos`
  ADD PRIMARY KEY (`id_alquiler`);

--
-- Indices de la tabla `bitacora_sistema`
--
ALTER TABLE `bitacora_sistema`
  ADD PRIMARY KEY (`id_evento`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD PRIMARY KEY (`id_carrito`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_carrito` (`id_carrito`);

--
-- Indices de la tabla `comentarios_paquetes`
--
ALTER TABLE `comentarios_paquetes`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  ADD PRIMARY KEY (`id_etiqueta`);

--
-- Indices de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD PRIMARY KEY (`id_orden`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `orden_items`
--
ALTER TABLE `orden_items`
  ADD PRIMARY KEY (`id_item`),
  ADD KEY `id_orden` (`id_orden`);

--
-- Indices de la tabla `paquetes_turisticos`
--
ALTER TABLE `paquetes_turisticos`
  ADD PRIMARY KEY (`id_paquete`);

--
-- Indices de la tabla `paquete_alojamientos`
--
ALTER TABLE `paquete_alojamientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_alojamiento` (`id_alojamiento`);

--
-- Indices de la tabla `paquete_autos`
--
ALTER TABLE `paquete_autos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_alquiler` (`id_alquiler`);

--
-- Indices de la tabla `paquete_etiquetas`
--
ALTER TABLE `paquete_etiquetas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_etiqueta` (`id_etiqueta`);

--
-- Indices de la tabla `paquete_servicios`
--
ALTER TABLE `paquete_servicios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `paquete_vuelos`
--
ALTER TABLE `paquete_vuelos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_paquete` (`id_paquete`),
  ADD KEY `id_vuelo` (`id_vuelo`);

--
-- Indices de la tabla `promociones`
--
ALTER TABLE `promociones`
  ADD PRIMARY KEY (`id_promocion`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `servicios_adicionales`
--
ALTER TABLE `servicios_adicionales`
  ADD PRIMARY KEY (`id_servicio`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `rol` (`rol`);

--
-- Indices de la tabla `vuelos`
--
ALTER TABLE `vuelos`
  ADD PRIMARY KEY (`id_vuelo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alojamientos`
--
ALTER TABLE `alojamientos`
  MODIFY `id_alojamiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `alquiler_autos`
--
ALTER TABLE `alquiler_autos`
  MODIFY `id_alquiler` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `bitacora_sistema`
--
ALTER TABLE `bitacora_sistema`
  MODIFY `id_evento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `carritos`
--
ALTER TABLE `carritos`
  MODIFY `id_carrito` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `comentarios_paquetes`
--
ALTER TABLE `comentarios_paquetes`
  MODIFY `id_comentario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `etiquetas`
--
ALTER TABLE `etiquetas`
  MODIFY `id_etiqueta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `ordenes`
--
ALTER TABLE `ordenes`
  MODIFY `id_orden` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `orden_items`
--
ALTER TABLE `orden_items`
  MODIFY `id_item` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `paquetes_turisticos`
--
ALTER TABLE `paquetes_turisticos`
  MODIFY `id_paquete` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paquete_alojamientos`
--
ALTER TABLE `paquete_alojamientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paquete_autos`
--
ALTER TABLE `paquete_autos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paquete_etiquetas`
--
ALTER TABLE `paquete_etiquetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `paquete_servicios`
--
ALTER TABLE `paquete_servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `paquete_vuelos`
--
ALTER TABLE `paquete_vuelos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `promociones`
--
ALTER TABLE `promociones`
  MODIFY `id_promocion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `servicios_adicionales`
--
ALTER TABLE `servicios_adicionales`
  MODIFY `id_servicio` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `vuelos`
--
ALTER TABLE `vuelos`
  MODIFY `id_vuelo` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `bitacora_sistema`
--
ALTER TABLE `bitacora_sistema`
  ADD CONSTRAINT `bitacora_sistema_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `carritos`
--
ALTER TABLE `carritos`
  ADD CONSTRAINT `carritos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `carrito_items`
--
ALTER TABLE `carrito_items`
  ADD CONSTRAINT `carrito_items_ibfk_1` FOREIGN KEY (`id_carrito`) REFERENCES `carritos` (`id_carrito`);

--
-- Filtros para la tabla `comentarios_paquetes`
--
ALTER TABLE `comentarios_paquetes`
  ADD CONSTRAINT `comentarios_paquetes_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes_turisticos` (`id_paquete`),
  ADD CONSTRAINT `comentarios_paquetes_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `ordenes`
--
ALTER TABLE `ordenes`
  ADD CONSTRAINT `ordenes_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

--
-- Filtros para la tabla `orden_items`
--
ALTER TABLE `orden_items`
  ADD CONSTRAINT `orden_items_ibfk_1` FOREIGN KEY (`id_orden`) REFERENCES `ordenes` (`id_orden`);

--
-- Filtros para la tabla `paquete_alojamientos`
--
ALTER TABLE `paquete_alojamientos`
  ADD CONSTRAINT `paquete_alojamientos_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes_turisticos` (`id_paquete`),
  ADD CONSTRAINT `paquete_alojamientos_ibfk_2` FOREIGN KEY (`id_alojamiento`) REFERENCES `alojamientos` (`id_alojamiento`);

--
-- Filtros para la tabla `paquete_autos`
--
ALTER TABLE `paquete_autos`
  ADD CONSTRAINT `paquete_autos_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes_turisticos` (`id_paquete`),
  ADD CONSTRAINT `paquete_autos_ibfk_2` FOREIGN KEY (`id_alquiler`) REFERENCES `alquiler_autos` (`id_alquiler`);

--
-- Filtros para la tabla `paquete_etiquetas`
--
ALTER TABLE `paquete_etiquetas`
  ADD CONSTRAINT `paquete_etiquetas_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes_turisticos` (`id_paquete`),
  ADD CONSTRAINT `paquete_etiquetas_ibfk_2` FOREIGN KEY (`id_etiqueta`) REFERENCES `etiquetas` (`id_etiqueta`);

--
-- Filtros para la tabla `paquete_servicios`
--
ALTER TABLE `paquete_servicios`
  ADD CONSTRAINT `paquete_servicios_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes_turisticos` (`id_paquete`),
  ADD CONSTRAINT `paquete_servicios_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicios_adicionales` (`id_servicio`);

--
-- Filtros para la tabla `paquete_vuelos`
--
ALTER TABLE `paquete_vuelos`
  ADD CONSTRAINT `paquete_vuelos_ibfk_1` FOREIGN KEY (`id_paquete`) REFERENCES `paquetes_turisticos` (`id_paquete`),
  ADD CONSTRAINT `paquete_vuelos_ibfk_2` FOREIGN KEY (`id_vuelo`) REFERENCES `vuelos` (`id_vuelo`);

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `rol` FOREIGN KEY (`rol`) REFERENCES `rol` (`id_rol`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
