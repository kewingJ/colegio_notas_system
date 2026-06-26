-- ============================================================
-- SISTEMA DE NOTAS - INSTITUTO PEDAGÓGICO ARCOÍRIS
-- schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS sistema_notas_arcoiris
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sistema_notas_arcoiris;

-- -----------------------------------------------
-- CONFIGURACIÓN DEL SISTEMA
-- -----------------------------------------------
CREATE TABLE configuracion (
  clave  VARCHAR(50) PRIMARY KEY,
  valor  TEXT,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------
-- ROLES DE USUARIO
-- -----------------------------------------------
CREATE TABLE roles (
  id        TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre    VARCHAR(30) NOT NULL UNIQUE,   -- 'administrador', 'profesor', 'alumno', 'tutor'
  descripcion VARCHAR(150),
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO roles (nombre, descripcion) VALUES
  ('administrador', 'Acceso total al sistema'),
  ('profesor',      'Gestión de notas de sus materias asignadas'),
  ('alumno',        'Consulta de sus propias notas'),
  ('tutor',         'Consulta de notas de sus hijos');

-- -----------------------------------------------
-- USUARIOS DEL SISTEMA
-- -----------------------------------------------
CREATE TABLE usuarios (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  rol_id        TINYINT UNSIGNED NOT NULL,
  nombre        VARCHAR(120) NOT NULL,
  email         VARCHAR(180) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  activo        TINYINT(1) DEFAULT 1,
  -- Para profesores: datos adicionales
  telefono      VARCHAR(30),
  especialidad  VARCHAR(100),
  -- Para alumnos/tutores: referencia al sistema principal
  alumno_api_id INT UNSIGNED NULL,  -- id del alumno en la API externa
  -- Auditoría
  ultimo_login  DATETIME,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by    INT UNSIGNED NULL,
  CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id) REFERENCES roles(id),
  INDEX idx_email (email),
  INDEX idx_rol (rol_id),
  INDEX idx_alumno_api (alumno_api_id)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- CACHÉ DE LA API REST EXTERNA
-- -----------------------------------------------
CREATE TABLE api_cache (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  endpoint    VARCHAR(100) NOT NULL,   -- ej: '/alumnos', '/niveles-grados'
  data_json   LONGTEXT NOT NULL,
  fetched_at  DATETIME NOT NULL,
  expires_at  DATETIME NOT NULL,
  INDEX idx_endpoint_expires (endpoint, expires_at)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- NIVELES (espejo local de la API para integridad)
-- -----------------------------------------------
CREATE TABLE niveles (
  id          INT UNSIGNED PRIMARY KEY,   -- mismo id que viene de la API
  nombre      VARCHAR(80) NOT NULL,
  api_sync_at DATETIME,
  INDEX idx_nombre (nombre)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- GRADOS
-- -----------------------------------------------
CREATE TABLE grados (
  id          INT UNSIGNED PRIMARY KEY,   -- mismo id que viene de la API
  nivel_id    INT UNSIGNED NOT NULL,
  nombre      VARCHAR(80) NOT NULL,
  api_sync_at DATETIME,
  CONSTRAINT fk_grados_nivel FOREIGN KEY (nivel_id) REFERENCES niveles(id),
  INDEX idx_nivel (nivel_id)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- MATERIAS
-- -----------------------------------------------
CREATE TABLE materias (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre        VARCHAR(120) NOT NULL,
  codigo        VARCHAR(20) UNIQUE,         -- Ej: MAT-SEC-7
  nivel_id      INT UNSIGNED NOT NULL,
  grado_id      INT UNSIGNED NOT NULL,
  descripcion   TEXT,
  cupo_maximo   INT UNSIGNED DEFAULT 0,
  activa        TINYINT(1) DEFAULT 1,
  created_at    DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at    DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by    INT UNSIGNED,
  CONSTRAINT fk_materias_nivel FOREIGN KEY (nivel_id) REFERENCES niveles(id),
  CONSTRAINT fk_materias_grado FOREIGN KEY (grado_id) REFERENCES grados(id),
  INDEX idx_nivel_grado (nivel_id, grado_id),
  INDEX idx_activa (activa)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- ASIGNACIÓN PROFESOR → MATERIA (puede tener varias)
-- -----------------------------------------------
CREATE TABLE profesor_materia (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  profesor_id     INT UNSIGNED NOT NULL,
  materia_id      INT UNSIGNED NOT NULL,
  anio_lectivo    YEAR NOT NULL,
  seccion         VARCHAR(5) DEFAULT 'A',
  activo          TINYINT(1) DEFAULT 1,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pm_profesor FOREIGN KEY (profesor_id) REFERENCES usuarios(id),
  CONSTRAINT fk_pm_materia  FOREIGN KEY (materia_id)  REFERENCES materias(id),
  UNIQUE KEY uq_profesor_materia_anio_seccion (profesor_id, materia_id, anio_lectivo, seccion),
  INDEX idx_materia (materia_id),
  INDEX idx_anio (anio_lectivo)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- HORARIOS DEL PROFESOR
-- -----------------------------------------------
CREATE TABLE horarios (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  profesor_id     INT UNSIGNED NOT NULL,
  materia_id      INT UNSIGNED NOT NULL,
  dia_semana      TINYINT UNSIGNED NOT NULL,   -- 1=Lunes ... 6=Sábado
  hora_inicio     TIME NOT NULL,
  hora_fin        TIME NOT NULL,
  aula            VARCHAR(30),
  anio_lectivo    YEAR NOT NULL,
  seccion         VARCHAR(5) DEFAULT 'A',
  activo          TINYINT(1) DEFAULT 1,
  created_at      DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at      DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  created_by      INT UNSIGNED,
  CONSTRAINT fk_horarios_profesor FOREIGN KEY (profesor_id) REFERENCES usuarios(id),
  CONSTRAINT fk_horarios_materia  FOREIGN KEY (materia_id)  REFERENCES materias(id),
  INDEX idx_profesor_anio (profesor_id, anio_lectivo),
  INDEX idx_dia (dia_semana)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- LOG DE AUDITORÍA (acciones importantes)
-- -----------------------------------------------
CREATE TABLE audit_log (
  id          BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id  INT UNSIGNED,
  accion      VARCHAR(80) NOT NULL,
  tabla       VARCHAR(60),
  registro_id INT UNSIGNED,
  detalle     JSON,
  ip          VARCHAR(45),
  created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_usuario (usuario_id),
  INDEX idx_created (created_at)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- PERIODOS ACADÉMICOS
-- -----------------------------------------------
CREATE TABLE periodos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO periodos (nombre) VALUES ('I Parcial'), ('II Parcial'), ('Examen');

-- -----------------------------------------------
-- INSCRIPCIONES (Alumno -> Materia/Sección)
-- -----------------------------------------------
CREATE TABLE inscripciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    alumno_carnet VARCHAR(20) NOT NULL,
    profesor_materia_id INT UNSIGNED NOT NULL,
    anio_lectivo YEAR NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_insc_pm FOREIGN KEY (profesor_materia_id) REFERENCES profesor_materia(id),
    INDEX idx_alumno_carnet (alumno_carnet),
    INDEX idx_anio (anio_lectivo)
) ENGINE=InnoDB;

-- -----------------------------------------------
-- CALIFICACIONES
-- -----------------------------------------------
CREATE TABLE calificaciones (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    inscripcion_id INT UNSIGNED NOT NULL,
    periodo_id INT UNSIGNED NOT NULL,
    nota DECIMAL(5,2) DEFAULT 0.00,
    observaciones TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_calif_insc FOREIGN KEY (inscripcion_id) REFERENCES inscripciones(id),
    CONSTRAINT fk_calif_periodo FOREIGN KEY (periodo_id) REFERENCES periodos(id),
    UNIQUE KEY uq_insc_periodo (inscripcion_id, periodo_id)
) ENGINE=InnoDB;
