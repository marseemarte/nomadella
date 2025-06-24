-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 23-06-2025 a las 10:07:40
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

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

DROP TABLE IF EXISTS `alojamientos`;
CREATE TABLE IF NOT EXISTS `alojamientos` (
  `id_alojamiento` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `categoria` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `telefono` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `precio_dia` int NOT NULL,
  `email_contacto` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_proveedor` int DEFAULT NULL,
  `id_destino` int NOT NULL,
  PRIMARY KEY (`id_alojamiento`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `fk_aloj_destino` (`id_destino`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alojamientos`
--

INSERT INTO `alojamientos` (`id_alojamiento`, `nombre`, `direccion`, `ciudad`, `categoria`, `descripcion`, `telefono`, `precio_dia`, `email_contacto`, `id_proveedor`, `id_destino`) VALUES
(1, 'Hotel Los Andes', 'Av. Libertador 1234', 'El Calafate', '4 estrellas', 'Confort y vistas a los glaciares.', '2901-123456', 300, 'info@losandes.com', 1, 1),
(2, 'EcoHotel Caribe', 'Calle Sol 432', 'Punta Cana', '5 estrellas', 'Todo incluido con actividades acuáticas.', '809-8765432', 650, 'reservas@ecocaribe.com', 2, 3),
(3, 'Hostal Europa', 'Rue des Fleurs 21', 'París', '3 estrellas', 'Ideal para mochileros.', '+33 1 23456789', 270, 'contact@hostaleuropa.fr', 3, 4),
(8, 'sdasd', NULL, NULL, 'sdasd', NULL, NULL, 32, NULL, 43, 18),
(9, 'Hotel Cuarto', NULL, NULL, '4 estrellas', NULL, NULL, 200, NULL, 44, 7);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alquiler_autos`
--

DROP TABLE IF EXISTS `alquiler_autos`;
CREATE TABLE IF NOT EXISTS `alquiler_autos` (
  `id_alquiler` int NOT NULL AUTO_INCREMENT,
  `proveedor` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_vehiculo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ubicacion_retiro` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_destino` int NOT NULL,
  `ubicacion_entrega` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `precio_por_dia` decimal(10,2) DEFAULT NULL,
  `condiciones` text COLLATE utf8mb4_general_ci,
  `id_proveedor` int DEFAULT NULL,
  PRIMARY KEY (`id_alquiler`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `fk_auto_destino` (`id_destino`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `alquiler_autos`
--

INSERT INTO `alquiler_autos` (`id_alquiler`, `proveedor`, `tipo_vehiculo`, `ubicacion_retiro`, `id_destino`, `ubicacion_entrega`, `precio_por_dia`, `condiciones`, `id_proveedor`) VALUES
(1, 'Hertz', 'SUV', 'Aeropuerto El Calafate', 2, 'Aeropuerto El Calafate', 80.00, 'Seguro básico incluido.', 4),
(2, 'Avis', 'Convertible', 'Aeropuerto Punta Cana', 3, 'Aeropuerto Punta Cana', 100.00, 'GPS y aire acondicionado incluidos.', 5),
(3, 'Sixt', 'Sedán', 'Aeropuerto CDG París', 4, 'Centro de París', 90.00, 'Kilometraje ilimitado.', 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `bitacora_sistema`
--

DROP TABLE IF EXISTS `bitacora_sistema`;
CREATE TABLE IF NOT EXISTS `bitacora_sistema` (
  `id_evento` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `accion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `fecha_hora` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_evento`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `bitacora_sistema`
--

INSERT INTO `bitacora_sistema` (`id_evento`, `id_usuario`, `accion`, `descripcion`, `fecha_hora`) VALUES
(1, 11, 'Inicio de sesión', 'El usuario 11 inició sesión', '2025-06-10 18:25:02'),
(2, 12, 'Compra realizada', 'El usuario 12 realizó una orden', '2025-06-10 18:25:02'),
(3, 13, 'Edición de perfil', 'El usuario 13 actualizó su email', '2025-06-10 18:25:02'),
(84, 1, 'Alta cliente', 'El usuario 1 registró al cliente #15', '2024-07-10 13:15:00'),
(85, 2, 'Baja cliente', 'El usuario 2 desactivó al cliente #12', '2024-07-12 17:25:00'),
(86, 3, 'Modificación reserva', 'El usuario 3 modificó la reserva #21', '2024-07-13 12:45:00'),
(87, 11, 'Alta paquete turístico', 'El usuario 11 publicó el paquete #3', '2024-07-15 19:30:00'),
(88, 12, 'Alta proveedor', 'El usuario 12 registró al proveedor #14', '2024-07-17 15:00:00'),
(89, 13, 'Baja paquete turístico', 'El usuario 13 desactivó el paquete #6', '2024-07-20 21:45:00'),
(90, 14, 'Modificación proveedor', 'El usuario 14 actualizó los datos del proveedor #9', '2024-07-22 11:30:00'),
(91, 15, 'Alta destino', 'El usuario 15 creó el destino #12', '2024-07-23 20:00:00'),
(92, 16, 'Baja destino', 'El usuario 16 eliminó el destino #22', '2024-07-24 14:15:00'),
(93, 17, 'Alta cliente', 'El usuario 17 registró al cliente #10', '2024-07-25 16:30:00'),
(94, 18, 'Alta reserva', 'El usuario 18 creó la reserva #17', '2024-07-26 22:20:00'),
(95, 19, 'Baja cliente', 'El usuario 19 desactivó al cliente #15', '2024-07-28 18:40:00'),
(96, 20, 'Alta paquete turístico', 'El usuario 20 publicó el paquete #19', '2024-07-30 01:10:00'),
(97, 21, 'Alta proveedor', 'El usuario 21 registró al proveedor #4', '2024-07-30 12:50:00'),
(98, 22, 'Modificación reserva', 'El usuario 22 modificó la reserva #7', '2024-08-01 15:25:00'),
(99, 23, 'Alta destino', 'El usuario 23 creó el destino #11', '2024-08-03 19:55:00'),
(100, 24, 'Baja proveedor', 'El usuario 24 eliminó al proveedor #13', '2024-08-05 23:35:00'),
(101, 25, 'Baja cliente', 'El usuario 25 desactivó al cliente #2', '2024-08-07 10:15:00'),
(102, 26, 'Alta reserva', 'El usuario 26 creó la reserva #16', '2024-08-09 21:05:00'),
(103, 44, 'Modificación paquete turístico', 'El usuario 44 actualizó el paquete #18', '2024-08-11 14:45:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carritos`
--

DROP TABLE IF EXISTS `carritos`;
CREATE TABLE IF NOT EXISTS `carritos` (
  `id_carrito` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_carrito`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(20, 20, '2025-06-10 18:17:53', 'activo'),
(26, 43, '2025-06-17 21:30:49', 'cerrado'),
(27, 43, '2025-06-18 00:22:08', 'cerrado'),
(28, 43, '2025-06-23 02:18:21', 'activo'),
(29, 53, '2025-06-23 06:56:15', 'cerrado'),
(30, 53, '2025-06-23 07:00:03', 'cerrado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_items`
--

DROP TABLE IF EXISTS `carrito_items`;
CREATE TABLE IF NOT EXISTS `carrito_items` (
  `id_item` int NOT NULL AUTO_INCREMENT,
  `id_carrito` int DEFAULT NULL,
  `tipo_producto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_producto` int DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `precio_unitario` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id_item`),
  KEY `id_carrito` (`id_carrito`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(33, 28, 'paquete_turistico', 1, 1, 3500.00, 3620.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios_paquetes`
--

DROP TABLE IF EXISTS `comentarios_paquetes`;
CREATE TABLE IF NOT EXISTS `comentarios_paquetes` (
  `id_comentario` int NOT NULL AUTO_INCREMENT,
  `id_paquete` int DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `texto` text COLLATE utf8mb4_general_ci,
  `puntuacion` smallint DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_comentario`),
  KEY `id_paquete` (`id_paquete`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Estructura de tabla para la tabla `destinos`
--

DROP TABLE IF EXISTS `destinos`;
CREATE TABLE IF NOT EXISTS `destinos` (
  `id_destino` int NOT NULL AUTO_INCREMENT,
  `destino` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `estado` varchar(11) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'activo',
  PRIMARY KEY (`id_destino`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `destinos`
--

INSERT INTO `destinos` (`id_destino`, `destino`, `fecha_registro`, `estado`) VALUES
(1, 'Patagonia', '2025-06-22 19:27:45', 'activo'),
(2, 'Europa', '2025-06-21 05:28:06', 'activo'),
(3, 'Punta Cana', '2025-06-21 05:28:06', 'activo'),
(4, 'París', '2025-06-23 04:38:37', 'activo'),
(5, 'Francia', '2025-06-21 05:28:06', 'activo'),
(6, 'Turquia', '2025-06-21 05:28:06', 'activo'),
(7, 'China', '2025-06-21 05:28:06', 'activo'),
(17, 'Santiago', '2025-06-23 04:38:19', 'activo'),
(18, 'Buenos Aires', '2025-06-23 04:42:26', 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etiquetas`
--

DROP TABLE IF EXISTS `etiquetas`;
CREATE TABLE IF NOT EXISTS `etiquetas` (
  `id_etiqueta` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_etiqueta`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etiquetas`
--

INSERT INTO `etiquetas` (`id_etiqueta`, `nombre`) VALUES
(1, 'Familiar'),
(2, 'Todo Incluido'),
(3, 'Aventura'),
(4, 'Exclusivo'),
(5, 'Cultural'),
(6, 'Relax'),
(7, 'Romántico'),
(8, 'Naturaleza'),
(9, 'Crucero'),
(10, 'Nieve'),
(11, 'Playa'),
(12, 'Montaña'),
(13, 'Safari'),
(14, 'Gastronómico'),
(15, 'Económico'),
(16, 'Lujo'),
(17, 'Bienestar'),
(18, 'City Tour'),
(19, 'Fotográfico'),
(20, 'Ecoturismo'),
(21, 'Religioso'),
(22, 'Deportivo'),
(23, 'Estudio'),
(24, 'Aventura Extrema'),
(25, 'Festival'),
(26, 'Business'),
(32, 'Misterioso'),
(33, 'Temático'),
(34, 'Family Friendly');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int NOT NULL,
  `mensaje` text COLLATE utf8mb4_general_ci,
  `tipo` enum('confirmada','pendiente','cancelada') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `leido` tinyint(1) DEFAULT '0',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `id_usuario`, `mensaje`, `tipo`, `leido`, `fecha`) VALUES
(1, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:44:59'),
(2, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:47:26'),
(3, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:48:52'),
(4, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:48:52'),
(5, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:48:52'),
(6, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:48:53'),
(7, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:48:53'),
(8, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:48:55'),
(9, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:48:56'),
(10, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:49:09'),
(11, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:49:12'),
(12, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:50:41'),
(13, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:50:45'),
(14, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 03:50:50'),
(15, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 03:50:53'),
(16, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 03:54:22'),
(17, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 03:54:26'),
(18, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 04:09:30'),
(19, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 04:11:07'),
(20, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 04:17:33'),
(21, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 04:18:04'),
(22, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 04:18:08'),
(23, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 04:20:04'),
(24, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 04:20:06'),
(25, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 04:20:13'),
(26, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 04:22:08'),
(27, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 04:22:11'),
(28, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 04:25:00'),
(29, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 23:21:29'),
(30, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 23:37:24'),
(31, 46, 'La fecha de su reserva ha sido modificada a 2025-06-15. Por favor confirme si acepta el cambio.', '', 1, '2025-06-15 23:37:24'),
(32, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 23:40:05'),
(33, 46, 'La fecha de su reserva ha sido modificada a 2025-06-15. Por favor confirme si acepta el cambio.', '', 1, '2025-06-15 23:40:05'),
(34, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 23:40:30'),
(35, 46, 'La fecha de su reserva ha sido modificada a 2025-06-15. Por favor confirme si acepta el cambio.', '', 1, '2025-06-15 23:40:30'),
(36, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 23:42:43'),
(37, 46, 'La fecha de su reserva ha sido modificada a 2025-06-15. Por favor confirme si acepta el cambio.', '', 1, '2025-06-15 23:42:43'),
(38, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 23:42:47'),
(39, 46, 'La fecha de su reserva ha sido modificada a 2025-06-15. Por favor confirme si acepta el cambio.', '', 1, '2025-06-15 23:42:47'),
(40, 46, 'Su reserva ha sido actualizada al estado Confirmada', 'confirmada', 1, '2025-06-15 23:43:07'),
(41, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 23:43:11'),
(42, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 23:51:36'),
(43, 46, 'Su reserva ha sido actualizada al estado Pendiente', 'pendiente', 1, '2025-06-15 23:51:39'),
(44, 46, 'El estado de su reserva ha cambiado a \'Confirmada\'.', 'confirmada', 1, '2025-06-16 00:03:55'),
(45, 46, 'El estado de su reserva #16 ha cambiado a \'Confirmada\'. Por favor verifique los cambios en <a href=\'public/carrito.php\'>Mis Reservas</a>.', 'confirmada', 1, '2025-06-16 00:14:14'),
(46, 11, 'El estado de su reserva #17 ha cambiado a \'Confirmada\'. Por favor verifique los cambios en Mis Reservas.', 'confirmada', 0, '2025-06-17 05:27:32'),
(47, 43, 'El estado de su reserva #15 ha cambiado a \'Pendiente\'. Por favor verifique los cambios en Mis Reservas.', 'pendiente', 1, '2025-06-17 22:17:07'),
(48, 43, 'El estado de su reserva #19 ha cambiado a \'Confirmada\'. Por favor verifique los cambios en Mis Reservas.', 'confirmada', 1, '2025-06-18 00:31:39'),
(49, 46, 'El estado de su reserva #22 ha cambiado a \'Pendiente\'. Por favor verifique los cambios en Mis Reservas.', 'pendiente', 1, '2025-06-21 20:46:08'),
(50, 46, 'El estado de su reserva #22 ha cambiado a \'Pendiente\'. Por favor verifique los cambios en Mis Reservas.', 'pendiente', 1, '2025-06-21 20:46:17'),
(51, 46, 'Su reserva #22 ha sido cancelada por el administrador.', '', 1, '2025-06-22 19:55:47'),
(52, 43, 'El estado de su reserva #19 ha cambiado a \'Confirmada\'. Por favor verifique los cambios en Mis Reservas.', '', 1, '2025-06-22 22:15:50'),
(53, 43, 'El estado de su reserva #25 ha cambiado a \'Confirmada\'. Por favor verifique los cambios en Mis Reservas.', '', 1, '2025-06-23 05:08:46'),
(54, 43, 'El estado de su reserva #25 ha cambiado a \'Pendiente\'. Por favor verifique los cambios en Mis Reservas.', '', 1, '2025-06-23 05:13:50'),
(55, 43, 'El estado de su reserva #25 ha cambiado a \'Confirmada\'. Por favor verifique los cambios en Mis Reservas.', '', 1, '2025-06-23 05:13:56');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ordenes`
--

DROP TABLE IF EXISTS `ordenes`;
CREATE TABLE IF NOT EXISTS `ordenes` (
  `id_orden` int NOT NULL AUTO_INCREMENT,
  `id_usuario` int DEFAULT NULL,
  `fecha_orden` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `total` decimal(12,2) DEFAULT NULL,
  `estado` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `medio_pago` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `datos_facturacion` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_orden`),
  KEY `id_usuario` (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(10, 20, '2025-06-07 11:30:00', 950.00, 'confirmada', 'Crédito', 'Nicolás Pérez - DNI 45677889 - Calle E'),
(11, 2, '2025-06-13 03:41:13', 23333.00, 'pendiente', 'Mercado Pago', 'Ana López - CUIT 27-23456789-0 - Calle Falsa 456'),
(12, 11, '2025-06-13 09:19:27', 0.00, 'Pendiente', NULL, NULL),
(13, 43, '2025-06-13 21:31:42', 7040.00, 'Confirmada', 'Tarjeta de Crédito', 'Datos de ejemplo'),
(14, 43, '2025-06-13 21:49:39', 10410.00, 'Confirmada', 'Tarjeta de Crédito', 'Datos de ejemplo'),
(15, 43, '2025-06-14 03:00:00', 6900.00, 'Pendiente', 'Tarjeta de Crédito', 'Datos de ejemplo'),
(16, 46, '2025-06-20 03:00:00', 3510.00, 'Confirmada', 'Tarjeta de Crédito', 'Datos de ejemplo'),
(18, 43, '2025-06-17 21:31:26', 3510.00, 'Cancelada', 'Tarjeta de Crédito', 'Nombre: pepe\nDNI/CUIT: 64353655435\nDirección: 50'),
(19, 43, '2025-06-22 22:15:50', 6900.00, 'Confirmada', 'Tarjeta de Débito', 'Nombre: wsdad\r\nDNI/CUIT: 24324324343\r\nDirección: 50'),
(20, 46, '2025-06-23 04:55:32', 3500.00, 'Pendiente', NULL, NULL),
(23, 47, '2025-06-22 19:57:27', 6900.00, 'Pendiente', NULL, NULL),
(24, 2, '2025-06-22 22:26:44', 3510.00, 'Pendiente', NULL, NULL),
(25, 43, '2025-06-23 05:13:56', 3200.00, 'Confirmada', 'Tarjeta de Crédito', 'CUIT: 11-11111111-1'),
(27, 53, '2025-06-23 07:00:03', 7150.00, 'Confirmada', 'Tarjeta de Crédito', 'Nombre: Santiago Fernandez\nDNI/CUIT: 11111111111\nDirección: 50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden_items`
--

DROP TABLE IF EXISTS `orden_items`;
CREATE TABLE IF NOT EXISTS `orden_items` (
  `id_item` int NOT NULL AUTO_INCREMENT,
  `id_orden` int DEFAULT NULL,
  `tipo_producto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_producto` int DEFAULT NULL,
  `cantidad` int DEFAULT NULL,
  `precio_unitario` decimal(12,2) DEFAULT NULL,
  `subtotal` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id_item`),
  KEY `id_orden` (`id_orden`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(13, 10, 'paquete_turistico', 1, 1, 950.00, 950.00),
(14, 12, 'paquete_turistico', 1, 1, NULL, NULL),
(15, 13, 'paquete_turistico', 3, 1, 7040.00, 7040.00),
(16, 14, 'paquete_turistico', 2, 1, 6900.00, 6900.00),
(17, 14, 'paquete_turistico', 1, 1, 3510.00, 3510.00),
(18, 15, 'paquete_turistico', 2, 1, 6900.00, 6900.00),
(19, 16, 'paquete_turistico', 1, 1, 3510.00, 3510.00),
(21, 18, 'paquete_turistico', 1, 1, 3510.00, 3510.00),
(23, 20, 'paquete_turistico', 32, 1, NULL, NULL),
(26, 23, 'paquete_turistico', 2, 1, NULL, NULL),
(27, 19, 'servicio_adicional', 2, 1, NULL, NULL),
(28, 24, 'paquete_turistico', 1, 1, NULL, NULL),
(29, 25, 'paquete_turistico', 1, 1, NULL, NULL),
(32, 25, 'servicio_adicional', 1, 1, NULL, NULL),
(34, 27, 'paquete_turistico', 2, 1, 6900.00, 7150.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes_turisticos`
--

DROP TABLE IF EXISTS `paquetes_turisticos`;
CREATE TABLE IF NOT EXISTS `paquetes_turisticos` (
  `id_paquete` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `id_destino` int DEFAULT NULL,
  `precio_base` decimal(12,2) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `cupo_disponible` int DEFAULT NULL,
  `destino` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo_paquete` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_paquete`),
  KEY `fk_paquete_destino` (`id_destino`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetes_turisticos`
--

INSERT INTO `paquetes_turisticos` (`id_paquete`, `nombre`, `descripcion`, `id_destino`, `precio_base`, `fecha_inicio`, `fecha_fin`, `cupo_disponible`, `destino`, `tipo_paquete`, `activo`) VALUES
(1, 'Aventura en Patagonia', 'Una semana de trekking, glaciares y naturaleza.', 1, 3200.00, '2025-12-09', '2025-12-21', 12, 'Patagonia', 'Aventura', 1),
(2, 'Relax en el Caribe', 'Resort all-inclusive con actividades acuáticas.', 3, 6900.00, '2025-07-17', '2025-07-22', 25, 'Punta Cana', 'Playa', 1),
(3, 'Turismo Cultural en Europa', 'Recorrido por las capitales europeas.', 2, 7040.00, '2025-09-05', '2025-09-20', 19, 'Europa', 'Cultural', 1),
(49, 'Misterio en Europa', 'Embarcate en un viaje temeroso por los escape room de Europa', 2, 4000.00, '2025-10-08', '2025-10-15', 20, 'Europa', 'Misterio\r\n', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_alojamientos`
--

DROP TABLE IF EXISTS `paquete_alojamientos`;
CREATE TABLE IF NOT EXISTS `paquete_alojamientos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_paquete` int DEFAULT NULL,
  `id_alojamiento` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paquete_alojamiento` (`id_paquete`,`id_alojamiento`),
  KEY `id_paquete` (`id_paquete`),
  KEY `id_alojamiento` (`id_alojamiento`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_alojamientos`
--

INSERT INTO `paquete_alojamientos` (`id`, `id_paquete`, `id_alojamiento`) VALUES
(24, 1, 1),
(2, 2, 2),
(4, 5, 3),
(5, 6, 3),
(21, 48, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_autos`
--

DROP TABLE IF EXISTS `paquete_autos`;
CREATE TABLE IF NOT EXISTS `paquete_autos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_paquete` int DEFAULT NULL,
  `id_alquiler` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paquete_auto` (`id_paquete`,`id_alquiler`),
  KEY `id_paquete` (`id_paquete`),
  KEY `id_alquiler` (`id_alquiler`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_autos`
--

INSERT INTO `paquete_autos` (`id`, `id_paquete`, `id_alquiler`) VALUES
(2, 2, 2),
(18, 3, 1),
(3, 3, 3),
(4, 5, 3),
(5, 6, 3),
(19, 49, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_etiquetas`
--

DROP TABLE IF EXISTS `paquete_etiquetas`;
CREATE TABLE IF NOT EXISTS `paquete_etiquetas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_paquete` int NOT NULL,
  `id_etiqueta` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_paquete` (`id_paquete`),
  KEY `id_etiqueta` (`id_etiqueta`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
(20, 3, 22),
(126, 46, 9),
(127, 46, 5),
(128, 46, 4),
(129, 46, 11),
(130, 46, 6),
(131, 46, 21),
(132, 47, 1),
(133, 47, 25),
(134, 47, 14),
(135, 48, 18),
(136, 48, 19),
(137, 48, 2),
(138, 49, 34),
(139, 49, 3),
(140, 49, 32),
(141, 49, 33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_servicios`
--

DROP TABLE IF EXISTS `paquete_servicios`;
CREATE TABLE IF NOT EXISTS `paquete_servicios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_paquete` int DEFAULT NULL,
  `id_servicio` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paquete_servicio` (`id_paquete`,`id_servicio`),
  KEY `id_paquete` (`id_paquete`),
  KEY `id_servicio` (`id_servicio`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_servicios`
--

INSERT INTO `paquete_servicios` (`id`, `id_paquete`, `id_servicio`) VALUES
(20, 1, 1),
(2, 2, 2),
(17, 3, 2),
(3, 3, 3),
(4, 5, 2),
(5, 6, 1),
(16, 48, 1),
(21, 49, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquete_vuelos`
--

DROP TABLE IF EXISTS `paquete_vuelos`;
CREATE TABLE IF NOT EXISTS `paquete_vuelos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_paquete` int DEFAULT NULL,
  `id_vuelo` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_paquete_vuelo` (`id_paquete`,`id_vuelo`),
  KEY `id_paquete` (`id_paquete`),
  KEY `id_vuelo` (`id_vuelo`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquete_vuelos`
--

INSERT INTO `paquete_vuelos` (`id`, `id_paquete`, `id_vuelo`) VALUES
(24, 1, 1),
(2, 2, 2),
(4, 5, 3),
(5, 6, 3),
(21, 48, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `promociones`
--

DROP TABLE IF EXISTS `promociones`;
CREATE TABLE IF NOT EXISTS `promociones` (
  `id_promocion` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `descuento_porcentaje` decimal(5,2) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `activo` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id_promocion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `promociones`
--

INSERT INTO `promociones` (`id_promocion`, `nombre`, `descripcion`, `descuento_porcentaje`, `fecha_inicio`, `fecha_fin`, `activo`) VALUES
(1, 'Promo Verano', '10% en paquetes al Caribe', 10.00, '2025-06-01', '2025-08-31', 1),
(2, 'Europa Week', '15% para reservas en junio', 15.00, '2025-06-01', '2025-06-22', 1),
(3, 'Punta Cana Express', '10% en reservas 3 dias o menos a Punta Cana', 10.00, '2025-06-16', '2025-06-30', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

DROP TABLE IF EXISTS `proveedores`;
CREATE TABLE IF NOT EXISTS `proveedores` (
  `id_proveedor` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` enum('alojamiento','vuelo','auto','servicio') COLLATE utf8mb4_general_ci NOT NULL,
  `contacto` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `direccion` varchar(150) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `id_destino` int DEFAULT NULL,
  `estado` varchar(11) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_proveedor`),
  KEY `fk_prov_destino` (`id_destino`)
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedor`, `nombre`, `tipo`, `contacto`, `telefono`, `email`, `direccion`, `descripcion`, `id_destino`, `estado`, `fecha_registro`) VALUES
(1, 'Los Andes Grupo', 'alojamiento', 'Carlos Mendoza', '2901-111223', 'contacto@losandesgroup.com', 'El Calafate nro 2', 'Operador hotelero en Patagonia', 7, 'activo', '2025-06-21 18:37:44'),
(2, 'Caribe Luxury Resorts', 'alojamiento', 'Lucía Paredes', '809-9998888', 'reservas@caribeluxury.com', 'Punta Cana', 'Resorts 5 estrellas en el Caribe', NULL, 'activo', '2025-06-21 18:37:44'),
(3, 'Europa Hostels SA', 'alojamiento', 'Jean Dupont', '+33 1 55667788', 'contact@europahostels.fr', 'París', 'Hostales económicos europeos', NULL, 'activo', '2025-06-21 18:37:44'),
(4, 'Aerolíneas Argentinas', 'vuelo', 'Oficina Central', '011-12345678', 'info@aerolineas.com', 'Buenos Aires', 'Operador nacional argentino', NULL, 'activo', '2025-06-21 18:37:44'),
(5, 'LATAM Airlines', 'vuelo', 'Central LATAM', '011-87654321', 'info@latam.com', 'Santiago de Chile', 'Conexiones a América Latina', NULL, 'activo', '2025-06-21 18:37:44'),
(6, 'Air France', 'vuelo', 'Central Europa', '+33 1 23456789', 'info@airfrance.fr', 'París', 'Vuelos intercontinentales', NULL, 'activo', '2025-06-21 18:37:44'),
(7, 'Hertz Rent-a-Car', 'auto', 'Soporte Hertz', '0800-123456', 'contacto@hertz.com', 'Aeropuerto El Calafate', 'Alquiler internacional de autos', NULL, 'activo', '2025-06-21 18:37:44'),
(8, 'Avis Rent-a-Car', 'auto', 'Oficina Avis', '0800-987654', 'contacto@avis.com', 'Aeropuerto Punta Cana', 'Flota Premium internacional', NULL, 'activo', '2025-06-21 18:37:44'),
(9, 'Sixt Rent-a-Car', 'auto', 'Soporte Europa', '+33 1 98765432', 'info@sixt.com', 'París', 'Alquiler premium Europa', NULL, 'activo', '2025-06-21 18:37:44'),
(10, 'Glaciares Patagonia Excursiones', 'servicio', 'Jorge Quiroga', '2901-333444', 'info@glaciarespatagonia.com', 'El Calafate', 'Excursiones sobre hielo', NULL, 'activo', '2025-06-21 18:37:44'),
(11, 'Caribe Spa & Wellness', 'servicio', 'Maria López', '809-555555', 'spa@caribewellness.com', 'Punta Cana', 'Masajes y tratamientos de relax', NULL, 'activo', '2025-06-21 18:37:44'),
(12, 'Tour Europa Histórica', 'servicio', 'Giuseppe Moretti', '+33 1 44556677', 'tours@europahistorica.com', 'París', 'Tours guiados por museos y castillos', NULL, 'activo', '2025-06-21 18:37:44'),
(33, 'dfdsf', 'servicio', 'dsfsdf', '2323', 'dfsd@asda', 'sdasd', 'asdad', 4, 'inactivo', '2025-06-21 18:42:01'),
(43, 'sdasd', 'alojamiento', 'asdasd', '32545', 'sddf@nrghrt', 'erger', 'etgerg', 18, 'inactivo', '2025-06-23 05:51:18'),
(44, 'Hotel Cuarto', 'alojamiento', 'Oficina Central', '5555555', 'hotel@cuarto.com', 'Pekin', 'Alojamiento ideal para familias y parejas', 7, 'activo', '2025-06-23 06:04:30');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

DROP TABLE IF EXISTS `rol`;
CREATE TABLE IF NOT EXISTS `rol` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

DROP TABLE IF EXISTS `servicios_adicionales`;
CREATE TABLE IF NOT EXISTS `servicios_adicionales` (
  `id_servicio` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_destino` int NOT NULL,
  `ciudad` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `descripcion` text COLLATE utf8mb4_general_ci,
  `tipo` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `id_proveedor` int DEFAULT NULL,
  PRIMARY KEY (`id_servicio`),
  KEY `id_proveedor` (`id_proveedor`),
  KEY `fk_servicio_destino` (`id_destino`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `servicios_adicionales`
--

INSERT INTO `servicios_adicionales` (`id_servicio`, `nombre`, `id_destino`, `ciudad`, `descripcion`, `tipo`, `precio`, `id_proveedor`) VALUES
(1, 'Excursión glacial', 1, NULL, 'Caminata sobre el glaciar Perito Moreno.', 'Actividad', 120.00, 7),
(2, 'Spa en resort', 2, NULL, 'Masajes y tratamientos de relax.', 'Bienestar', 150.00, 8),
(3, 'Tour histórico', 3, NULL, 'Visita guiada por castillos y museos en Europa.', 'Cultural', 80.00, 9),
(4, 'dfdsf', 4, NULL, NULL, NULL, NULL, 33);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `apellido` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contraseña` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono` int NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `rol` int DEFAULT '4',
  `estado` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'activo',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`),
  KEY `rol` (`rol`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `nombre`, `apellido`, `email`, `contraseña`, `telefono`, `fecha_registro`, `rol`, `estado`) VALUES
(1, 'Super', 'Admin', 'super@admin.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 222444, '2025-06-22 22:17:16', 1, 'activo'),
(2, 'Ana', 'Admin', 'ana@admin.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 565665, '2025-06-21 04:55:07', 2, 'activo'),
(3, 'Luis', 'Admin', 'luis@admin.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-22 22:17:49', 2, 'activo'),
(11, 'Cliente4', 'Apellido', 'cliente4@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-22 20:37:53', 3, 'activo'),
(12, 'Cliente5', 'Apellido', 'cliente5@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-16 03:25:37', 3, 'inactivo'),
(13, 'Cliente6', 'Apellido', 'cliente6@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-16 03:25:37', 3, 'inactivo'),
(14, 'Cliente7', 'Apellido', 'cliente7@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-16 03:25:37', 3, 'inactivo'),
(15, 'Cliente8', 'Apellido', 'cliente8@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-16 03:25:37', 3, 'inactivo'),
(16, 'Cliente9', 'Apellido', 'cliente9@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-16 03:25:37', 3, 'inactivo'),
(17, 'Cliente10', 'Apellido', 'cliente10@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-16 03:25:37', 3, 'inactivo'),
(18, 'Cliente11', 'Apellido', 'cliente11@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-18 00:31:50', 3, 'inactivo'),
(19, 'Cliente12', 'Apellido', 'cliente12@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 22222, '2025-06-23 05:28:31', 3, 'activo'),
(20, 'Cliente13', 'Apellido', 'cliente13@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 2313, '2025-06-22 20:37:22', 3, 'activo'),
(21, 'Cliente14', 'Apellido', 'cliente14@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-16 03:25:37', 3, 'activo'),
(22, 'Cliente15', 'Apellido', 'cliente15@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-23 03:40:17', 3, 'activo'),
(23, 'Cliente16', 'Apellido', 'cliente16@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-23 03:40:17', 3, 'activo'),
(24, 'Cliente17', 'Apellido', 'cliente17@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-23 03:40:17', 3, 'activo'),
(25, 'Cliente18', 'Apellido', 'cliente18@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-23 03:40:17', 3, 'activo'),
(26, 'Cliente19', 'Apellido', 'cliente19@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-23 03:40:17', 3, 'activo'),
(27, 'Cliente20', 'Apellido', 'cliente20@correo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-23 03:40:17', 3, 'activo'),
(43, 'Admin', 'tres', 'admin@tres.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 758496, '2025-06-23 03:42:12', 2, 'activo'),
(44, 'Admin', 'Cuatro', 'admin@cuatro.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 123, '2025-06-23 03:42:48', 2, 'activo'),
(45, 'Cliente21', 'Apellido', 'cliente@ejemplo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 123456, '2025-06-23 03:44:12', 3, 'activo'),
(46, 'Cliente22', 'Apellido', 'Cliente22@ejemplo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 2246558, '2025-06-23 03:44:12', 3, 'activo'),
(47, 'Cliente23', 'Apellido', 'Cliente23@ejemplo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-23 03:44:12', 3, 'activo'),
(48, 'dvdsf', 'sdfsdf', 'sdfsdf@asdfas', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 0, '2025-06-23 03:40:17', 3, 'activo'),
(49, 'ewefwe', 'wefwefwr', 'ewe@ada', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 2323333, '2025-06-23 03:40:17', 3, 'activo'),
(50, 'Juan', 'Ejemplo', 'juan@ejemplo.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 1222333, '2025-06-23 03:40:17', 3, 'activo'),
(51, 'Juan', 'Perez', 'juan@perez.com', '$2y$10$NUFtblMpEALJP.AkUVFUhuLl.myQz2NBlXelf1hbs6L7P/c2E8sgC', 2244113, '2025-06-23 03:40:17', 3, 'activo'),
(52, 'Luciana', 'Pérez', 'luciana@ejemplo.com', '$2y$10$bRpOzPn0as3cMqq5B4.9Wu0QWgga./kisxkdKNPEHV7F3wCT0IxGy', 222222, '2025-06-23 08:19:03', 3, 'activo'),
(53, 'Santiago', 'Fernandez', 'santiago@gmail.com', '$2y$10$8umrSSCjNYxHuU.3xzzKGuyPkx1TiCOLL1mok3qEZ8yZzBqQG0mI.', 0, '2025-06-23 06:48:19', 3, 'activo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `vuelos`
--

DROP TABLE IF EXISTS `vuelos`;
CREATE TABLE IF NOT EXISTS `vuelos` (
  `id_vuelo` int NOT NULL AUTO_INCREMENT,
  `codigo_vuelo` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `aerolinea` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `origen` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `destino` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `id_destino` int NOT NULL,
  `fecha_salida` datetime NOT NULL,
  `fecha_llegada` datetime NOT NULL,
  `precio_base` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`id_vuelo`),
  KEY `fk_vuelo_destino` (`id_destino`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `vuelos`
--

INSERT INTO `vuelos` (`id_vuelo`, `codigo_vuelo`, `aerolinea`, `origen`, `destino`, `id_destino`, `fecha_salida`, `fecha_llegada`, `precio_base`) VALUES
(1, 'AR1234', 'Aerolíneas Argentinas', 'Buenos Aires', 'El Calafate', 1, '2025-12-01 08:00:00', '2025-12-01 11:00:00', 350.00),
(2, 'PC5678', 'LATAM', 'Buenos Aires', 'Punta Cana', 3, '2025-07-15 07:00:00', '2025-07-15 13:30:00', 750.00),
(3, 'AF9876', 'Air France', 'Buenos Aires', 'París', 4, '2025-09-05 18:00:00', '2025-09-06 09:00:00', 1200.00);

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `alojamientos`
--
ALTER TABLE `alojamientos`
  ADD CONSTRAINT `alojamientos_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `fk_aloj_destino` FOREIGN KEY (`id_destino`) REFERENCES `destinos` (`id_destino`);

--
-- Filtros para la tabla `alquiler_autos`
--
ALTER TABLE `alquiler_autos`
  ADD CONSTRAINT `alquiler_autos_ibfk_1` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedor`),
  ADD CONSTRAINT `fk_auto_destino` FOREIGN KEY (`id_destino`) REFERENCES `destinos` (`id_destino`);

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
-- Filtros para la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD CONSTRAINT `notificaciones_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`);

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
-- Filtros para la tabla `paquetes_turisticos`
--
ALTER TABLE `paquetes_turisticos`
  ADD CONSTRAINT `fk_paquete_destino` FOREIGN KEY (`id_destino`) REFERENCES `destinos` (`id_destino`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
