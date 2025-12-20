<?php

    class Configuracion {
        // Datos de conexion
        private $host = "localhost";
        private $user = "DBUSER2025";
        private $password = "DBPSWD2025";
        private $dbname = "UO295645_DB";

        private $conexion;

        public function __construct() {
            $this->conexion = new mysqli($this->host, $this->user, $this->password);

            if ($this->conexion->connect_error) {
                die("Conexion fallida: " . $this->conexion->connect_error);
            }

            $this->conexion->set_charset("utf8");
        }

        // Método para reiniciar la BBDD
        public function reiniciarBaseDatos() {
            try {
                if ($this->conexion->select_db($this->dbname)) {
                    $tablas = ['OBSERVACION', 'RESULTADO', 'USER_INFO', 'DISPOSITIVO'];
                    $errores = [];

                    // desactivar comprobacion de claves foraneas
                    $this->conexion->query("SET FOREIGN_KEY_CHECKS=0;");

                    foreach ($tablas as $tabla) {
                        if ($tabla !== 'DISPOSITIVO') {
                            if(!$this->conexion->query("TRUNCATE TABLE $tabla")) {
                                $errores[] = "Error al truncar la tabla $tabla: " . $this->conexion->error;
                            }
                        }
                    }

                    // Reactivar comprobacion de claves foraneas
                    $this->conexion->query("SET FOREIGN_KEY_CHECKS=1;");

                    return empty($errores) ? "Base de datos reiniciada correctamente." : implode(", ", $errores);
                }
                return "No se pudo seleccionar la base de datos: " . $this->conexion->error;
            }
            catch (Exception $e) {
                return "Error al reiniciar la base de datos: " . $this->dbname;
            }
        }


        // Eliminar la BBDD
        public function eliminarBaseDatos() {
            $sql = "DROP DATABASE IF EXISTS {$this->dbname}";

            if ($this->conexion->query($sql) === TRUE) {
                return "Base de datos eliminada correctamente.";
            }
            else {
                return "Error al eliminar la base de datos: " . $this->conexion->error;
            }
        }


        // Exportar BBDD en formato CSV
        public function exportarBaseDatosCSV($tabla = 'RESULTADO') {

            try {
                $conexion_db = new mysqli($this->host, $this->user, $this->password, $this->dbname);

                if ($conexion_db->connect_error) {
                    die("Conexion fallida: " . $conexion_db->connect_error);
                }

                $query = "SELECT * FROM $tabla";
                $resultado = $conexion_db->query($query);

                if ($resultado === FALSE) {
                    header("HTTP/1.0 404 Not Found");
                    return "Error al consultar la tabla $tabla" . $conexion_db->error;
                }
                
                // nombres de columnas
                $cabecera = [];
                while ($campo = $resultado->fetch_field()) {
                    $cabecera[] = $campo->name;
                }

                // Definir cabeceras archivo
                $f = fopen('php://memory', 'w');

                // Escribir cabecera en el archivo
                fputcsv($f, $cabecera, ';'); 

                // escribir datos
                while ($fila = $resultado->fetch_assoc()) {
                    fputcsv($f, $fila, ';');
                }

                fseek($f, 0); // mueve el puntero al inicio del archivo

                // Establecer cabeceras
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="' . $tabla . '_' . date('Ymd_His') . '.csv"');

                // devolver el contenido del archivo
                fpassthru($f);
                fclose($f);
                exit();
            }
            catch (Exception $e) {
                return "Error al exportar la tabla $tabla: " . $this->dbname;
            }
            
        }

        // Recrear la BBDD
        public function recrearBaseDatos() {
            $sql = "
                -- Usar la BBDD creada
                CREATE DATABASE IF NOT EXISTS {$this->dbname};
                USE {$this->dbname};

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
            ";

            if ($this->conexion->multi_query($sql)) {
            while ($this->conexion->more_results() && $this->conexion->next_result()); 
                return "Estructura de la BBDD '{$this->dbname}' recreada correctamente.";
            }
            return "Error al recrear la BBDD: " . $this->conexion->error;

        }

        public function guardarResultadoCompleto(
            $datosUsuario,
            $dispositivoId,
            $tiempo,
            $completada,
            $comentarioObservador
        ) {

            $stmt = null;

            if (! $this->conexion->select_db($this->dbname)) {
                throw new \Exception("No se pudo seleccionar la base de datos: " . $this->conexion->error);
            }

            $this->conexion->begin_transaction();

            try {
                // INSERCION EN USER_INFO
                $sql_user = "INSERT INTO USER_INFO (profesion, edad, genero, periciaInformatica) VALUES (?, ?, ?, ?)";
                $stmt = $this->conexion->prepare($sql_user);

                $stmt->bind_param("sisi", $datosUsuario['profesion'], $datosUsuario['edad'], $datosUsuario['genero'], $datosUsuario['periciaInformatica']);

                $stmt->execute();
                $userId = $this->conexion->insert_id;
                $stmt->close();
                $stmt = null;

                // INSERCION EN RESULTADO
                $sql_res = "INSERT INTO RESULTADO (userId, dispositivoId, tiempoRealizacion, completada, comentario, propuestaMejora, valoracion) VALUES (?, ?, ?, ?, ?, ?, ?)";

                if (!$stmt = $this->conexion->prepare($sql_res)) {
                     throw new \Exception("Error prepare RESULTADO: " . $this->conexion->error);
                }

                 $completada_int = (int)$completada;

                
                if (!$stmt->bind_param("iidissi", $userId, $dispositivoId, $tiempo, $completada_int, $datosUsuario['comentario'], $datosUsuario['propuestaMejora'], $datosUsuario['valoracion'])) {
                    throw new \Exception("Error en bind_param (RESULTADO): " . $stmt->error);
                }

                $stmt->execute();
                $resultadoId = $this->conexion->insert_id;
                $stmt->close();
                $stmt = null;

                // INSERCION EN OBSERVACIONES
                if (!empty(trim($comentarioObservador))) {
                    $sql_obs = "INSERT INTO OBSERVACION (resultadoId, comentario) VALUES (?, ?)";

                    if (!$stmt = $this->conexion->prepare($sql_obs)) {
                        throw new \Exception("Error prepare OBSERVACION: " . $this->conexion->error);
                    }
                    
                    $stmt->bind_param("is", $resultadoId, $comentarioObservador);

                    $stmt->execute();
                    $stmt->close();
                    $stmt = null;
                }

                // si todo ha ido bien, confirmar transacción
                $this->conexion->commit();
                return true;
            }
            catch (mysqli_sql_exception $e) {
                $this->conexion->rollback();
                $stmt->close();
                throw new \Exception("Error al guardar los datos: " . $e->getMessage());
            }
        }

    }

?>