-- ==========================================
-- BASE DE DATOS DEL SISTEMA DE GESTIÓN DE PROYECTOS
-- ==========================================
CREATE DATABASE IF NOT EXISTS sgcs CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sgcs;

-- ==========================================
-- Capa: Usuarios y Seguridad
-- ==========================================

CREATE TABLE usuarios (
    id CHAR(36) PRIMARY KEY,
    correo VARCHAR(255) NOT NULL UNIQUE,
    correo_verificado_en TIMESTAMP NULL,
    nombre_completo VARCHAR(255) NOT NULL,
    contrasena_hash VARCHAR(255) NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    remember_token VARCHAR(100) NULL,
    google2fa_secret VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE usuarios_roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id CHAR(36) NOT NULL,
    rol_id INT NOT NULL,
    proyecto_id CHAR(36) NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id)
);

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
);

CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id CHAR(36) NOT NULL,
    ip_address VARCHAR(50),
    user_agent VARCHAR(255),
    payload TEXT,
    last_activity TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE accesos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id CHAR(36) NOT NULL,
    ip VARCHAR(50),
    accion VARCHAR(100),
    recurso VARCHAR(255),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- ==========================================
-- Capa: Proyectos y Metodologías
-- ==========================================

CREATE TABLE metodologias (
    id_metodologia INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    tipo VARCHAR(50),
    descripcion VARCHAR(255)
);

CREATE TABLE fases_metodologia (
    id_fase INT AUTO_INCREMENT PRIMARY KEY,
    id_metodologia INT NOT NULL,
    nombre_fase VARCHAR(100) NOT NULL,
    orden INT,
    descripcion VARCHAR(255),
    FOREIGN KEY (id_metodologia) REFERENCES metodologias(id_metodologia)
);

CREATE TABLE proyectos (
    id CHAR(36) PRIMARY KEY,
    codigo VARCHAR(50) NOT NULL UNIQUE,
    nombre VARCHAR(255) NOT NULL,
    descripcion TEXT,
    id_metodologia INT NOT NULL,
    link_repositorio VARCHAR(255),
    creado_por CHAR(36) NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_metodologia) REFERENCES metodologias(id_metodologia),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id)
);

CREATE TABLE equipos (
    id CHAR(36) PRIMARY KEY,
    proyecto_id CHAR(36) NOT NULL,
    nombre VARCHAR(255) NOT NULL,
    lider_id CHAR(36) NOT NULL,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id),
    FOREIGN KEY (lider_id) REFERENCES usuarios(id)
);

CREATE TABLE miembros_equipo (
    equipo_id CHAR(36) NOT NULL,
    usuario_id CHAR(36) NOT NULL,
    rol_id INT NOT NULL,
    PRIMARY KEY (equipo_id, usuario_id, rol_id),
    FOREIGN KEY (equipo_id) REFERENCES equipos(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

CREATE TABLE tareas_proyecto (
    id_tarea INT AUTO_INCREMENT PRIMARY KEY,
    id_proyecto CHAR(36) NOT NULL,
    id_fase INT NOT NULL,
    id_ec CHAR(36),
    responsable CHAR(36),
    fecha_inicio DATE,
    fecha_fin DATE,
    estado VARCHAR(50),
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id),
    FOREIGN KEY (id_fase) REFERENCES fases_metodologia(id_fase),
    FOREIGN KEY (id_ec) REFERENCES elementos_configuracion(id),
    FOREIGN KEY (responsable) REFERENCES usuarios(id)
);

-- ==========================================
-- Capa: Gestión de Configuración
-- ==========================================

CREATE TABLE elementos_configuracion (
    id CHAR(36) PRIMARY KEY,
    codigo_ec VARCHAR(50) NOT NULL UNIQUE,
    titulo VARCHAR(255) NOT NULL,
    descripcion TEXT,
    proyecto_id CHAR(36) NOT NULL,
    tipo ENUM('DOCUMENTO','CODIGO','SCRIPT_BD','CONFIGURACION','OTRO') DEFAULT 'OTRO',
    version_actual_id CHAR(36),
    creado_por CHAR(36),
    estado ENUM('BORRADOR','EN_REVISION','APROBADO','LIBERADO','OBSOLETO') DEFAULT 'BORRADOR',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id),
    FOREIGN KEY (version_actual_id) REFERENCES versiones_ec(id),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id)
);

CREATE TABLE versiones_ec (
    id CHAR(36) PRIMARY KEY,
    ec_id CHAR(36) NOT NULL,
    version VARCHAR(50) NOT NULL,
    registro_cambios TEXT,
    commit_id VARCHAR(255) NULL,
    creado_por CHAR(36),
    aprobado_por CHAR(36),
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    aprobado_en TIMESTAMP NULL,
    FOREIGN KEY (ec_id) REFERENCES elementos_configuracion(id),
    FOREIGN KEY (creado_por) REFERENCES usuarios(id),
    FOREIGN KEY (aprobado_por) REFERENCES usuarios(id)
);

CREATE TABLE relaciones_ec (
    id CHAR(36) PRIMARY KEY,
    desde_ec CHAR(36) NOT NULL,
    hacia_ec CHAR(36) NOT NULL,
    tipo_relacion ENUM('DEPENDE_DE','DERIVADO_DE','REFERENCIA','REQUERIDO_POR') NOT NULL,
    nota TEXT,
    FOREIGN KEY (desde_ec) REFERENCES elementos_configuracion(id),
    FOREIGN KEY (hacia_ec) REFERENCES elementos_configuracion(id)
);

-- ==========================================
-- Capa: Cambios y CCB
-- ==========================================

CREATE TABLE solicitudes_cambio (
    id CHAR(36) PRIMARY KEY,
    proyecto_id CHAR(36) NOT NULL,
    titulo VARCHAR(255),
    descripcion_cambio TEXT,
    motivo_cambio TEXT,
    prioridad ENUM('BAJA','MEDIA','ALTA','CRITICA') DEFAULT 'MEDIA',
    estado ENUM('ABIERTA','EN_REVISION','APROBADA','RECHAZADA','IMPLEMENTADA','CERRADA') DEFAULT 'ABIERTA',
    solicitante_id CHAR(36),
    resumen_impacto TEXT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id),
    FOREIGN KEY (solicitante_id) REFERENCES usuarios(id)
);

CREATE TABLE items_cambio (
    id CHAR(36) PRIMARY KEY,
    solicitud_cambio_id CHAR(36) NOT NULL,
    ec_id CHAR(36) NOT NULL,
    version_actual_ec_id CHAR(36),
    version_propuesta VARCHAR(50),
    nota TEXT,
    FOREIGN KEY (solicitud_cambio_id) REFERENCES solicitudes_cambio(id),
    FOREIGN KEY (ec_id) REFERENCES elementos_configuracion(id),
    FOREIGN KEY (version_actual_ec_id) REFERENCES versiones_ec(id)
);

CREATE TABLE comite_cambios (
    id CHAR(36) PRIMARY KEY,
    proyecto_id CHAR(36) NOT NULL,
    nombre VARCHAR(255),
    quorum INT,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id)
);

CREATE TABLE miembros_ccb (
    ccb_id CHAR(36) NOT NULL,
    usuario_id CHAR(36) NOT NULL,
    rol_en_ccb VARCHAR(100),
    PRIMARY KEY (ccb_id, usuario_id),
    FOREIGN KEY (ccb_id) REFERENCES comite_cambios(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE votos_ccb (
    id CHAR(36) PRIMARY KEY,
    ccb_id CHAR(36) NOT NULL,
    solicitud_cambio_id CHAR(36) NOT NULL,
    usuario_id CHAR(36) NOT NULL,
    voto ENUM('APROBAR','RECHAZAR','ABSTENERSE') NOT NULL,
    comentario TEXT,
    votado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ccb_id) REFERENCES comite_cambios(id),
    FOREIGN KEY (solicitud_cambio_id) REFERENCES solicitudes_cambio(id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

-- ==========================================
-- Capa: Liberaciones
-- ==========================================

CREATE TABLE liberaciones (
    id CHAR(36) PRIMARY KEY,
    proyecto_id CHAR(36) NOT NULL,
    etiqueta VARCHAR(50),
    nombre VARCHAR(255),
    descripcion TEXT,
    fecha_liberacion DATE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id)
);

CREATE TABLE items_liberacion (
    id CHAR(36) PRIMARY KEY,
    liberacion_id CHAR(36) NOT NULL,
    ec_id CHAR(36) NOT NULL,
    version_ec_id CHAR(36) NOT NULL,
    FOREIGN KEY (liberacion_id) REFERENCES liberaciones(id),
    FOREIGN KEY (ec_id) REFERENCES elementos_configuracion(id),
    FOREIGN KEY (version_ec_id) REFERENCES versiones_ec(id)
);

-- ==========================================
-- Capa: Auditoría y Notificaciones
-- ==========================================

CREATE TABLE auditorias (
    id CHAR(36) PRIMARY KEY,
    tipo_entidad VARCHAR(100),
    entidad_id CHAR(36),
    accion VARCHAR(100),
    usuario_id CHAR(36),
    detalles JSON,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE notificaciones (
    id CHAR(36) PRIMARY KEY,
    usuario_id CHAR(36) NOT NULL,
    tipo VARCHAR(50),
    datos JSON,
    leida BOOLEAN DEFAULT FALSE,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

