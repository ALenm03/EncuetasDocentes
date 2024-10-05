-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 03-10-2024 a las 08:26:23
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
-- Base de datos: `bdform`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formularios`
--

CREATE TABLE `formularios` (
  `id` int(11) NOT NULL,
  `nombre_formulario` varchar(100) NOT NULL,
  `pregunta_num` int(11) NOT NULL,
  `pregunta` text NOT NULL,
  `respuesta_1` text DEFAULT NULL,
  `respuesta_2` text DEFAULT NULL,
  `respuesta_3` text DEFAULT NULL,
  `respuesta_4` text DEFAULT NULL,
  `tipo_respuesta` enum('parrafo','checkbox','opcion_multiple') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `formularios`
--

INSERT INTO `formularios` (`id`, `nombre_formulario`, `pregunta_num`, `pregunta`, `respuesta_1`, `respuesta_2`, `respuesta_3`, `respuesta_4`, `tipo_respuesta`) VALUES
(1, 'wqeeq', 1, 'wqewqe', 'wqqe', 'wqeqew', 'wqeq', 'wqee', ''),
(2, 'wqeeq2', 1, 'wqewqe', 'wqqe', 'wqeqew', 'wqeq', 'wqee', ''),
(3, 'wqeeq2', 2, 'wqewqe', 'wqewqwewq', 'sds', 'sdsdsd', NULL, ''),
(4, 'wqeeq2', 3, 'sdsdds', NULL, NULL, NULL, NULL, '');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id_pregunta` int(11) NOT NULL,
  `nombre_encuesta` varchar(100) NOT NULL,
  `pregunta` text NOT NULL,
  `respuesta1` varchar(255) NOT NULL,
  `respuesta2` varchar(255) NOT NULL,
  `respuesta3` varchar(255) NOT NULL,
  `respuesta4` varchar(255) NOT NULL,
  `respuesta_correcta` varchar(255) DEFAULT NULL,
  `tipo_pregunta` enum('multiple','unica') DEFAULT 'multiple',
  `activo` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('user','admin') NOT NULL,
  `estatus` tinyint(1) DEFAULT 0,
  `correo` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `name`, `password`, `rol`, `estatus`, `correo`) VALUES
(1, 'Omar', '$2y$10$1phPA5ymUhfZIzMIyI5ouug7I3ivr0J0Zim.hrJG.LRESq7RFIVX6', 'user', 1, 'omardlc999@gmail.com'),
(2, 'Alen', '$2y$10$LqyYvZWyf1z8r89CPG6mN.VUDEEMVVBhlE1mFJODQJHGFQHumzLUy', 'admin', 1, 'alanleon121203@gmail.com'),
(3, 'Holo', '$2y$10$pqJ3GQ0SNRVlembasGa/0.U8xa.Zb3m2CfSu02J3FAunh2cnV5qvy', 'user', 1, 'lololertrololer@gmail.com');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `formularios`
--
ALTER TABLE `formularios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id_pregunta`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `formularios`
--
ALTER TABLE `formularios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id_pregunta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
