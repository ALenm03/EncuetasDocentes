-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 23-11-2024 a las 05:06:07
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
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `id_usuario` bigint(20) DEFAULT NULL,
  `id_formulario` int(11) DEFAULT NULL,
  `nombre_evento` varchar(100) DEFAULT NULL,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_final` date DEFAULT NULL,
  `participantes_actuales` int(11) DEFAULT 0,
  `participantes_totales` int(11) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `id_usuario`, `id_formulario`, `nombre_evento`, `fecha_inicio`, `fecha_final`, `participantes_actuales`, `participantes_totales`, `link`) VALUES
(1, 2, 1, 'nose', '2024-11-21', '2024-11-22', 2, 2, 'contestarencuesta.php?evento=673ed1aa83b0f'),
(2, 2, 1, 'holis', '2024-11-21', '2024-11-30', 1, 12, 'contestarencuesta.php?evento=673ed440e3d1c'),
(3, 2, 1, 'Holaaa', '2024-11-21', '2024-11-22', 1, 15, 'contestarencuesta.php?evento=673f9242a9925'),
(4, 2, 2, 'Evento1', '2024-11-21', '2024-11-22', 2, 2, 'contestarencuesta.php?evento=673f92fa4472c'),
(5, 2, 2, 'asdfasd', '2024-11-21', '2024-11-22', 1, 15, 'contestarencuesta.php?evento=673f9509c0c14'),
(6, 2, 2, 'jolu', '2024-11-23', '2024-11-24', 1, 4, 'contestarencuesta.php?evento=6741538fdfa66');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formulario`
--

CREATE TABLE `formulario` (
  `id` int(11) NOT NULL,
  `nombre_formulario` varchar(255) NOT NULL,
  `id_usuario` bigint(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `formulario`
--

INSERT INTO `formulario` (`id`, `nombre_formulario`, `id_usuario`) VALUES
(1, 'Hola', 2),
(2, 'Prueba1', 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `formularios`
--

CREATE TABLE `formularios` (
  `id` int(11) NOT NULL,
  `pregunta_num` int(11) NOT NULL,
  `pregunta` text NOT NULL,
  `respuesta_1` text DEFAULT NULL,
  `respuesta_2` text DEFAULT NULL,
  `respuesta_3` text DEFAULT NULL,
  `respuesta_4` text DEFAULT NULL,
  `tipo_respuesta` enum('parrafo','checkbox','opcion_multiple') NOT NULL,
  `id_formulario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `formularios`
--

INSERT INTO `formularios` (`id`, `pregunta_num`, `pregunta`, `respuesta_1`, `respuesta_2`, `respuesta_3`, `respuesta_4`, `tipo_respuesta`, `id_formulario`) VALUES
(1, 1, 'Sabes?', NULL, NULL, NULL, NULL, 'parrafo', 1),
(2, 2, 'Si?', 'No', 'Talvez', 'xd', NULL, 'checkbox', 1),
(4, 3, 'sad', NULL, NULL, NULL, NULL, 'parrafo', 1),
(5, 1, 'Hola', NULL, NULL, NULL, NULL, 'parrafo', 2),
(6, 2, 'Hola2', 'Opcion1', 'Opcion2', NULL, NULL, 'checkbox', 2);

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
-- Estructura de tabla para la tabla `respuestas`
--

CREATE TABLE `respuestas` (
  `id` int(11) NOT NULL,
  `id_usuario` bigint(20) DEFAULT NULL,
  `id_evento` int(11) DEFAULT NULL,
  `id_formulario` int(11) DEFAULT NULL,
  `pregunta_num` int(11) DEFAULT NULL,
  `respuesta` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `respuestas`
--

INSERT INTO `respuestas` (`id`, `id_usuario`, `id_evento`, `id_formulario`, `pregunta_num`, `respuesta`) VALUES
(1, 1, 1, 1, 1, 'Aveces'),
(2, 1, 1, 1, 2, '[\"No\",\"xd\"]'),
(3, 1, 1, 1, 3, 'si'),
(4, 1, 3, 1, 1, 'oaaaaaaaaaaaaaaaaaaaaaaaaaaaa'),
(5, 1, 3, 1, 2, '[\"No\"]'),
(6, 1, 3, 1, 3, 'yes'),
(7, 1, 4, 2, 1, 'Hola'),
(8, 1, 4, 2, 2, '[\"Opcion1\",\"Opcion2\"]'),
(9, 4, 4, 2, 1, 'Hola'),
(10, 4, 4, 2, 2, '[\"Opcion1\"]'),
(11, 1, 5, 2, 1, 'oifbvouebuobb u eab zu bbaeu rubu uro rueu'),
(12, 1, 5, 2, 2, '[\"Opcion1\",\"Opcion2\"]'),
(13, 1, 6, 2, 1, '45'),
(14, 1, 6, 2, 2, '[\"Opcion1\"]');

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
(3, 'Holo', '$2y$10$pqJ3GQ0SNRVlembasGa/0.U8xa.Zb3m2CfSu02J3FAunh2cnV5qvy', 'user', 1, 'lololertrololer@gmail.com'),
(4, 'Jermy', '$2y$10$aqzi1vudHjo0HO0vUjslHOaXvrv2/eS25rao2Qhhp/.jH3cYBtBEu', 'user', 1, 'Jermy123@gmail.com'),
(5, 'pol', '$2y$10$HWrx8aIHLtdNEh/3wnh/xeY4xVc58ai3LmLYNpPDZCqhuKR69AYn2', 'user', 0, 'pol@gmail.com'),
(6, 'Juan', '$2y$10$uwMcFoe3SrJJSxVFBeSDhuD6l22NEAK3u4OvnCdkVwhf6tUHI34S.', 'admin', 1, 'Juan@gmail.com'),
(7, 'Lopez', '$2y$10$rgm0WgkMSR8hTX1Xrw5/RuRfiWyPSQ8lNInML5eVT0flW6FsLSaOm', 'user', 0, 'Lopez999@gmail.com'),
(8, 'Paolo', '$2y$10$3ij3BSsl6poWUeeMBMJD0e3p4Xfv8USRG6dVb2BtMhnzfXhjDfXFe', 'user', 0, 'Paolo@gmail.com'),
(10, 'Paolo2', '$2y$10$9GzDb5m6bYMQDpfo1z97KuofiLwTzQtXnjUySRiRJd2T5o6mMYKeq', 'user', 0, 'Paolo2@gmail.com'),
(12, 'Paolo3', '$2y$10$1IlLr.dgz7BJ8jUWuSO6mOKeyUX54VYsPy.8VUeSORcuKJCa92BjK', 'user', 0, 'Paolo3@gmail.com'),
(14, 'Irving', '$2y$10$SPJ1QGcFldUVJEye20Qh9.4gBWSJOym.8XSNkkUcmsOqo8CHNP/3y', 'user', 1, 'iafs@gmail.com'),
(16, 'lolo', '$2y$10$/IZ0k1K9a26P199ItP5tgun8NiyIABh2paUz80jaCyM3SRrGOJsy.', 'user', 0, 'lolo@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_formulario`
--

CREATE TABLE `usuario_formulario` (
  `id` int(11) NOT NULL,
  `usuario_id` bigint(20) NOT NULL,
  `formulario_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_formulario` (`id_formulario`);

--
-- Indices de la tabla `formulario`
--
ALTER TABLE `formulario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `formularios`
--
ALTER TABLE `formularios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_formulario` (`id_formulario`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id_pregunta`);

--
-- Indices de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_evento` (`id_evento`),
  ADD KEY `id_formulario` (`id_formulario`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD UNIQUE KEY `unique_name` (`name`);

--
-- Indices de la tabla `usuario_formulario`
--
ALTER TABLE `usuario_formulario`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`),
  ADD KEY `formulario_id` (`formulario_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `formulario`
--
ALTER TABLE `formulario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `formularios`
--
ALTER TABLE `formularios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id_pregunta` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `respuestas`
--
ALTER TABLE `respuestas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `usuario_formulario`
--
ALTER TABLE `usuario_formulario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD CONSTRAINT `eventos_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `eventos_ibfk_2` FOREIGN KEY (`id_formulario`) REFERENCES `formulario` (`id`);

--
-- Filtros para la tabla `formulario`
--
ALTER TABLE `formulario`
  ADD CONSTRAINT `formulario_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`);

--
-- Filtros para la tabla `formularios`
--
ALTER TABLE `formularios`
  ADD CONSTRAINT `fk_formulario` FOREIGN KEY (`id_formulario`) REFERENCES `formulario` (`id`);

--
-- Filtros para la tabla `respuestas`
--
ALTER TABLE `respuestas`
  ADD CONSTRAINT `respuestas_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `respuestas_ibfk_2` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`),
  ADD CONSTRAINT `respuestas_ibfk_3` FOREIGN KEY (`id_formulario`) REFERENCES `formulario` (`id`);

--
-- Filtros para la tabla `usuario_formulario`
--
ALTER TABLE `usuario_formulario`
  ADD CONSTRAINT `usuario_formulario_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`),
  ADD CONSTRAINT `usuario_formulario_ibfk_2` FOREIGN KEY (`formulario_id`) REFERENCES `formularios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
