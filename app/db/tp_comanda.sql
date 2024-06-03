-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-06-2024 a las 23:51:47
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
(1, 'mozo', 6),
(2, 'cocinero', 1),
(3, 'batender', 2),
(4, 'cervecero', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id` int(11) NOT NULL,
  `idDeUsuario` int(11) DEFAULT NULL,
  `idDeCargo` int(11) DEFAULT NULL,
  `estado` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id`, `idDeUsuario`, `idDeCargo`, `estado`) VALUES
(3, 5, 1, 'activo'),
(4, 6, 1, 'activo'),
(7, 14, 1, 'activo'),
(8, 15, 1, 'activo');

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
(2, 'sah4o', 'cerrada'),
(3, 'wp2ab', 'cerrada'),
(4, '4gcla', 'cerrada'),
(5, 'iyabu', 'cerrada'),
(6, 'prnqk', 'cerrada'),
(7, 'qrapk', 'cerrada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orden`
--

CREATE TABLE `orden` (
  `id` int(11) NOT NULL,
  `codigo` varchar(5) DEFAULT NULL,
  `idDeCliente` int(11) DEFAULT NULL,
  `idDeMesa` int(11) DEFAULT NULL,
  `fechaDeOrden` datetime DEFAULT NULL,
  `rutaDeLaImagen` varchar(50) DEFAULT NULL,
  `nombreDeLaImagen` varchar(50) DEFAULT NULL,
  `costoTotal` float DEFAULT NULL,
  `estado` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `orden`
--

INSERT INTO `orden` (`id`, `codigo`, `idDeCliente`, `idDeMesa`, `fechaDeOrden`, `rutaDeLaImagen`, `nombreDeLaImagen`, `costoTotal`, `estado`) VALUES
(1, 'ABC12', 10, 1, '2024-06-01 00:00:00', NULL, NULL, NULL, 'activa'),
(3, '2okgf', 10, 1, '2024-06-03 00:00:00', NULL, NULL, 0, 'activa'),
(4, '0gp2p', 10, 1, '2024-06-03 00:00:00', 'Imagenes/Mesa/', 'mesa.jpg', 0, 'activa');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pedido`
--

CREATE TABLE `pedido` (
  `id` int(11) NOT NULL,
  `numeroDePedido` int(11) DEFAULT NULL,
  `idDeOrden` int(11) DEFAULT NULL,
  `idDeProducto` int(11) DEFAULT NULL,
  `idDeEmpleado` int(11) DEFAULT NULL,
  `idDeSector` int(11) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `tiempoDePreparacion` datetime DEFAULT NULL,
  `tiempoDeEntrega` datetime DEFAULT NULL,
  `importeTotal` float DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `pedido`
--

INSERT INTO `pedido` (`id`, `numeroDePedido`, `idDeOrden`, `idDeProducto`, `idDeEmpleado`, `idDeSector`, `cantidad`, `tiempoDePreparacion`, `tiempoDeEntrega`, `importeTotal`, `estado`) VALUES
(3, 521, 1, 1, NULL, NULL, 3, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 2400, 'pendiente'),
(4, 676, 1, 1, NULL, NULL, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, 'pendiente'),
(5, 466, 1, 4, NULL, NULL, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, 'pendiente'),
(6, 320, 1, 4, NULL, 2, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, 'pendiente'),
(7, 400, 1, 2, NULL, 1, 2, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, 'pendiente');

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
(5, 'Tiramisu', 7, 8500);

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
(2, 'barraDeTragos'),
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
  `rol` varchar(60) DEFAULT NULL,
  `fechaDeRegistro` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `email`, `clave`, `nombre`, `apellido`, `rol`, `fechaDeRegistro`) VALUES
(5, 'pablo@gmail.com', '123456789', 'pablo', 'hernandez', 'Empleado', '2024-05-29 08:41:24'),
(6, 'juliofernandez@gmail.com', '12345678', 'julio', 'fernandez', 'Empleado', '2024-05-30 06:22:14'),
(7, 'pedrolopilato@gmail.com', '12345678', 'mario', 'lopilato', 'Empleado', '2024-06-01 09:35:48'),
(9, 'papelitojulio@gmail.com', '12345678', 'julio', 'Dicassio', 'cliente', '2024-06-01 21:28:33'),
(10, 'marianocloss@gmail.com', '12345678', 'mariano', 'Closs', 'cliente', '2024-06-01 21:29:26'),
(14, 'pepito123@gmail.com', '12345678', 'pepe', 'Dicamio', 'Empleado', '2024-06-03 06:58:47'),
(15, 'mariaJuana123@gmail.com', '12345678', 'maria', 'Dicamio', 'Empleado', '2024-06-03 22:41:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cargo`
--
ALTER TABLE `cargo`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `mesa`
--
ALTER TABLE `mesa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `orden`
--
ALTER TABLE `orden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `pedido`
--
ALTER TABLE `pedido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `tipodeproducto`
--
ALTER TABLE `tipodeproducto`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
