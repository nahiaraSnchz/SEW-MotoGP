<?php
    require_once 'Configuracion.php';

    $mensaje = "";

    $config = new Configuracion();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        if (isset($_FILES['archivo_csv']) && $_FILES['archivo_csv']['error'] === UPLOAD_ERR_OK) {
            $archivo = $_FILES['archivo_csv'];
            
            if ($archivo['error'] === UPLOAD_ERR_OK) {
                try {
                    $mensaje = $config->importarBaseDatosCSV($archivo['tmp_name']);
                } catch (Exception $e) {
                    $mensaje = "Error en la importación: " . $e->getMessage();
                }
            } elseif ($archivo['error'] === UPLOAD_ERR_NO_FILE) {
                $mensaje = "No se seleccionó ningún archivo.";
            } else {
                $mensaje = "Error de subida código: " . $archivo['error'];
            }
        }
        elseif (isset($_POST['reiniciar'])) {
            $mensaje = $config->reiniciarBaseDatos();
        }
        elseif (isset($_POST['eliminar'])) {
            $mensaje = $config->eliminarBaseDatos();
        }
        elseif (isset($_POST['exportar'])) {
            $mensaje = $config->exportarBaseDatosCSV('RESULTADO');
        }
        elseif (isset($_POST['recrear'])) {
            $mensaje = $config->recrearBaseDatos();
        }
        else {
            $mensaje = "Petición POST recibida, pero no se reconoce la acción.";
        }
    }
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>Configuración BBDD</title>
    <meta name ="author" content ="Nahiara Sánchez García" /> 
    <meta name ="description" content ="Archivo para la configuración de la BBDD" /> 
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" /> 

     <!-- Enlaces -->
     <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
     <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />

     <!--FavIcon-->
    <link rel="icon" href="../multimedia/favicon.ico" />
     
</head>

<body>

    <header>
        <h1>Configuración de la Base de Datos</h1>
    </header>

    <p>Estas en: <a href="../index.html" title="Página Inicio">Inicio</a> | Configuración BBDD</p>

    <h2>Gestión de la Base de Datos</h2>

    <p>Herramientas para la gestión de la base de datos:</p>

    <main>
        <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST" enctype="multipart/form-data">
            <button type="submit" name="reiniciar">Reiniciar BBDD</button>
            <button type="submit" name="eliminar">Eliminar BBDD</button>
            <button type="submit" name="exportar">Exportar BBDD</button>
            <button type="submit" name="recrear">Recrear BBDD</button>

            <h3>Importar BBDD desde archivo CSV:</h3>
            <label>
                Importar BBDD (CSV)
                <input type="file" name="archivo_csv" accept=".csv" onchange="this.form.submit()" hidden>
            </label>
        </form>

    </main>

    <?php if (!empty($mensaje)): ?>
            <section>
                <?php echo $mensaje; ?>
            </section>
    <?php endif; ?>

</body>

</html>

