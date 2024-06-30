-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-06-2024 a las 07:36:09
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
-- Base de datos: `tp_comanda`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cargo`
--

CREATE TABLE `cargo` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(30) DEFAULT NULL,
  `idDeSector` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cargo`
--

INSERT INTO `cargo` (`id`, `descripcion`, `idDeSector`) VALUES
(1, 'mozo', 0),
(2, 'cocinero', 1),
(3, 'batender', 2),
(4, 'cervecero', 4),
(5, 'Repostero', 3),
(6, 'director', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `encuesta`
--

CREATE TABLE `encuesta` (
  `id` int(11) NOT NULL,
  `nombreDelCliente` varchar(50) DEFAULT NULL,
  `idDeOrden` int(11) DEFAULT NULL,
  `mensaje` varchar(66) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `encuesta`
--

INSERT INTO `encuesta` (`id`, `nombreDelCliente`, `idDeOrden`, `mensaje`) VALUES
(9, 'Eduardo', 1, 'la mesa estaba sucia'),
(10, 'Messi', 3, 'no me gusto el color de la mesa'),
(11, 'Andrea', 3, 'el color de la mesa me gusto'),
(12, 'Picasso', 1, 'la mesa estaba limpia');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logdeauditoria`
--

CREATE TABLE `logdeauditoria` (
  `id` int(11) NOT NULL,
  `idDeUsuario` int(11) DEFAULT NULL,
  `accion` varchar(50) DEFAULT NULL,
  `fechaDeEntrada` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logdeauditoria`
--

INSERT INTO `logdeauditoria` (`id`, `idDeUsuario`, `accion`, `fechaDeEntrada`) VALUES
(12, 23, 'Listar Pedidos Pendientes', '2024-06-27 11:47:14'),
(13, 23, 'Listar Pedidos Pendientes', '2024-06-27 11:48:56'),
(14, 23, 'Listar Pedidos Pendientes', '2024-06-27 11:49:19'),
(15, 21, 'Creacion De Orden', '2024-06-29 01:52:12'),
(16, 21, 'Agregar Foto a Orden', '2024-06-29 01:54:55'),
(17, 21, 'Agregar Foto a Orden', '2024-06-29 01:55:38'),
(18, 21, 'Agregar Foto a Orden', '2024-06-29 02:07:19'),
(19, 21, 'Creacion De Pedido', '2024-06-29 04:12:06'),
(20, 21, 'Creacion De Pedido', '2024-06-29 04:16:19'),
(21, 21, 'Creacion De Pedido', '2024-06-29 04:20:02'),
(22, 23, 'Listar Pedidos Pendientes', '2024-06-29 04:23:51'),
(23, 23, 'Listar Pedidos Pendientes', '2024-06-29 04:26:49'),
(24, 23, 'Listar Pedidos Pendientes', '2024-06-29 04:27:03'),
(25, 23, 'Listar Pedidos Pendientes', '2024-06-29 04:27:24'),
(26, 23, 'Listar Pedidos Pendientes', '2024-06-29 04:27:57'),
(27, 23, 'Listar Pedidos Pendientes', '2024-06-29 04:28:32'),
(28, 20, 'Listar Pedidos Pendientes', '2024-06-29 04:29:26'),
(29, 22, 'Listar Pedidos Pendientes', '2024-06-29 04:30:11'),
(30, 22, 'Preparar un Pedido', '2024-06-29 04:37:33'),
(31, 20, 'Finalizar Preparacion De Un Pedido', '2024-06-29 04:39:08'),
(32, 21, 'Creacion De Pedido', '2024-06-29 06:38:35'),
(33, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-29 06:41:22'),
(34, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:01:05'),
(35, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:02:08'),
(36, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:02:43'),
(37, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:03:26'),
(38, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:05:59'),
(39, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:06:35'),
(40, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:07:21'),
(41, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:07:54'),
(42, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:08:11'),
(43, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:09:05'),
(44, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:10:02'),
(45, 22, 'Listar Pedidos Pendientes', '2024-06-29 08:21:50'),
(46, 21, 'Listar Pedidos Terminados', '2024-06-29 11:22:31'),
(47, 21, 'Listar Pedidos Terminados', '2024-06-29 11:23:28'),
(48, 21, 'Listar Pedidos Terminados', '2024-06-29 11:24:44'),
(49, 21, 'Listar Pedidos Terminados', '2024-06-29 11:24:54'),
(50, 21, 'Listar Pedidos Terminados', '2024-06-29 11:25:30'),
(51, 21, 'Listar Pedidos Terminados', '2024-06-29 11:26:47'),
(52, 21, 'Listar Pedidos Terminados', '2024-06-29 11:26:51'),
(53, 21, 'Listar Pedidos Terminados', '2024-06-29 11:27:19'),
(54, 21, 'Listar Pedidos Terminados', '2024-06-29 11:27:24'),
(55, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:12:44'),
(56, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:13:59'),
(57, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:15:02'),
(58, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:15:51'),
(59, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:16:12'),
(60, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:17:48'),
(61, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:18:41'),
(62, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:18:57'),
(63, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:19:29'),
(64, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:20:20'),
(65, 21, 'Modificacion De Estado De Mesa a Servir Comida', '2024-06-30 02:26:15');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mesa`
--

CREATE TABLE `mesa` (
  `id` int(11) NOT NULL,
  `codigo` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `mesa`
--

INSERT INTO `mesa` (`id`, `codigo`, `estado`) VALUES
(1, 'eit64', 'cerrada'),
(2, 'sah4o', 'con cliente comiendo'),
(3, 'wp2ab', 'con cliente comiendo'),
(4, '4gcla', 'cerrada'),
(5, 'iyabu', 'con cliente esperando pedido'),
(6, 'prnqk', 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden`
--

CREATE TABLE `orden` (
  `id` int(11) NOT NULL,
  `codigo` varchar(5) DEFAULT NULL,
  `nombreDelCliente` varchar(50) DEFAULT NULL,
  `idDeMesa` int(11) DEFAULT NULL,
  `tiempoInicio` date DEFAULT NULL,
  `tiempoFinal` date DEFAULT NULL,
  `tiempoTotalEstimado` varchar(50) DEFAULT NULL,
  `fechaDeOrden` datetime DEFAULT NULL,
  `rutaDeLaImagen` varchar(50) DEFAULT NULL,
  `nombreDeLaImagen` varchar(50) DEFAULT NULL,
  `costoTotal` float DEFAULT NULL,
  `estado` varchar(50) NOT NULL,
  `estadoDelTiempo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden`
--

INSERT INTO `orden` (`id`, `codigo`, `nombreDelCliente`, `idDeMesa`, `tiempoInicio`, `tiempoFinal`, `tiempoTotalEstimado`, `fechaDeOrden`, `rutaDeLaImagen`, `nombreDeLaImagen`, `costoTotal`, `estado`, `estadoDelTiempo`) VALUES
(1, 'ABC12', 'Pablito', 1, NULL, '2024-06-29', '2024-06-29', '2024-06-01 00:00:00', NULL, NULL, NULL, 'activa', NULL),
(3, '2okgf', 'Manuelito', 1, NULL, NULL, NULL, '2024-06-03 00:00:00', NULL, NULL, 0, 'activa', NULL),
(4, '0gp2p', 'Dieguito', 1, NULL, NULL, NULL, '2024-06-03 00:00:00', 'Imagenes/Mesa/', 'mesa.jpg', 0, 'activa', NULL),
(5, '4fh3f', 'mariano', 1, NULL, NULL, '2024-06-29', '2024-06-22 00:00:00', NULL, NULL, 4500, 'inactiva', NULL),
(6, '4a395', 'julian', 5, NULL, NULL, NULL, '2024-06-23 00:00:00', NULL, NULL, 0, 'activa', NULL),
(7, 'a55h9', 'julian', 1, NULL, NULL, NULL, '2024-06-29 00:00:00', NULL, NULL, 0, 'activa', NULL),
(8, 'omt1h', 'julian', 2, NULL, '2024-06-30', NULL, '2024-06-29 00:00:00', NULL, NULL, 0, 'activa', NULL),
(9, 't4hr2', 'julian', 3, '2024-06-29', '2024-06-30', '01-15-00', '2024-06-29 00:00:00', 'Imagenes/Mesa/', '2024-06-29-00-00-00mesa.jpg', 0, 'activa', 'no cumplido');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `codigo` varchar(60) DEFAULT NULL,
  `idDeOrden` int(11) DEFAULT NULL,
  `idDeProducto` int(11) DEFAULT NULL,
  `idDeEmpleado` int(11) DEFAULT NULL,
  `idDeSector` int(11) DEFAULT NULL,
  `fechaDePedido` date DEFAULT NULL,
  `tiempoEstimado` varchar(50) DEFAULT NULL,
  `tiempoDeInicio` datetime DEFAULT NULL,
  `tiempoDeFinalizacion` datetime DEFAULT NULL,
  `importeTotal` float DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `estadoDelTiempo` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id`, `codigo`, `idDeOrden`, `idDeProducto`, `idDeEmpleado`, `idDeSector`, `fechaDePedido`, `tiempoEstimado`, `tiempoDeInicio`, `tiempoDeFinalizacion`, `importeTotal`, `estado`, `estadoDelTiempo`) VALUES
(20, '676', 1, 10, NULL, 1, '2024-06-12', NULL, NULL, NULL, 8500, NULL, 'indeterminado'),
(21, '390', 1, 14, NULL, 1, '2024-06-12', NULL, NULL, NULL, 8500, NULL, 'indeterminado'),
(22, '915', 4, 12, NULL, 4, '2024-06-21', NULL, NULL, NULL, 8500, NULL, 'indeterminado'),
(23, '139', 4, 12, NULL, 4, '2024-06-21', NULL, NULL, NULL, 8500, NULL, 'indeterminado'),
(24, 'kbppy', 5, 4, 20, 2, '2024-06-22', '0 hours 16 minutes', '2024-06-23 00:54:30', '2024-06-23 01:37:41', 4500, 'listo para servir', 'no cumplido'),
(25, 'sigxv', 1, 4, NULL, 2, '2024-06-23', NULL, NULL, NULL, 4500, 'cancelado', 'indeterminado'),
(26, 'bue8v', 9, 12, 22, 4, '2024-06-29', '1 hours 15 minutes', '2024-06-29 16:37:33', '2024-06-29 16:39:08', 8500, 'listo para servir', 'cumplido'),
(27, 'j5v40', 9, 14, NULL, 1, '2024-06-29', NULL, NULL, NULL, 4500, 'pendiente', 'indeterminado'),
(28, '06eq5', 9, 13, NULL, 2, '2024-06-29', NULL, NULL, NULL, 700, 'pendiente', 'indeterminado'),
(29, 'tmcmh', 9, 14, NULL, 1, '2024-06-29', NULL, NULL, NULL, 4500, 'pendiente', 'indeterminado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `idDeTipo` int(11) DEFAULT NULL,
  `precio` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id`, `nombre`, `idDeTipo`, `precio`) VALUES
(1, 'Vino Uva', 1, 800),
(2, 'milanesa napolitana', 6, 13500),
(4, 'Coca Cola', 3, 4500),
(5, 'Tiramisu', 7, 8500),
(10, 'milanesa a caballo', 6, 8500),
(12, 'corona', 5, 8500),
(13, 'daikiri', 1, 700),
(14, 'hamburguesa de garbanzo', 6, 4500),
(16, 'selva negra', 7, 3500);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puntuacion`
--

CREATE TABLE `puntuacion` (
  `id` int(11) NOT NULL,
  `idDeEncuesta` int(11) DEFAULT NULL,
  `descripcion` varchar(50) DEFAULT NULL,
  `puntuacion` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puntuacion`
--

INSERT INTO `puntuacion` (`id`, `idDeEncuesta`, `descripcion`, `puntuacion`) VALUES
(1, 9, 'Mesa', 2),
(2, 9, 'Restaurante', 6),
(3, 9, 'Cocinero', 9),
(4, 9, 'Mozo', 9),
(5, 10, 'Mesa', 2),
(6, 10, 'Restaurante', 6),
(7, 10, 'Cocinero', 9),
(8, 10, 'Mozo', 9),
(9, 11, 'Mesa', 9),
(10, 11, 'Restaurante', 6),
(11, 11, 'Cocinero', 9),
(12, 11, 'Mozo', 9),
(13, 12, 'Mesa', 9),
(14, 12, 'Restaurante', 6),
(15, 12, 'Cocinero', 9),
(16, 12, 'Mozo', 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id`, `descripcion`) VALUES
(1, 'Empleado'),
(2, 'Socio'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector`
--

CREATE TABLE `sector` (
  `id` int(11) NOT NULL,
  `descripcion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `sector`
--

INSERT INTO `sector` (`id`, `descripcion`) VALUES
(1, 'Cocina'),
(2, 'BarraDeTragos'),
(3, 'CandyBar'),
(4, 'barraDeChoperas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipodeproducto`
--

CREATE TABLE `tipodeproducto` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `idDeSector` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipodeproducto`
--

INSERT INTO `tipodeproducto` (`id`, `nombre`, `idDeSector`) VALUES
(1, 'vino', 2),
(3, 'Bebida', 2),
(5, 'cerveza artesanal', 4),
(6, 'comida', 1),
(7, 'Postre', 3);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `email` varchar(50) DEFAULT NULL,
  `clave` varchar(50) DEFAULT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `apellido` varchar(50) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  `dni` varchar(50) DEFAULT NULL,
  `idDeCargo` int(11) DEFAULT NULL,
  `idDeRol` int(11) DEFAULT NULL,
  `fechaDeRegistro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `email`, `clave`, `nombre`, `apellido`, `estado`, `dni`, `idDeCargo`, `idDeRol`, `fechaDeRegistro`) VALUES
(18, 'julakami@gmail.com', '12345678', 'julio', 'almada', 'activo', '12345678', 6, 2, '2024-06-20 13:36:25'),
(19, 'juanMariano@gmail.com', '12345678', 'juan', 'Damatto', 'suspendido', '66666666', 1, 1, '2024-06-21 01:07:50'),
(20, 'marangonilobi@gmail.com', '12345678', 'kiara', 'lopez', 'activo', '12345678', 3, 1, '2024-06-21 03:13:11'),
(21, 'evaLopez@gmail.com', '12345678', 'eva', 'lopez', 'activo', '12345678', 1, 1, '2024-06-21 03:13:44'),
(22, 'manuel@gmail.com', '12345678', 'juan', 'adorni', 'activo', '12345678', 4, 1, '2024-06-21 03:14:19'),
(23, 'feliperoro@gmail.com', '12345678', 'felipe', 'romario', 'activo', '12345678', 2, 1, '2024-06-21 03:15:20'),
(24, 'gustavocor@gmail.com', '12345678', 'gustavo', 'cordera', 'activo', '12345678', 5, 1, '2024-06-21 03:16:22'),
(25, 'julieta@gmail.com', '121345678', 'mario', 'versaliko', 'activo', '12345678', 2, 1, '2024-06-22 17:37:14'),
(27, 'mariogomez@gmail.com', '12345679', 'mario', 'gomez', 'borrado', '12345678', 6, 2, '2024-06-23 16:19:29');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `logdeauditoria`
--
ALTER TABLE `logdeauditoria`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `mesa`
--
ALTER TABLE `mesa`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `orden`
--
ALTER TABLE `orden`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `pedido`
--
ALTER TABLE `pedido`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `puntuacion`
--
ALTER TABLE `puntuacion`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tipodeproducto`
--
ALTER TABLE `tipodeproducto`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cargo`
--
ALTER TABLE `cargo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `encuesta`
--
ALTER TABLE `encuesta`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT de la tabla `logdeauditoria`
--
ALTER TABLE `logdeauditoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT de la tabla `mesa`
--
ALTER TABLE `mesa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `orden`
--
ALTER TABLE `orden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `puntuacion`
--
ALTER TABLE `puntuacion`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `tipodeproducto`
--
ALTER TABLE `tipodeproducto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
