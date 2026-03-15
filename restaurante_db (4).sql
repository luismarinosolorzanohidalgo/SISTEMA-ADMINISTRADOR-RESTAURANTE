-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-12-2025 a las 19:33:39
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
-- Base de datos: `restaurante_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `clientes`
--

CREATE TABLE `clientes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `sede_id` int(11) NOT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `clientes`
--

INSERT INTO `clientes` (`id`, `nombre`, `correo`, `telefono`, `direccion`, `password`, `sede_id`, `avatar`, `fecha_registro`) VALUES
(3, 'Yamile Laveriano', 'yami@gmail.com', '987654321', 'Jr. Alfonso Ugarte', '$2y$10$3cmPkdTZooGj6oObXW5ZjOi6ifQmg3WpR5R2.olbudYw3UUQLBOqe', 1, 'uploads/avatars/avatar_3_1761746995.jfif', '2025-09-24 04:31:04'),
(4, 'Kany', 'kany@gmail.com', '978456123', 'Calle Arequipa', '$2y$10$1dF9.ZnFkY3ErYROSQtkEe8WRC9YAFfXdAi/4N4PFobtxBPJdoIQa', 1, 'uploads/avatars/avatar_4_1761751362.jfif', '2025-09-24 04:51:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_pedidos`
--

CREATE TABLE `detalle_pedidos` (
  `id` int(11) NOT NULL,
  `pedido_id` int(11) NOT NULL,
  `plato_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `detalle_pedidos`
--

INSERT INTO `detalle_pedidos` (`id`, `pedido_id`, `plato_id`, `cantidad`, `precio_unitario`) VALUES
(1, 26, 2, 1, 10.00),
(2, 27, 13, 1, 7.00),
(3, 28, 13, 1, 7.00),
(4, 29, 13, 1, 7.00),
(5, 30, 13, 1, 7.00),
(6, 31, 2, 1, 10.00),
(7, 32, 2, 1, 10.00),
(8, 33, 12, 1, 10.00),
(9, 33, 2, 1, 10.00),
(10, 33, 13, 1, 7.00),
(11, 33, 14, 1, 20.00),
(12, 33, 22, 1, 17.00),
(13, 33, 21, 6, 20.00),
(14, 33, 19, 5, 15.00),
(15, 33, 6, 45, 40.00),
(16, 34, 12, 2, 10.00),
(17, 35, 6, 1, 40.00),
(18, 35, 11, 1, 6.00),
(19, 35, 10, 1, 5.00),
(20, 36, 13, 1, 7.00),
(21, 37, 13, 1, 7.00),
(22, 37, 2, 1, 10.00),
(23, 39, 2, 1, 10.00),
(24, 41, 15, 2, 5.00),
(25, 41, 38, 2, 7.00),
(26, 42, 37, 1, 5.00),
(27, 42, 4, 1, 8.00);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favoritos`
--

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `id_plato` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedidos`
--

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estado` enum('Pendiente','En preparación','En camino','Entregado','Cancelado','Rechazado') DEFAULT 'Pendiente',
  `tiempo_estimado` int(11) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `tiempo_estimado_minutes` int(11) DEFAULT NULL,
  `tiempo_fin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedidos`
--

INSERT INTO `pedidos` (`id`, `id_cliente`, `total`, `estado`, `tiempo_estimado`, `fecha`, `tiempo_estimado_minutes`, `tiempo_fin`) VALUES
(9, 3, 10.00, 'Cancelado', NULL, '2025-09-24 20:18:32', NULL, NULL),
(10, 3, 30.00, 'Entregado', NULL, '2025-09-29 14:41:33', NULL, NULL),
(11, 3, 7.00, 'Entregado', NULL, '2025-09-29 15:31:59', NULL, NULL),
(12, 3, 10.00, 'Rechazado', NULL, '2025-09-29 15:32:52', NULL, NULL),
(13, 3, 10.00, 'Cancelado', NULL, '2025-09-30 21:17:07', NULL, NULL),
(14, 3, 7.00, 'Rechazado', NULL, '2025-09-30 21:43:22', NULL, NULL),
(15, 3, 7.00, 'Rechazado', NULL, '2025-09-30 21:43:38', NULL, NULL),
(16, 3, 10.00, 'Rechazado', NULL, '2025-09-30 21:44:27', NULL, NULL),
(17, 3, 10.00, 'Cancelado', NULL, '2025-09-30 21:47:24', NULL, NULL),
(18, 3, 10.00, 'Rechazado', NULL, '2025-09-30 21:50:53', NULL, NULL),
(19, 3, 10.00, 'Cancelado', NULL, '2025-10-05 21:28:50', NULL, NULL),
(20, 3, 10.00, 'Cancelado', NULL, '2025-10-05 21:31:01', NULL, NULL),
(21, 3, 7.00, 'Cancelado', NULL, '2025-10-05 21:33:50', NULL, NULL),
(22, 3, 7.00, 'Rechazado', NULL, '2025-10-06 19:46:13', NULL, NULL),
(23, 3, 10.00, 'Entregado', 15, '2025-10-06 20:03:44', 30, '2025-10-09 22:20:24'),
(24, 3, 7.00, 'Rechazado', NULL, '2025-10-07 14:00:29', NULL, NULL),
(25, 3, 20.00, 'Cancelado', NULL, '2025-10-09 14:04:29', 10, '2025-10-09 22:00:20'),
(26, 3, 10.00, 'Pendiente', NULL, '2025-10-17 15:46:54', NULL, NULL),
(27, 3, 7.00, 'Rechazado', NULL, '2025-10-29 14:25:27', NULL, NULL),
(28, 3, 7.00, 'Rechazado', NULL, '2025-10-29 14:26:35', NULL, NULL),
(29, 3, 7.00, 'Rechazado', NULL, '2025-10-29 14:27:01', NULL, NULL),
(30, 3, 7.00, 'Rechazado', NULL, '2025-10-29 14:34:53', NULL, NULL),
(31, 3, 10.00, 'Cancelado', NULL, '2025-10-29 14:35:13', NULL, NULL),
(32, 3, 10.00, 'Rechazado', NULL, '2025-10-29 14:35:25', NULL, NULL),
(33, 3, 2059.00, 'Rechazado', NULL, '2025-11-03 14:23:25', NULL, NULL),
(34, 3, 20.00, 'Rechazado', NULL, '2025-11-05 16:20:31', NULL, NULL),
(35, 3, 51.00, 'En preparación', NULL, '2025-11-05 16:20:57', NULL, NULL),
(36, 3, 7.00, 'Entregado', NULL, '2025-12-01 14:38:54', NULL, NULL),
(37, 3, 17.00, 'Pendiente', NULL, '2025-12-11 15:13:48', NULL, NULL),
(38, 3, 12.00, 'Pendiente', NULL, '2025-12-11 15:37:28', NULL, NULL),
(39, 3, 10.00, 'Rechazado', NULL, '2025-12-15 17:01:14', NULL, NULL),
(40, 3, 10.00, 'Cancelado', NULL, '2025-12-15 17:03:27', NULL, NULL),
(41, 3, 24.00, 'Pendiente', NULL, '2025-12-16 16:20:04', NULL, NULL),
(42, 3, 13.00, 'En preparación', NULL, '2025-12-16 16:23:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `platos`
--

CREATE TABLE `platos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `descripcion` text DEFAULT NULL,
  `precio` decimal(10,2) DEFAULT NULL,
  `categoria` enum('Entrada','Plato Principal','Postre','Bebida','Embutidos') DEFAULT NULL,
  `sede` varchar(100) NOT NULL,
  `estado` enum('Disponible','Agotado') DEFAULT 'Disponible',
  `imagen` varchar(200) DEFAULT NULL,
  `stock` int(11) NOT NULL DEFAULT 0,
  `existencia` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish_ci;

--
-- Volcado de datos para la tabla `platos`
--

INSERT INTO `platos` (`id`, `nombre`, `descripcion`, `precio`, `categoria`, `sede`, `estado`, `imagen`, `stock`, `existencia`) VALUES
(1, 'Lomo Saltado', '', 25.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1761597011_1758663692_1758060871_Lomo-saltado-perudelights.jpg', 22, 0),
(2, 'Causa Rellena', NULL, 10.00, 'Entrada', 'Barranca', 'Disponible', 'uploads/1758663823_1758061708_causa_limena_31268_orig.jpg', 2, 0),
(3, 'Maracuyá Frozen', NULL, 12.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1758664285_1758061683_FROZEN-MARACUYA.png', 3, 0),
(4, 'Torta de Chocolate', NULL, 8.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1758664316_1758061736_r0o8msm3ecpvqsvhve3l.png', 8, 0),
(5, 'PIZZA PERSONAL', NULL, 30.00, 'Embutidos', 'Barranca', 'Disponible', 'uploads/1758664680_aaaa.jpg', 36, 0),
(6, 'SUSHI', NULL, 40.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1758665117_sushi.jpg', 21, 0),
(7, 'COMBO HAMBURGUESA DUO', NULL, 55.00, 'Embutidos', 'Barranca', 'Disponible', 'uploads/1758665191_abcd.jpg', 35, 0),
(8, 'Arroz Chaufa', NULL, 20.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1759157017_arroz-chaufa-peruano-receta.webp', 22, 0),
(9, 'Pisco Sour', NULL, 25.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1759157164_pisco-sour-44f6c3b.jpg', 19, 0),
(10, 'Empanada de Carne', NULL, 5.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1759157308_Empanada-peruana-shutterstock_102114526.webp', 8, 0),
(11, 'Crema volteada', NULL, 6.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1759157508_9df2188d2dca6620686286963604e358.avif', 8, 0),
(12, 'Anticuchos', NULL, 10.00, 'Entrada', 'Barranca', 'Disponible', 'uploads/1759157765_anti.jpg', 8, 0),
(13, 'Ensalada', NULL, 7.00, 'Entrada', 'Barranca', 'Disponible', 'uploads/1759157788_792-h.jpg', 4, 0),
(14, 'Makis', NULL, 20.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1759157897_NTKU3EROOZDVPCWTEQRRWFDV54.avif', 19, 0),
(15, 'Torta tres leches', NULL, 5.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1759157971_Tres-Leches-Cake-S2-500x500.jpg', 10, 0),
(16, 'Machu Picchu', NULL, 20.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1759158272_receta-coctel-de-machu-picchu.jpg', 24, 0),
(17, 'Mojito de Fresa', NULL, 15.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1759158294_mojito-fresa-mucho-hielo-triturado_140725-3011.jpg', 10, 0),
(18, 'Salchipapa', NULL, 12.00, 'Embutidos', 'Barranca', 'Disponible', 'uploads/1759158671_emplatado-final-de-las-salchipapas.jpg', 2, 0),
(19, 'Pollo Broaster', NULL, 15.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1759158699_pollo-a-la-broaster-Paulina-Cocina-Recetas-1722251878.webp', 18, 0),
(20, 'Tequeños', '', 7.00, 'Entrada', 'Barranca', 'Disponible', 'uploads/1761597196_1759158721_tequenos_800x534.webp', 4, 0),
(21, 'Pollo a la plancha', NULL, 20.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1759158751_e3c76dc9cd3e17a7d59953cee700bd29.avif', 13, 0),
(22, 'Pollo a la brasa', '', 17.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1761597214_1759158771_669d57a7e0add.png', 10, 0),
(23, 'Mostrito', NULL, 18.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1759158801_mostrito-600x439.webp', 16, 0),
(24, 'Shawarma', NULL, 12.00, 'Embutidos', 'Barranca', 'Disponible', 'uploads/1759159116_shawa.jpg', 1, 0),
(25, 'Perro caliente', NULL, 8.00, 'Embutidos', 'Barranca', 'Disponible', 'uploads/1759159142_perro.jpg', 2, 0),
(26, 'Nuggets con papas', '', 12.00, 'Embutidos', 'Barranca', 'Disponible', 'uploads/1761597130_1759159182_nugget.jpg', 3, 0),
(27, 'Frappe Clasico', NULL, 11.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1759159381_frapp.jpeg', 2, 0),
(28, 'Piña Colada', '', 15.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1761597465_1759159409_C-jalisco-colada@2x.webp', 22, 0),
(29, 'Tamal Criollo', '', 6.00, 'Entrada', 'Barranca', 'Disponible', 'uploads/1761597151_1759161238_tmañ.jpg', 9, 0),
(30, 'Tiradito de Pescado', '', 15.00, 'Entrada', 'Barranca', 'Disponible', 'uploads/1761597630_tirado.jpg', 19, 0),
(31, 'Ají de Gallina', '', 25.00, 'Plato Principal', 'Barranca', 'Disponible', 'uploads/1761597098_1759161429_aji.jpg', 17, 0),
(32, 'Torta Helada', '', 5.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1761597310_1759161560_torta.png', 2, 0),
(33, 'Keke de Vainilla', '', 3.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1761597492_1759161794_keke.jpg', 9, 0),
(34, 'Chilcano Aperitivo', '', 12.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1761597398_1759161962_aperitivo-chilcano.jpg', 8, 0),
(35, 'Smoothie de Mango', '', 10.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1761597511_1759162075_smou.jpg', 4, 0),
(36, 'Jugos de Frutas', '', 10.00, 'Bebida', 'Barranca', 'Disponible', 'uploads/1761597031_1759162310_jugos.webp', 3, 0),
(37, 'Mazamorra Morada', '', 5.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1761597071_1760019100_mazamorra.jfif', 5, 0),
(38, 'Triple de huevo, jamón y queso', '', 7.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1761597171_1760019463_triple.jfif', 4, 0),
(40, 'Helado en Copa', '', 10.00, 'Postre', 'Barranca', 'Disponible', 'uploads/1761596996_1760019864_Copa-decorada.jpg', 3, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `restaurantes`
--

CREATE TABLE `restaurantes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `ruc` varchar(15) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `direccion` varchar(150) DEFAULT NULL,
  `sede` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `logo` varchar(255) DEFAULT 'img_restaurantes/default.png',
  `estado` enum('Activo','Inactivo') DEFAULT 'Activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp(),
  `disponible` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `restaurantes`
--

INSERT INTO `restaurantes` (`id`, `nombre`, `ruc`, `razon_social`, `direccion`, `sede`, `telefono`, `correo`, `logo`, `estado`, `fecha_registro`, `disponible`) VALUES
(1, 'FastFeast', '20456897451', 'FastFeast SAC', 'La Molina', 'Lima - San Isidro', '987654321', 'fastfeast@gmail.com', '1761917411_fastfeast.png', 'Activo', '2025-10-26 02:42:49', 1),
(2, 'SushiWave', '20678124563', 'SushiWave EIRL', 'Surco ', 'Miraflores', '986543210', 'sushiwave@gmail.com', '1761917366_sushiwave.png', 'Activo', '2025-10-26 02:42:49', 1),
(3, 'El Sabor del Mar', '20965231478', 'Mar y Sol S.A.C.', 'Monterrico', 'Callao', '985123456', 'elsabordelmar@gmail.com', '1761917332_mar.png', 'Activo', '2025-10-26 02:42:49', 1),
(4, 'PastaManía', '20875631412', 'PastaManía SAC', 'San Juan de Lurigancho', 'Surco', '984789654', 'pastamania@gmail.com', '1761917309_pastamania.png', 'Activo', '2025-10-26 02:42:49', 1),
(5, 'Dulce Tentación', '20789456123', 'Dulce Tentación SAC', 'Calle Arequipa', 'Barranco', '982456321', 'dulcetentacion@gmail.com', '1761917282_dulce.png', 'Inactivo', '2025-10-26 02:42:49', 1),
(6, 'Cloud Food', '20789456124', 'Cloud Food SAC', 'San Idelfonso', 'San Miguel', '978654321', 'cloudfood@gmail.com', '1761917396_Logo cloud food en claro.png', 'Activo', '2025-10-27 15:50:22', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sedes`
--

CREATE TABLE `sedes` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sedes`
--

INSERT INTO `sedes` (`id`, `nombre`) VALUES
(1, 'BARRANCA'),
(2, 'PUERTO'),
(3, 'PATIVILCA'),
(4, 'HUACHO');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trabajadores`
--

CREATE TABLE `trabajadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `dni` varchar(20) DEFAULT NULL,
  `fecha_nacimiento` date DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `rol` varchar(50) DEFAULT NULL,
  `sede` varchar(100) NOT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `latitud` decimal(10,8) DEFAULT NULL,
  `longitud` decimal(11,8) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trabajadores`
--

INSERT INTO `trabajadores` (`id`, `nombre`, `correo`, `telefono`, `dni`, `fecha_nacimiento`, `password`, `rol`, `sede`, `direccion`, `latitud`, `longitud`) VALUES
(3, 'Luis', 'luismarino@gmail.com', NULL, NULL, NULL, '$2y$10$VyKQy3swXOXYQR/Z9Bn0fe6grICgiEOSirNbMrMYQjeko79QicgIy', 'Administrador', '1', NULL, NULL, NULL),
(7, 'Yamile Laveriano', 'yami@gmail.com', NULL, NULL, NULL, '$2y$10$cGVBrIbdZBwq/IabnlXe1umau/pVobkrsmfBdZkrK43uK/814s8ea', 'Cajero', '1', NULL, NULL, NULL),
(8, 'RAYMUNDO', 'ray@gmail.com', NULL, NULL, NULL, '$2y$10$C2iBRbEFRrPVKXL6PmgjGeE7HKqvg4JuBDvmV8/IkelpA6sDnDaF.', 'User', '4', NULL, NULL, NULL),
(9, 'Luis', 'josu@gmail.com', NULL, NULL, NULL, '$2y$10$u667FpiKqOhJg3.P4H8UOOUUdYxIp9Rlf6n8aB9Q2qRkjLjrXmeEq', 'Administrador', '1', NULL, NULL, NULL),
(11, 'Leonardo', 'leonardo@gmail.com', '978456123', '61003755', '2007-08-21', '$2y$10$U2EiNcMnUqmU5qNMCOmfVuHbVcGpPGLV1B4hRPe7ZM3l5SbX9.7.a', 'Administrador', 'barranca', 'Monterico', NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `sede_id` (`sede_id`);

--
-- Indices de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pedido_id` (`pedido_id`),
  ADD KEY `plato_id` (`plato_id`);

--
-- Indices de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`),
  ADD KEY `id_plato` (`id_plato`);

--
-- Indices de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_cliente` (`id_cliente`);

--
-- Indices de la tabla `platos`
--
ALTER TABLE `platos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `restaurantes`
--
ALTER TABLE `restaurantes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `ruc` (`ruc`);

--
-- Indices de la tabla `sedes`
--
ALTER TABLE `sedes`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `clientes`
--
ALTER TABLE `clientes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `favoritos`
--
ALTER TABLE `favoritos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `platos`
--
ALTER TABLE `platos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT de la tabla `restaurantes`
--
ALTER TABLE `restaurantes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `sedes`
--
ALTER TABLE `sedes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `trabajadores`
--
ALTER TABLE `trabajadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `clientes`
--
ALTER TABLE `clientes`
  ADD CONSTRAINT `clientes_ibfk_1` FOREIGN KEY (`sede_id`) REFERENCES `sedes` (`id`);

--
-- Filtros para la tabla `detalle_pedidos`
--
ALTER TABLE `detalle_pedidos`
  ADD CONSTRAINT `detalle_pedidos_ibfk_1` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `detalle_pedidos_ibfk_2` FOREIGN KEY (`plato_id`) REFERENCES `platos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `favoritos`
--
ALTER TABLE `favoritos`
  ADD CONSTRAINT `favoritos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`),
  ADD CONSTRAINT `favoritos_ibfk_2` FOREIGN KEY (`id_plato`) REFERENCES `platos` (`id`);

--
-- Filtros para la tabla `pedidos`
--
ALTER TABLE `pedidos`
  ADD CONSTRAINT `pedidos_ibfk_1` FOREIGN KEY (`id_cliente`) REFERENCES `clientes` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
