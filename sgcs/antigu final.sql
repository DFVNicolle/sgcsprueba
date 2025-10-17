-- ==========================================
-- BASE DE DATOS SGCS COMPLETA Y EXTENDIDA
-- ==========================================

CREATE DATABASE IF NOT EXISTS sgcs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgcs;

-- ==========================================
-- CONFIGURACIÓN DE USUARIOS Y ROLES
-- ==========================================

CREATE TABLE usuarios (
  id CHAR(36) PRIMARY KEY,
  correo VARCHAR(255) NOT NULL UNIQUE,
  correo_verificado_en TIMESTAMP NULL,
  nombre_completo VARCHAR(255),
  contrasena_hash TEXT,
  activo BOOLEAN NOT NULL DEFAULT TRUE,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(50) NOT NULL UNIQUE,
  descripcion TEXT
);

CREATE TABLE usuarios_roles (
  id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id CHAR(36),
  rol_id INT,
  proyecto_id CHAR(36) NULL,
  UNIQUE KEY unique_usuario_rol_proyecto (usuario_id, rol_id, proyecto_id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT
);

-- ==========================================
-- PROYECTOS, EQUIPOS Y METODOLOGÍAS
-- ==========================================

CREATE TABLE proyectos (
  id CHAR(36) PRIMARY KEY,
  codigo VARCHAR(50) UNIQUE,
  nombre VARCHAR(255) NOT NULL,
  descripcion TEXT,
  metodologia ENUM('agil','cascada','hibrida') DEFAULT 'agil',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE equipos (
  id CHAR(36) PRIMARY KEY,
  proyecto_id CHAR(36) NOT NULL,
  nombre VARCHAR(255) NOT NULL,
  lider_id CHAR(36),
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
  FOREIGN KEY (lider_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE miembros_equipo (
  equipo_id CHAR(36),
  usuario_id CHAR(36),
  rol_id INT,
  PRIMARY KEY (equipo_id, usuario_id),
  FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (rol_id) REFERENCES roles(id)
);

-- ==========================================
-- ELEMENTOS DE CONFIGURACIÓN (EC)
-- ==========================================

CREATE TABLE elementos_configuracion (
  id CHAR(36) PRIMARY KEY,
  codigo_ec VARCHAR(50) UNIQUE,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT,
  proyecto_id CHAR(36) NOT NULL,
  tipo ENUM('DOCUMENTO','CODIGO','SCRIPT_BD','CASO_PRUEBA','ARTEFACTO','DIAGRAMA','OTRO') NOT NULL,
  padre_id CHAR(36) NULL,
  version_actual_id CHAR(36) NULL,
  estado ENUM('BORRADOR','EN_REVISION','APROBADO','LIBERADO','OBSOLETO') DEFAULT 'BORRADOR',
  metadatos JSON,
  creado_por CHAR(36),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE CASCADE,
  FOREIGN KEY (padre_id) REFERENCES elementos_configuracion(id) ON DELETE SET NULL,
  FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

-- ==========================================
-- VERSIONES DE ELEMENTOS DE CONFIGURACIÓN
-- ==========================================

CREATE TABLE archivos (
  id CHAR(36) PRIMARY KEY,
  ruta_almacenamiento TEXT,
  nombre_archivo TEXT,
  tipo_mime TEXT,
  tamano_bytes BIGINT,
  checksum TEXT,
  subido_por CHAR(36),
  subido_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (subido_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE versiones_ec (
  id CHAR(36) PRIMARY KEY,
  ec_id CHAR(36) NOT NULL,
  version VARCHAR(20) NOT NULL,
  registro_cambios TEXT,
  archivo_id CHAR(36) NULL,
  metadatos JSON,
  estado ENUM('BORRADOR','REVISION','APROBADO','LIBERADO','DEPRECADO') DEFAULT 'BORRADOR',
  creado_por CHAR(36),
  aprobado_por CHAR(36),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  aprobado_en TIMESTAMP NULL,
  FOREIGN KEY (ec_id) REFERENCES elementos_configuracion(id) ON DELETE CASCADE,
  FOREIGN KEY (archivo_id) REFERENCES archivos(id),
  FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE SET NULL,
  FOREIGN KEY (aprobado_por) REFERENCES usuarios(id) ON DELETE SET NULL
);

ALTER TABLE elementos_configuracion
  ADD CONSTRAINT fk_version_actual FOREIGN KEY (version_actual_id)
  REFERENCES versiones_ec(id) ON DELETE SET NULL;

-- ==========================================
-- MATRIZ DE TRAZABILIDAD
-- ==========================================

CREATE TABLE relaciones_ec (
  id CHAR(36) PRIMARY KEY,
  desde_ec CHAR(36) NOT NULL,
  hacia_ec CHAR(36) NOT NULL,
  tipo_relacion ENUM('DEPENDE_DE','DERIVADO_DE','REFERENCIA','REQUERIDO_POR') NOT NULL,
  nota TEXT,
  FOREIGN KEY (desde_ec) REFERENCES elementos_configuracion(id) ON DELETE CASCADE,
  FOREIGN KEY (hacia_ec) REFERENCES elementos_configuracion(id) ON DELETE CASCADE
);

-- ==========================================
-- SOLICITUDES DE CAMBIO (RFC)
-- ==========================================

CREATE TABLE liberaciones (
  id CHAR(36) PRIMARY KEY,
  proyecto_id CHAR(36) NOT NULL,
  etiqueta VARCHAR(50) NOT NULL,
  nombre VARCHAR(255),
  descripcion TEXT,
  fecha_liberacion DATE,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id)
);

CREATE TABLE solicitudes_cambio (
  id CHAR(36) PRIMARY KEY,
  proyecto_id CHAR(36) NOT NULL,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT,
  solicitante_id CHAR(36),
  prioridad ENUM('BAJA','MEDIA','ALTA','CRITICA') DEFAULT 'MEDIA',
  estado ENUM('ABIERTA','EN_REVISION','APROBADA','RECHAZADA','IMPLEMENTADA','CERRADA') DEFAULT 'ABIERTA',
  resumen_impacto TEXT,
  liberacion_objetivo_id CHAR(36),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id),
  FOREIGN KEY (solicitante_id) REFERENCES usuarios(id),
  FOREIGN KEY (liberacion_objetivo_id) REFERENCES liberaciones(id)
);

CREATE TABLE items_cambio (
  id CHAR(36) PRIMARY KEY,
  solicitud_cambio_id CHAR(36),
  ec_id CHAR(36),
  version_actual_ec_id CHAR(36),
  version_propuesta VARCHAR(20),
  nota TEXT,
  FOREIGN KEY (solicitud_cambio_id) REFERENCES solicitudes_cambio(id) ON DELETE CASCADE,
  FOREIGN KEY (ec_id) REFERENCES elementos_configuracion(id),
  FOREIGN KEY (version_actual_ec_id) REFERENCES versiones_ec(id)
);

CREATE TABLE comite_cambios (
  id CHAR(36) PRIMARY KEY,
  proyecto_id CHAR(36),
  nombre VARCHAR(255),
  quorum INT DEFAULT 1,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id)
);

CREATE TABLE miembros_ccb (
  ccb_id CHAR(36),
  usuario_id CHAR(36),
  rol_en_ccb VARCHAR(100),
  PRIMARY KEY (ccb_id, usuario_id),
  FOREIGN KEY (ccb_id) REFERENCES comite_cambios(id) ON DELETE CASCADE,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

CREATE TABLE votos_ccb (
  id CHAR(36) PRIMARY KEY,
  ccb_id CHAR(36),
  solicitud_cambio_id CHAR(36),
  usuario_id CHAR(36),
  voto ENUM('APROBAR','RECHAZAR','ABSTENERSE'),
  comentario TEXT,
  votado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ccb_id) REFERENCES comite_cambios(id),
  FOREIGN KEY (solicitud_cambio_id) REFERENCES solicitudes_cambio(id),
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE items_liberacion (
  id CHAR(36) PRIMARY KEY,
  liberacion_id CHAR(36),
  ec_id CHAR(36),
  version_ec_id CHAR(36),
  FOREIGN KEY (liberacion_id) REFERENCES liberaciones(id) ON DELETE CASCADE,
  FOREIGN KEY (ec_id) REFERENCES elementos_configuracion(id),
  FOREIGN KEY (version_ec_id) REFERENCES versiones_ec(id)
);

-- ==========================================
-- AUDITORÍA Y REGISTROS
-- ==========================================

CREATE TABLE auditorias (
  id CHAR(36) PRIMARY KEY,
  tipo_entidad VARCHAR(100),
  entidad_id CHAR(36),
  accion VARCHAR(50),
  usuario_id CHAR(36),
  detalles JSON,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE accesos (
  id CHAR(36) PRIMARY KEY,
  usuario_id CHAR(36),
  ip VARCHAR(45),
  accion TEXT,
  recurso TEXT,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE notificaciones (
  id CHAR(36) PRIMARY KEY,
  usuario_id CHAR(36),
  tipo VARCHAR(100),
  datos JSON,
  leida BOOLEAN DEFAULT FALSE,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL
);

CREATE TABLE commits_repositorio (
  id CHAR(36) PRIMARY KEY,
  url_repositorio TEXT,
  hash_commit TEXT,
  autor TEXT,
  mensaje TEXT,
  fecha_commit TIMESTAMP,
  ec_id CHAR(36),
  FOREIGN KEY (ec_id) REFERENCES elementos_configuracion(id)
);

-- ==========================================
-- NUEVAS TABLAS COMPLEMENTARIAS
-- ==========================================

-- Registro de incidencias o no conformidades detectadas en auditorías
CREATE TABLE incidencias (
  id CHAR(36) PRIMARY KEY,
  proyecto_id CHAR(36),
  descripcion TEXT,
  tipo ENUM('ERROR','OMISION','CAMBIO_NO_AUTORIZADO','OTRO') DEFAULT 'OTRO',
  severidad ENUM('BAJA','MEDIA','ALTA','CRITICA') DEFAULT 'MEDIA',
  estado ENUM('ABIERTA','EN_PROCESO','RESUELTA','CERRADA') DEFAULT 'ABIERTA',
  reportado_por CHAR(36),
  asignado_a CHAR(36),
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  cerrado_en TIMESTAMP NULL,
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id),
  FOREIGN KEY (reportado_por) REFERENCES usuarios(id),
  FOREIGN KEY (asignado_a) REFERENCES usuarios(id)
);

-- Tabla de métricas de desempeño y control
CREATE TABLE metricas_proyecto (
  id CHAR(36) PRIMARY KEY,
  proyecto_id CHAR(36),
  tipo VARCHAR(100),
  valor DECIMAL(10,2),
  descripcion TEXT,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id)
);

-- Revisiones técnicas y de calidad
CREATE TABLE revisiones (
  id CHAR(36) PRIMARY KEY,
  ec_id CHAR(36),
  version_ec_id CHAR(36),
  revisor_id CHAR(36),
  resultado ENUM('APROBADO','OBSERVADO','RECHAZADO') DEFAULT 'OBSERVADO',
  observaciones TEXT,
  fecha_revision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (ec_id) REFERENCES elementos_configuracion(id),
  FOREIGN KEY (version_ec_id) REFERENCES versiones_ec(id),
  FOREIGN KEY (revisor_id) REFERENCES usuarios(id)
);

-- Respaldos automáticos o manuales del sistema
CREATE TABLE respaldos (
  id CHAR(36) PRIMARY KEY,
  ruta TEXT,
  tipo ENUM('AUTOMATICO','MANUAL') DEFAULT 'MANUAL',
  tamano_mb DECIMAL(10,2),
  realizado_por CHAR(36),
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (realizado_por) REFERENCES usuarios(id)
);

-- Bitácora de implementación
CREATE TABLE bitacora_implementacion (
  id CHAR(36) PRIMARY KEY,
  proyecto_id CHAR(36),
  liberacion_id CHAR(36),
  descripcion TEXT,
  realizado_por CHAR(36),
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id),
  FOREIGN KEY (liberacion_id) REFERENCES liberaciones(id),
  FOREIGN KEY (realizado_por) REFERENCES usuarios(id)
);

-- ==========================================
-- FOREIGN KEYS ADICIONALES
-- ==========================================

-- Agregar la FK que faltaba en usuarios_roles (después de crear proyectos)
ALTER TABLE usuarios_roles 
  ADD CONSTRAINT fk_usuarios_roles_proyecto 
  FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE SET NULL;

-- Agregar tabla de password reset tokens
CREATE TABLE password_reset_tokens (
  correo VARCHAR(255) PRIMARY KEY,
  token VARCHAR(255) NOT NULL,
  creado_en TIMESTAMP NULL
);
