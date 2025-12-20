<?php
    require_once 'Configuracion.php';

    $mensaje = "";

    $config = new Configuracion();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['reiniciar'])) {
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

    <p>Herramientas para la gestión de la base de datos:</p>

    <main>
        <form method="POST">
            <button type="submit" name="reiniciar">Reiniciar BBDD</button>
            <button type="submit" name="eliminar">Eliminar BBDD</button>
            <button type="submit" name="exportar">Exportar BBDD</button>
            <button type="submit" name="recrear">Recrear BBDD</button>
        </form>

    </main>

    <?php if (!empty($mensaje)): ?>
            <section>
                <?php echo $mensaje; ?>
            </section>
    <?php endif; ?>

</body>

</html>

