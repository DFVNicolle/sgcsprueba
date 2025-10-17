-- SQL generado a partir de las migraciones Laravel en database/migrations
-- Motor: InnoDB, charset: utf8mb4

SET FOREIGN_KEY_CHECKS = 0;

-- Tabla `usuarios`
CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` CHAR(36) NOT NULL,
  `correo` VARCHAR(255) NOT NULL,
  `correo_verificado_en` TIMESTAMP NULL DEFAULT NULL,
  `nombre_completo` VARCHAR(255) NULL,
  `contrasena_hash` TEXT NULL,
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `remember_token` VARCHAR(100) NULL,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuarios_correo_unique` (`correo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `password_reset_tokens`
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` VARCHAR(255) NOT NULL,
  `token` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `sessions`
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` VARCHAR(255) NOT NULL,
  `usuario_id` CHAR(36) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` TEXT NULL,
  `payload` LONGTEXT NOT NULL,
  `last_activity` INT NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_usuario_id_index` (`usuario_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `roles`
CREATE TABLE IF NOT EXISTS `roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `descripcion` TEXT NULL,
  `created_at` TIMESTAMP NULL DEFAULT NULL,
  `updated_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_nombre_unique` (`nombre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `usuarios_roles` (sin FK a proyectos aún)
CREATE TABLE IF NOT EXISTS `usuarios_roles` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `usuario_id` CHAR(36) NOT NULL,
  `rol_id` BIGINT UNSIGNED NOT NULL,
  `proyecto_id` CHAR(36) NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_usuario_rol_proyecto` (`usuario_id`,`rol_id`,`proyecto_id`),
  CONSTRAINT `usuarios_roles_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  CONSTRAINT `usuarios_roles_rol_fk` FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `proyectos`
CREATE TABLE IF NOT EXISTS `proyectos` (
  `id` CHAR(36) NOT NULL,
  `codigo` VARCHAR(50) NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NULL,
  `metodologia` ENUM('agil','cascada','hibrida') NOT NULL DEFAULT 'agil',
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `proyectos_codigo_unique` (`codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ahora sí agregar FK de `usuarios_roles.proyecto_id` hacia `proyectos` (migration añade FK después)
ALTER TABLE `usuarios_roles`
  ADD CONSTRAINT `usuarios_roles_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE SET NULL;

-- Tabla `equipos`
CREATE TABLE IF NOT EXISTS `equipos` (
  `id` CHAR(36) NOT NULL,
  `proyecto_id` CHAR(36) NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `lider_id` CHAR(36) NULL,
  PRIMARY KEY (`id`),
  KEY `equipos_proyecto_id_index` (`proyecto_id`),
  KEY `equipos_lider_id_index` (`lider_id`),
  CONSTRAINT `equipos_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `equipos_lider_fk` FOREIGN KEY (`lider_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `miembros_equipo`
CREATE TABLE IF NOT EXISTS `miembros_equipo` (
  `equipo_id` CHAR(36) NOT NULL,
  `usuario_id` CHAR(36) NOT NULL,
  `rol_id` BIGINT UNSIGNED NOT NULL,
  PRIMARY KEY (`equipo_id`,`usuario_id`),
  KEY `miembros_equipo_rol_id_index` (`rol_id`),
  CONSTRAINT `miembros_equipo_equipo_fk` FOREIGN KEY (`equipo_id`) REFERENCES `equipos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `miembros_equipo_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE,
  CONSTRAINT `miembros_equipo_rol_fk` FOREIGN KEY (`rol_id`) REFERENCES `roles`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `elementos_configuracion`
CREATE TABLE IF NOT EXISTS `elementos_configuracion` (
  `id` CHAR(36) NOT NULL,
  `codigo_ec` VARCHAR(50) NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NULL,
  `proyecto_id` CHAR(36) NOT NULL,
  `tipo` ENUM('DOCUMENTO','CODIGO','SCRIPT_BD','CONFIGURACION','OTRO') NOT NULL,
  `padre_id` CHAR(36) NULL,
  `version_actual_id` CHAR(36) NULL,
  `estado` ENUM('BORRADOR','EN_REVISION','APROBADO','LIBERADO','OBSOLETO') NOT NULL DEFAULT 'BORRADOR',
  `metadatos` JSON NULL,
  `creado_por` CHAR(36) NULL,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `elementos_configuracion_codigo_ec_unique` (`codigo_ec`),
  KEY `elementos_configuracion_proyecto_id_index` (`proyecto_id`),
  KEY `elementos_configuracion_padre_id_index` (`padre_id`),
  KEY `elementos_configuracion_version_actual_id_index` (`version_actual_id`),
  CONSTRAINT `elementos_configuracion_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`) ON DELETE CASCADE,
  CONSTRAINT `elementos_configuracion_padre_fk` FOREIGN KEY (`padre_id`) REFERENCES `elementos_configuracion`(`id`) ON DELETE SET NULL,
  CONSTRAINT `elementos_configuracion_creado_por_fk` FOREIGN KEY (`creado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `versiones_ec`
CREATE TABLE IF NOT EXISTS `versiones_ec` (
  `id` CHAR(36) NOT NULL,
  `ec_id` CHAR(36) NOT NULL,
  `version` VARCHAR(20) NOT NULL,
  `registro_cambios` TEXT NULL,
  `commit_id` CHAR(36) NULL,
  `metadatos` JSON NULL,
  `estado` ENUM('BORRADOR','REVISION','APROBADO','LIBERADO','DEPRECADO') NOT NULL DEFAULT 'BORRADOR',
  `creado_por` CHAR(36) NULL,
  `aprobado_por` CHAR(36) NULL,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `aprobado_en` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `versiones_ec_ec_id_index` (`ec_id`),
  KEY `versiones_ec_commit_id_index` (`commit_id`),
  CONSTRAINT `versiones_ec_ec_fk` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion`(`id`) ON DELETE CASCADE,
  CONSTRAINT `versiones_ec_creado_por_fk` FOREIGN KEY (`creado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL,
  CONSTRAINT `versiones_ec_aprobado_por_fk` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ahora agregar FK de `elementos_configuracion.version_actual_id` hacia `versiones_ec` (migration lo hace después)
ALTER TABLE `elementos_configuracion`
  ADD CONSTRAINT `elementos_configuracion_version_actual_fk` FOREIGN KEY (`version_actual_id`) REFERENCES `versiones_ec`(`id`) ON DELETE SET NULL;

-- Tabla `relaciones_ec`
CREATE TABLE IF NOT EXISTS `relaciones_ec` (
  `id` CHAR(36) NOT NULL,
  `desde_ec` CHAR(36) NOT NULL,
  `hacia_ec` CHAR(36) NOT NULL,
  `tipo_relacion` ENUM('DEPENDE_DE','DERIVADO_DE','REFERENCIA','REQUERIDO_POR') NOT NULL,
  `nota` TEXT NULL,
  PRIMARY KEY (`id`),
  KEY `relaciones_ec_desde_ec_index` (`desde_ec`),
  KEY `relaciones_ec_hacia_ec_index` (`hacia_ec`),
  CONSTRAINT `relaciones_ec_desde_ec_fk` FOREIGN KEY (`desde_ec`) REFERENCES `elementos_configuracion`(`id`) ON DELETE CASCADE,
  CONSTRAINT `relaciones_ec_hacia_ec_fk` FOREIGN KEY (`hacia_ec`) REFERENCES `elementos_configuracion`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `liberaciones`
CREATE TABLE IF NOT EXISTS `liberaciones` (
  `id` CHAR(36) NOT NULL,
  `proyecto_id` CHAR(36) NOT NULL,
  `etiqueta` VARCHAR(50) NOT NULL,
  `nombre` VARCHAR(255) NULL,
  `descripcion` TEXT NULL,
  `fecha_liberacion` DATE NULL,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `liberaciones_proyecto_id_index` (`proyecto_id`),
  CONSTRAINT `liberaciones_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `items_liberacion`
CREATE TABLE IF NOT EXISTS `items_liberacion` (
  `id` CHAR(36) NOT NULL,
  `liberacion_id` CHAR(36) NOT NULL,
  `ec_id` CHAR(36) NULL,
  `version_ec_id` CHAR(36) NULL,
  PRIMARY KEY (`id`),
  KEY `items_liberacion_liberacion_id_index` (`liberacion_id`),
  KEY `items_liberacion_ec_id_index` (`ec_id`),
  KEY `items_liberacion_version_ec_id_index` (`version_ec_id`),
  CONSTRAINT `items_liberacion_liberacion_fk` FOREIGN KEY (`liberacion_id`) REFERENCES `liberaciones`(`id`) ON DELETE CASCADE,
  CONSTRAINT `items_liberacion_ec_fk` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion`(`id`),
  CONSTRAINT `items_liberacion_version_fk` FOREIGN KEY (`version_ec_id`) REFERENCES `versiones_ec`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `solicitudes_cambio`
CREATE TABLE IF NOT EXISTS `solicitudes_cambio` (
  `id` CHAR(36) NOT NULL,
  `proyecto_id` CHAR(36) NOT NULL,
  `titulo` VARCHAR(255) NOT NULL,
  `descripcion` TEXT NULL,
  `solicitante_id` CHAR(36) NULL,
  `prioridad` ENUM('BAJA','MEDIA','ALTA','CRITICA') NOT NULL DEFAULT 'MEDIA',
  `estado` ENUM('ABIERTA','EN_REVISION','APROBADA','RECHAZADA','IMPLEMENTADA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
  `resumen_impacto` TEXT NULL,
  `liberacion_objetivo_id` CHAR(36) NULL,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `actualizado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `solicitudes_cambio_proyecto_id_index` (`proyecto_id`),
  KEY `solicitudes_cambio_solicitante_id_index` (`solicitante_id`),
  KEY `solicitudes_cambio_liberacion_objetivo_id_index` (`liberacion_objetivo_id`),
  CONSTRAINT `solicitudes_cambio_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`),
  CONSTRAINT `solicitudes_cambio_solicitante_fk` FOREIGN KEY (`solicitante_id`) REFERENCES `usuarios`(`id`),
  CONSTRAINT `solicitudes_cambio_liberacion_fk` FOREIGN KEY (`liberacion_objetivo_id`) REFERENCES `liberaciones`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `items_cambio`
CREATE TABLE IF NOT EXISTS `items_cambio` (
  `id` CHAR(36) NOT NULL,
  `solicitud_cambio_id` CHAR(36) NOT NULL,
  `ec_id` CHAR(36) NULL,
  `version_actual_ec_id` CHAR(36) NULL,
  `version_propuesta` VARCHAR(20) NULL,
  `nota` TEXT NULL,
  PRIMARY KEY (`id`),
  KEY `items_cambio_solicitud_cambio_id_index` (`solicitud_cambio_id`),
  KEY `items_cambio_ec_id_index` (`ec_id`),
  KEY `items_cambio_version_actual_ec_id_index` (`version_actual_ec_id`),
  CONSTRAINT `items_cambio_solicitud_fk` FOREIGN KEY (`solicitud_cambio_id`) REFERENCES `solicitudes_cambio`(`id`) ON DELETE CASCADE,
  CONSTRAINT `items_cambio_ec_fk` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion`(`id`),
  CONSTRAINT `items_cambio_version_actual_fk` FOREIGN KEY (`version_actual_ec_id`) REFERENCES `versiones_ec`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `comite_cambios`
CREATE TABLE IF NOT EXISTS `comite_cambios` (
  `id` CHAR(36) NOT NULL,
  `proyecto_id` CHAR(36) NULL,
  `nombre` VARCHAR(255) NULL,
  `quorum` INT NOT NULL DEFAULT 1,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `comite_cambios_proyecto_id_index` (`proyecto_id`),
  CONSTRAINT `comite_cambios_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `miembros_ccb`
CREATE TABLE IF NOT EXISTS `miembros_ccb` (
  `ccb_id` CHAR(36) NOT NULL,
  `usuario_id` CHAR(36) NOT NULL,
  `rol_en_ccb` VARCHAR(100) NULL,
  PRIMARY KEY (`ccb_id`,`usuario_id`),
  CONSTRAINT `miembros_ccb_ccb_fk` FOREIGN KEY (`ccb_id`) REFERENCES `comite_cambios`(`id`) ON DELETE CASCADE,
  CONSTRAINT `miembros_ccb_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `votos_ccb`
CREATE TABLE IF NOT EXISTS `votos_ccb` (
  `id` CHAR(36) NOT NULL,
  `ccb_id` CHAR(36) NOT NULL,
  `solicitud_cambio_id` CHAR(36) NOT NULL,
  `usuario_id` CHAR(36) NOT NULL,
  `voto` ENUM('APROBAR','RECHAZAR','ABSTENERSE') NULL,
  `comentario` TEXT NULL,
  `votado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `votos_ccb_ccb_id_index` (`ccb_id`),
  KEY `votos_ccb_solicitud_cambio_id_index` (`solicitud_cambio_id`),
  KEY `votos_ccb_usuario_id_index` (`usuario_id`),
  CONSTRAINT `votos_ccb_ccb_fk` FOREIGN KEY (`ccb_id`) REFERENCES `comite_cambios`(`id`),
  CONSTRAINT `votos_ccb_solicitud_cambio_fk` FOREIGN KEY (`solicitud_cambio_id`) REFERENCES `solicitudes_cambio`(`id`),
  CONSTRAINT `votos_ccb_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `auditorias`
CREATE TABLE IF NOT EXISTS `auditorias` (
  `id` CHAR(36) NOT NULL,
  `tipo_entidad` VARCHAR(100) NULL,
  `entidad_id` CHAR(36) NULL,
  `accion` VARCHAR(50) NULL,
  `usuario_id` CHAR(36) NULL,
  `detalles` JSON NULL,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `auditorias_usuario_id_index` (`usuario_id`),
  CONSTRAINT `auditorias_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `accesos`
CREATE TABLE IF NOT EXISTS `accesos` (
  `id` CHAR(36) NOT NULL,
  `usuario_id` CHAR(36) NULL,
  `ip` VARCHAR(45) NULL,
  `accion` TEXT NULL,
  `recurso` TEXT NULL,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `accesos_usuario_id_index` (`usuario_id`),
  CONSTRAINT `accesos_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `notificaciones`
CREATE TABLE IF NOT EXISTS `notificaciones` (
  `id` CHAR(36) NOT NULL,
  `usuario_id` CHAR(36) NULL,
  `tipo` VARCHAR(100) NULL,
  `datos` JSON NULL,
  `leida` TINYINT(1) NOT NULL DEFAULT 0,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `notificaciones_usuario_id_index` (`usuario_id`),
  CONSTRAINT `notificaciones_usuario_fk` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla `commits_repositorio`
CREATE TABLE IF NOT EXISTS `commits_repositorio` (
  `id` CHAR(36) NOT NULL,
  `url_repositorio` TEXT NULL,
  `hash_commit` TEXT NULL,
  `autor` TEXT NULL,
  `mensaje` TEXT NULL,
  `fecha_commit` TIMESTAMP NULL DEFAULT NULL,
  `ec_id` CHAR(36) NULL,
  PRIMARY KEY (`id`),
  KEY `commits_repositorio_ec_id_index` (`ec_id`),
  CONSTRAINT `commits_repositorio_ec_fk` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ahora agregar FK de `versiones_ec.commit_id` hacia `commits_repositorio`
ALTER TABLE `versiones_ec`
  ADD CONSTRAINT `versiones_ec_commit_fk` FOREIGN KEY (`commit_id`) REFERENCES `commits_repositorio`(`id`) ON DELETE SET NULL;

-- Tablas complementarias (incidencias, metricas_proyecto, revisiones, respaldos, bitacora_implementacion)
CREATE TABLE IF NOT EXISTS `incidencias` (
  `id` CHAR(36) NOT NULL,
  `proyecto_id` CHAR(36) NULL,
  `descripcion` TEXT NULL,
  `tipo` ENUM('ERROR','OMISION','CAMBIO_NO_AUTORIZADO','OTRO') NOT NULL DEFAULT 'OTRO',
  `severidad` ENUM('BAJA','MEDIA','ALTA','CRITICA') NOT NULL DEFAULT 'MEDIA',
  `estado` ENUM('ABIERTA','EN_PROCESO','RESUELTA','CERRADA') NOT NULL DEFAULT 'ABIERTA',
  `reportado_por` CHAR(36) NULL,
  `asignado_a` CHAR(36) NULL,
  `creado_en` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cerrado_en` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `incidencias_proyecto_id_index` (`proyecto_id`),
  KEY `incidencias_reportado_por_index` (`reportado_por`),
  KEY `incidencias_asignado_a_index` (`asignado_a`),
  CONSTRAINT `incidencias_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`),
  CONSTRAINT `incidencias_reportado_por_fk` FOREIGN KEY (`reportado_por`) REFERENCES `usuarios`(`id`),
  CONSTRAINT `incidencias_asignado_a_fk` FOREIGN KEY (`asignado_a`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `metricas_proyecto` (
  `id` CHAR(36) NOT NULL,
  `proyecto_id` CHAR(36) NULL,
  `tipo` VARCHAR(100) NULL,
  `valor` DECIMAL(10,2) NULL,
  `descripcion` TEXT NULL,
  `fecha_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `metricas_proyecto_proyecto_id_index` (`proyecto_id`),
  CONSTRAINT `metricas_proyecto_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `revisiones` (
  `id` CHAR(36) NOT NULL,
  `ec_id` CHAR(36) NULL,
  `version_ec_id` CHAR(36) NULL,
  `revisor_id` CHAR(36) NULL,
  `resultado` ENUM('APROBADO','OBSERVADO','RECHAZADO') NOT NULL DEFAULT 'OBSERVADO',
  `observaciones` TEXT NULL,
  `fecha_revision` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `revisiones_ec_id_index` (`ec_id`),
  KEY `revisiones_version_ec_id_index` (`version_ec_id`),
  KEY `revisiones_revisor_id_index` (`revisor_id`),
  CONSTRAINT `revisiones_ec_fk` FOREIGN KEY (`ec_id`) REFERENCES `elementos_configuracion`(`id`),
  CONSTRAINT `revisiones_version_ec_fk` FOREIGN KEY (`version_ec_id`) REFERENCES `versiones_ec`(`id`),
  CONSTRAINT `revisiones_revisor_fk` FOREIGN KEY (`revisor_id`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `respaldos` (
  `id` CHAR(36) NOT NULL,
  `ruta` TEXT NULL,
  `tipo` ENUM('AUTOMATICO','MANUAL') NOT NULL DEFAULT 'MANUAL',
  `tamano_mb` DECIMAL(10,2) NULL,
  `realizado_por` CHAR(36) NULL,
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `respaldos_realizado_por_index` (`realizado_por`),
  CONSTRAINT `respaldos_realizado_por_fk` FOREIGN KEY (`realizado_por`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `bitacora_implementacion` (
  `id` CHAR(36) NOT NULL,
  `proyecto_id` CHAR(36) NULL,
  `liberacion_id` CHAR(36) NULL,
  `descripcion` TEXT NULL,
  `realizado_por` CHAR(36) NULL,
  `fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bitacora_implementacion_proyecto_id_index` (`proyecto_id`),
  KEY `bitacora_implementacion_liberacion_id_index` (`liberacion_id`),
  KEY `bitacora_implementacion_realizado_por_index` (`realizado_por`),
  CONSTRAINT `bitacora_implementacion_proyecto_fk` FOREIGN KEY (`proyecto_id`) REFERENCES `proyectos`(`id`),
  CONSTRAINT `bitacora_implementacion_liberacion_fk` FOREIGN KEY (`liberacion_id`) REFERENCES `liberaciones`(`id`),
  CONSTRAINT `bitacora_implementacion_realizado_por_fk` FOREIGN KEY (`realizado_por`) REFERENCES `usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Migration: add creado_por to proyectos
ALTER TABLE `proyectos`
  ADD COLUMN `creado_por` CHAR(36) NULL AFTER `metodologia`;
ALTER TABLE `proyectos`
  ADD CONSTRAINT `proyectos_creado_por_fk` FOREIGN KEY (`creado_por`) REFERENCES `usuarios`(`id`) ON DELETE RESTRICT;

-- Migration: add google2fa_secret to usuarios
ALTER TABLE `usuarios`
  ADD COLUMN `google2fa_secret` VARCHAR(255) NULL AFTER `correo_verificado_en`;

SET FOREIGN_KEY_CHECKS = 1;

-- Fin del script
