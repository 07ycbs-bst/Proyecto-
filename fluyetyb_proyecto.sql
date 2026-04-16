-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 16-04-2026 a las 18:11:59
-- Versión del servidor: 8.0.45-cll-lve
-- Versión de PHP: 8.4.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `fluyetyb_proyecto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `acciones`
--

CREATE TABLE `acciones` (
  `id_accion` int NOT NULL,
  `nombre_accion` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comentarios`
--

CREATE TABLE `comentarios` (
  `id_comentario` int NOT NULL,
  `id_proyecto` int DEFAULT NULL,
  `id_usuario` int DEFAULT NULL,
  `texto_comentario` text,
  `fecha_registro` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `comentarios`
--

INSERT INTO `comentarios` (`id_comentario`, `id_proyecto`, `id_usuario`, `texto_comentario`, `fecha_registro`) VALUES
(1, 6, NULL, 'Espectacular, excelente trabajo.', '2026-04-08 03:14:56'),
(3, 9, 11, 'Excelente trabajo hecho en tiempo record', '2026-04-08 03:23:02'),
(4, 6, 11, 'Un equipo muy profesional', '2026-04-08 03:25:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto`
--

CREATE TABLE `contacto` (
  `id_contacto` int NOT NULL,
  `id_empresa` int NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `asunto` varchar(150) DEFAULT NULL,
  `mensaje` text,
  `fecha` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `contacto`
--

INSERT INTO `contacto` (`id_contacto`, `id_empresa`, `nombre`, `correo`, `asunto`, `mensaje`, `fecha`) VALUES
(1, 1, 'Yohana Burgos', 'palmarmediastudio@gmail.com', 'rgfdfg', 'fsdfsf', '2026-04-08 02:46:18'),
(2, 1, 'Yohana Burgos', 'palmarmediastudio@gmail.com', 'Solicitud', '1235', '2026-04-08 02:47:50'),
(3, 1, 'Joender Torres ', 'Joender.torres@empresaspolar.com', 'Mantenimiento ', 'Reparación ', '2026-04-08 04:05:20'),
(4, 1, 'Asnardo Palmar', 'palmarmediastudio@gmail.com', 'Prueba', 'Prueba de envio de mesaje', '2026-04-14 02:49:53'),
(5, 1, 'Asnardo Palmar', 'palmarmediastudio@gmail.com', 'Prueba 2', 'Pueba 2 de envio de mensaje ', '2026-04-14 02:58:08');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `direcciones`
--

CREATE TABLE `direcciones` (
  `id_direccion` int NOT NULL,
  `calle_avenida` varchar(255) NOT NULL,
  `ciudad` varchar(100) DEFAULT NULL,
  `punto_referencia` text,
  `id_empresa` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Empresa`
--

CREATE TABLE `Empresa` (
  `id_empresa` int NOT NULL,
  `nombre_empresa` varchar(100) DEFAULT NULL,
  `rif` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Empresa`
--

INSERT INTO `Empresa` (`id_empresa`, `nombre_empresa`, `rif`) VALUES
(1, 'Fluye T&B', 'J501423970');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagina`
--

CREATE TABLE `pagina` (
  `id_pagina` int NOT NULL,
  `nombre_pagina` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto`
--

CREATE TABLE `proyecto` (
  `id_proyecto` int NOT NULL,
  `descripcion` text,
  `imagen_principal` varchar(255) DEFAULT 'default.jpg',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `presupuesto_estimado` decimal(12,2) DEFAULT NULL,
  `fecha_entrega_real` date DEFAULT NULL,
  `id_servicio` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proyecto`
--

INSERT INTO `proyecto` (`id_proyecto`, `descripcion`, `imagen_principal`, `fecha_inicio`, `fecha_fin`, `presupuesto_estimado`, `fecha_entrega_real`, `id_servicio`) VALUES
(4, 'Refuerzo de bisagras vencidas y ajuste de la cremallera del motor que se desalineó por el peso.', 'p_1775147718.png', '2025-12-01', '2025-12-01', 100.00, NULL, 10),
(5, 'Limpieza de superficie, aplicación de imprimante asfáltico y pegado de manto de 3.2mm o 4mm con soplete.', 'p_1775151257.png', '2026-01-20', '2026-02-06', 6200.00, NULL, 11),
(6, 'Excavación de fundaciones, levantamiento de estructura metálica (columnas y cerchas), techado con láminas de zinc o acerolit, y vaciado de losa de concreto de alta resistencia.', 'p_1775152469.png', '2026-01-05', '2026-03-31', 40000.00, NULL, 12),
(7, 'Desmontaje del cilindro, evaluación del vástago (cromado si es necesario), cambio de sellos (kit de estoperas) y pruebas de presión hidrostática.', 'p_1775153187.png', '2025-11-11', '2025-11-16', 1200.00, NULL, 13),
(8, 'Desarmado completo del motor, rectificación de culatas, cambio de camisas, pistones, anillos y cojinetes. Incluye la calibración del sistema de inyección y turbocompresores.', 'p_1775153641.png', '2025-04-03', '2025-05-28', 25000.00, NULL, 14),
(9, 'Instalación de  100 luminarias LED de alta potencia (150W-200W), tendido de tubería EMT/IMC en altura y tablero de control con fotoceldas o temporizadores.', 'p_1775154033.png', '2025-09-07', '2025-09-21', 23200.00, NULL, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proyecto_imagenes`
--

CREATE TABLE `proyecto_imagenes` (
  `id_img` int NOT NULL,
  `id_proyecto` int DEFAULT NULL,
  `ruta_imagen` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proyecto_imagenes`
--

INSERT INTO `proyecto_imagenes` (`id_img`, `id_proyecto`, `ruta_imagen`) VALUES
(6, 4, 'gal_4_0_1775147719.png'),
(7, 5, 'gal_5_0_1775151258.png'),
(8, 6, 'gal_6_0_1775152469.png'),
(9, 7, 'gal_7_0_1775153188.png'),
(10, 8, 'gal_8_0_1775153642.png'),
(11, 9, 'gal_9_0_1775154034.png');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `redes_sociales`
--

CREATE TABLE `redes_sociales` (
  `id_red` int NOT NULL,
  `plataforma` varchar(50) NOT NULL,
  `usuario_url` varchar(255) NOT NULL,
  `id_empresa` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Roles`
--

CREATE TABLE `Roles` (
  `id_rol` int NOT NULL,
  `nombre_rol` varchar(50) DEFAULT NULL,
  `descripcion_rol` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Roles`
--

INSERT INTO `Roles` (`id_rol`, `nombre_rol`, `descripcion_rol`) VALUES
(1, 'Administrador', 'Acceso total al sistema y gestión de usuarios'),
(2, 'Técnico', 'Gestión de proyectos y servicios asignados'),
(3, 'Cliente', 'Acceso para ver el estatus de sus solicitudes'),
(4, 'Soporte', 'Atención al cliente y registro de comentarios');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `R_E_S`
--

CREATE TABLE `R_E_S` (
  `id_empresa` int NOT NULL,
  `id_servicio` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `R_E_S`
--

INSERT INTO `R_E_S` (`id_empresa`, `id_servicio`) VALUES
(1, 10),
(1, 11),
(1, 12),
(1, 13),
(1, 14),
(1, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `R_R_A_P`
--

CREATE TABLE `R_R_A_P` (
  `id_rol` int NOT NULL,
  `id_accion` int NOT NULL,
  `id_pagina` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `R_R_U`
--

CREATE TABLE `R_R_U` (
  `id_rol` int NOT NULL,
  `id_usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `R_R_U`
--

INSERT INTO `R_R_U` (`id_rol`, `id_usuario`) VALUES
(1, 11),
(1, 16),
(3, 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicio`
--

CREATE TABLE `servicio` (
  `id_servicio` int NOT NULL,
  `tipo_servicio` varchar(100) DEFAULT NULL,
  `precio_referencial` decimal(10,2) DEFAULT NULL,
  `unidad_medida` enum('Hora','Metro','Kilo','Global') DEFAULT 'Global',
  `id_status` int DEFAULT NULL,
  `id_tipo` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `servicio`
--

INSERT INTO `servicio` (`id_servicio`, `tipo_servicio`, `precio_referencial`, `unidad_medida`, `id_status`, `id_tipo`) VALUES
(10, 'Reparación de portón eléctrico ', 100.00, 'Global', 1, 2),
(11, 'Impermeabilización de Techo', 6200.00, 'Global', 3, 4),
(12, 'Construcción de Galpón Industrial (300 m2)', 40000.00, 'Global', 1, 2),
(13, 'Reparación de Cilindros Hidráulicos de Maquinaria Pesada', 1200.00, 'Global', 1, 3),
(14, 'Overhaul (Reparación General) de Motor Generador Industrial', 25000.00, 'Global', 1, 3),
(15, 'Sistema de Iluminación para Galpones o Estadios (High-Bay)', 23200.00, 'Global', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `solicitud`
--

CREATE TABLE `solicitud` (
  `id_solicitud` int NOT NULL,
  `id_servicio` int DEFAULT NULL,
  `id_status` int DEFAULT NULL,
  `descripcion_falla` text,
  `prioridad` enum('Baja','Media','Alta','Urgente') DEFAULT 'Media',
  `nombre_contacto` varchar(100) DEFAULT NULL,
  `telefono_contacto` varchar(25) DEFAULT NULL,
  `correo_contacto` varchar(100) DEFAULT NULL,
  `id_usuario` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `solicitud`
--

INSERT INTO `solicitud` (`id_solicitud`, `id_servicio`, `id_status`, `descripcion_falla`, `prioridad`, `nombre_contacto`, `telefono_contacto`, `correo_contacto`, `id_usuario`) VALUES
(4, 15, 1, 'Necesitamos este Servicio para nuestra empresa', 'Urgente', 'Jose Manuel', '042463154580', 'josemanuel@gmailcom', NULL),
(5, 12, 1, 'Cotizame la fabricación de un Galpón insdutrial de 500 m2', 'Alta', 'Asnardo Palmar', '04124285952', '', 11),
(6, 12, 3, 'Cotizame la fabricación de un Galpón insdutrial de 500 m2', 'Alta', 'Asnardo Palmar', '04124285952', '', 11),
(7, 15, 2, 'Quiero un presupuesto', 'Media', 'Asnardo Palmar', '04124285952', '', 11),
(8, 10, 2, 'Presupuestame un Porton Electrico', 'Media', 'Asnardo Palmar', '04246315884', 'palmarmediastudio@gmail.com', 11);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status_servicios`
--

CREATE TABLE `status_servicios` (
  `id_status` int NOT NULL,
  `nombre_status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `status_servicios`
--

INSERT INTO `status_servicios` (`id_status`, `nombre_status`) VALUES
(1, 'Activo'),
(2, 'Inactivo '),
(3, 'Pendiente ');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status_solicitud`
--

CREATE TABLE `status_solicitud` (
  `id_status_solicitud` int NOT NULL,
  `nombre_status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `status_solicitud`
--

INSERT INTO `status_solicitud` (`id_status_solicitud`, `nombre_status`) VALUES
(1, 'Recibida'),
(2, 'En Análisis'),
(3, 'Presupuestada'),
(4, 'Aprobada'),
(5, 'Rechazada'),
(6, 'Finalizada');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `telefonos`
--

CREATE TABLE `telefonos` (
  `id_telefono` int NOT NULL,
  `numero` varchar(20) NOT NULL,
  `tipo_telefono` enum('Móvil','Fijo','WhatsApp') DEFAULT 'Móvil',
  `id_empresa` int DEFAULT NULL,
  `codigo_area` varchar(5) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo`
--

CREATE TABLE `tipo` (
  `id_tipo` int NOT NULL,
  `nombre_tipo` varchar(100) DEFAULT NULL,
  `icono_tipo` varchar(50) DEFAULT 'bi-tag'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `tipo`
--

INSERT INTO `tipo` (`id_tipo`, `nombre_tipo`, `icono_tipo`) VALUES
(1, 'Electricidad', 'bi-lightning'),
(2, 'Soldadura', 'bi-asterisk'),
(3, 'Mecánica', 'bi-wrench-adjustable'),
(4, 'Construcción', 'bi-cone-striped'),
(8, 'Mantenimiento', 'bi-gear-wide'),
(9, 'Pintura', 'bi-paint-bucket');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `Usuario`
--

CREATE TABLE `Usuario` (
  `id_usuario` int NOT NULL,
  `nombre_usuario` varchar(100) DEFAULT NULL,
  `cedula` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `id_empresa` int DEFAULT NULL,
  `token_verificacion` varchar(255) DEFAULT NULL,
  `cuenta_activa` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `Usuario`
--

INSERT INTO `Usuario` (`id_usuario`, `nombre_usuario`, `cedula`, `correo`, `password`, `id_empresa`, `token_verificacion`, `cuenta_activa`) VALUES
(11, 'Asnardo Palmar', '15060349', 'palmarmediastudio@gmail.com', '$2y$10$bPYjHMHwviDdLB7ckp1VYOCwlxFbvxk4wSFaQKULkK62yMRv9M2jW', 1, NULL, 1),
(16, 'Yohana Burgos', NULL, 'ycbs07@hotmail.com', '$2y$10$cb.TjvL2XSqA.vdQWbJlvOTBbPvamRqIvv7eow9ACFojEvL4K8CFy', NULL, NULL, 1),
(17, 'Jaide Ponce', NULL, 'jaiderponce@gmail.com', '$2y$10$eUzFGCF2QLNZgNGodDuZpue.MB36vRPdvPlaD4NjTsKYNgYB3N74.', NULL, NULL, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `acciones`
--
ALTER TABLE `acciones`
  ADD PRIMARY KEY (`id_accion`);

--
-- Indices de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id_comentario`),
  ADD KEY `id_proyecto` (`id_proyecto`);

--
-- Indices de la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD PRIMARY KEY (`id_contacto`),
  ADD KEY `fk_contacto_empresa` (`id_empresa`);

--
-- Indices de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD PRIMARY KEY (`id_direccion`),
  ADD KEY `fk_dir_empresa` (`id_empresa`);

--
-- Indices de la tabla `Empresa`
--
ALTER TABLE `Empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `pagina`
--
ALTER TABLE `pagina`
  ADD PRIMARY KEY (`id_pagina`);

--
-- Indices de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD PRIMARY KEY (`id_proyecto`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `proyecto_imagenes`
--
ALTER TABLE `proyecto_imagenes`
  ADD PRIMARY KEY (`id_img`),
  ADD KEY `id_proyecto` (`id_proyecto`);

--
-- Indices de la tabla `redes_sociales`
--
ALTER TABLE `redes_sociales`
  ADD PRIMARY KEY (`id_red`),
  ADD KEY `fk_red_empresa` (`id_empresa`);

--
-- Indices de la tabla `Roles`
--
ALTER TABLE `Roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `R_E_S`
--
ALTER TABLE `R_E_S`
  ADD PRIMARY KEY (`id_empresa`,`id_servicio`),
  ADD KEY `id_servicio` (`id_servicio`);

--
-- Indices de la tabla `R_R_A_P`
--
ALTER TABLE `R_R_A_P`
  ADD PRIMARY KEY (`id_rol`,`id_accion`,`id_pagina`),
  ADD KEY `id_accion` (`id_accion`),
  ADD KEY `id_pagina` (`id_pagina`);

--
-- Indices de la tabla `R_R_U`
--
ALTER TABLE `R_R_U`
  ADD PRIMARY KEY (`id_rol`,`id_usuario`),
  ADD KEY `id_usuario` (`id_usuario`);

--
-- Indices de la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD PRIMARY KEY (`id_servicio`),
  ADD KEY `id_tipo` (`id_tipo`),
  ADD KEY `id_status` (`id_status`);

--
-- Indices de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD PRIMARY KEY (`id_solicitud`),
  ADD KEY `Servicio_ibfk_1` (`id_servicio`),
  ADD KEY `id_status` (`id_status`),
  ADD KEY `idx_status_solicitud` (`id_status`);

--
-- Indices de la tabla `status_servicios`
--
ALTER TABLE `status_servicios`
  ADD PRIMARY KEY (`id_status`);

--
-- Indices de la tabla `status_solicitud`
--
ALTER TABLE `status_solicitud`
  ADD PRIMARY KEY (`id_status_solicitud`);

--
-- Indices de la tabla `telefonos`
--
ALTER TABLE `telefonos`
  ADD PRIMARY KEY (`id_telefono`),
  ADD KEY `fk_tel` (`id_empresa`);

--
-- Indices de la tabla `tipo`
--
ALTER TABLE `tipo`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Indices de la tabla `Usuario`
--
ALTER TABLE `Usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `id_empresa` (`id_empresa`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `acciones`
--
ALTER TABLE `acciones`
  MODIFY `id_accion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id_comentario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `contacto`
--
ALTER TABLE `contacto`
  MODIFY `id_contacto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `direcciones`
--
ALTER TABLE `direcciones`
  MODIFY `id_direccion` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Empresa`
--
ALTER TABLE `Empresa`
  MODIFY `id_empresa` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pagina`
--
ALTER TABLE `pagina`
  MODIFY `id_pagina` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `proyecto`
--
ALTER TABLE `proyecto`
  MODIFY `id_proyecto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `proyecto_imagenes`
--
ALTER TABLE `proyecto_imagenes`
  MODIFY `id_img` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `redes_sociales`
--
ALTER TABLE `redes_sociales`
  MODIFY `id_red` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `Roles`
--
ALTER TABLE `Roles`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `servicio`
--
ALTER TABLE `servicio`
  MODIFY `id_servicio` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `solicitud`
--
ALTER TABLE `solicitud`
  MODIFY `id_solicitud` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `status_servicios`
--
ALTER TABLE `status_servicios`
  MODIFY `id_status` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `status_solicitud`
--
ALTER TABLE `status_solicitud`
  MODIFY `id_status_solicitud` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `telefonos`
--
ALTER TABLE `telefonos`
  MODIFY `id_telefono` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo`
--
ALTER TABLE `tipo`
  MODIFY `id_tipo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `Usuario`
--
ALTER TABLE `Usuario`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`id_proyecto`) REFERENCES `proyecto` (`id_proyecto`);

--
-- Filtros para la tabla `contacto`
--
ALTER TABLE `contacto`
  ADD CONSTRAINT `fk_contacto_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `Empresa` (`id_empresa`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `direcciones`
--
ALTER TABLE `direcciones`
  ADD CONSTRAINT `fk_dir_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `Empresa` (`id_empresa`) ON DELETE CASCADE;

--
-- Filtros para la tabla `proyecto`
--
ALTER TABLE `proyecto`
  ADD CONSTRAINT `proyecto_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`);

--
-- Filtros para la tabla `proyecto_imagenes`
--
ALTER TABLE `proyecto_imagenes`
  ADD CONSTRAINT `proyecto_imagenes_ibfk_1` FOREIGN KEY (`id_proyecto`) REFERENCES `proyecto` (`id_proyecto`) ON DELETE CASCADE;

--
-- Filtros para la tabla `redes_sociales`
--
ALTER TABLE `redes_sociales`
  ADD CONSTRAINT `fk_red_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `Empresa` (`id_empresa`) ON DELETE CASCADE;

--
-- Filtros para la tabla `R_E_S`
--
ALTER TABLE `R_E_S`
  ADD CONSTRAINT `R_E_S_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `Empresa` (`id_empresa`),
  ADD CONSTRAINT `R_E_S_ibfk_2` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`);

--
-- Filtros para la tabla `R_R_A_P`
--
ALTER TABLE `R_R_A_P`
  ADD CONSTRAINT `R_R_A_P_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `Roles` (`id_rol`),
  ADD CONSTRAINT `R_R_A_P_ibfk_2` FOREIGN KEY (`id_accion`) REFERENCES `acciones` (`id_accion`),
  ADD CONSTRAINT `R_R_A_P_ibfk_3` FOREIGN KEY (`id_pagina`) REFERENCES `pagina` (`id_pagina`);

--
-- Filtros para la tabla `R_R_U`
--
ALTER TABLE `R_R_U`
  ADD CONSTRAINT `R_R_U_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `Roles` (`id_rol`),
  ADD CONSTRAINT `R_R_U_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `Usuario` (`id_usuario`);

--
-- Filtros para la tabla `servicio`
--
ALTER TABLE `servicio`
  ADD CONSTRAINT `fk_servicio_starus` FOREIGN KEY (`id_status`) REFERENCES `status_servicios` (`id_status`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  ADD CONSTRAINT `fk_servicio_tipo` FOREIGN KEY (`id_tipo`) REFERENCES `tipo` (`id_tipo`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Filtros para la tabla `solicitud`
--
ALTER TABLE `solicitud`
  ADD CONSTRAINT `fk_solicitud_status` FOREIGN KEY (`id_status`) REFERENCES `status_solicitud` (`id_status_solicitud`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `Servicio_ibfk_1` FOREIGN KEY (`id_servicio`) REFERENCES `servicio` (`id_servicio`) ON DELETE RESTRICT ON UPDATE RESTRICT;

--
-- Filtros para la tabla `telefonos`
--
ALTER TABLE `telefonos`
  ADD CONSTRAINT `fk_tel` FOREIGN KEY (`id_empresa`) REFERENCES `Empresa` (`id_empresa`),
  ADD CONSTRAINT `fk_tel_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `Empresa` (`id_empresa`) ON DELETE CASCADE;

--
-- Filtros para la tabla `Usuario`
--
ALTER TABLE `Usuario`
  ADD CONSTRAINT `Usuario_ibfk_1` FOREIGN KEY (`id_empresa`) REFERENCES `Empresa` (`id_empresa`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
