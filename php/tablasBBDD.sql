-- Usar la BBDD creada
USE UO295645_DB;

-- Eliminar tablas si existen
DROP TABLE IF EXISTS OBSERVACION;
DROP TABLE IF EXISTS RESULTADO;
DROP TABLE IF EXISTS USER_INFO;
DROP TABLE IF EXISTS DISPOSITIVO;

-- 1. Tabla USER_INFO (Datos del usuario que hace la prueba)
CREATE TABLE USER_INFO (
    userId INT PRIMARY KEY AUTO_INCREMENT,
    profesion VARCHAR(50),
    edad INT ,
    genero ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    periciaInformatica INT NOT NULL,

    CONSTRAINT chk_edad CHECK (edad > 0),
    CONSTRAINT chk_pericia CHECK (periciaInformatica BETWEEN 1 AND 10)
) ENGINE=InnoDB;

-- 2. Tabla DISPOSITIVO (Dispositivos utilizados en las pruebas)
CREATE TABLE DISPOSITIVO (
    dispositivoId INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- 3. Tabla RESULTADO (Resultados de las pruebas de usabilidad)
CREATE TABLE RESULTADO (
    resultadoId INT PRIMARY KEY AUTO_INCREMENT,
    userId INT NOT NULL,
    dispositivoId INT NOT NULL,
    tiempoRealizacion DECIMAL(10, 2) NOT NULL,
    completada BOOLEAN NOT NULL,
    comentario TEXT,
    propuestaMejora TEXT,
    valoracion INT NOT NULL,

    FOREIGN KEY (userId) REFERENCES USER_INFO(userId) ON DELETE CASCADE,
    FOREIGN KEY (dispositivoId) REFERENCES DISPOSITIVO(dispositivoId) ON DELETE CASCADE,

    CONSTRAINT chk_tiempo CHECK (tiempoRealizacion >= 0),
    CONSTRAINT chk_valoracion CHECK (valoracion BETWEEN 0 AND 10)
) ENGINE=InnoDB;

-- 4. Tabla OBSERVACION (Observaciones realizadas durante las pruebas por el observador)
CREATE TABLE OBSERVACION (
    observacionId INT PRIMARY KEY AUTO_INCREMENT,
    resultadoId INT NOT NULL,
    comentario TEXT,

    FOREIGN KEY (resultadoId) REFERENCES RESULTADO(resultadoId) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Datos fijos de la tabla DISPOSITIVO
INSERT INTO DISPOSITIVO (nombre) VALUES
('Ordenador'),
('Tablet'),
('Teléfono');