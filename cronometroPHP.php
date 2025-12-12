<?php
    session_start();
    require_once 'cronometro.php';
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <title>MotoGP-Cronómetro</title>
    <meta name ="author" content ="Nahiara Sánchez García" /> 
    <meta name ="description" content ="Documento inicial (índice) del proyecto." /> 
    <meta name ="keywords" content ="motogp, cronómetro, tiempo, carreras" /> 
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" /> 

    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />

    <link rel="icon" href="multimedia/favicon.ico" />

</head>

<body>

    <header>
        <h1>MotoGP Desktop</h1>
        <nav>
            <a href="index.html" title="Página Inicio">Inicio</a>
            <a href="piloto.html" title="Página Piloto de MotoGP">Piloto</a>
            <a href="circuito.html" title="Página Circuito">Circuito</a>
            <a href="meteorologia.html" title="Página Meteorología  ">Meteorología</a>
            <a href="clasificaciones.php" title="Página Clasificaciones">Clasificaciones</a>
            <a href="juegos.html" title="Página Juegos">Juegos</a>
            <a href="ayuda.html" title="Página Ayuda">Ayuda</a>
        </nav>

    </header>

    <p>Estas en: <a href="index.html" title="Página Inicio">Inicio</a> | <strong>Cronómetro</strong></p>

    <h2>Cronómetro</h2>

    <?php 
        // Mostrar el mensaje de estado si existe
        if ($mensaje) {
             echo "<p>{$mensaje}</p>";
        }
    ?>

    <section>
        <h3>Control de Tiempo</h3>
        <p>Tiempo: <?php echo $miCronometro->mostrar(); ?> </p>

        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" name="cronometro">
            <input type='submit' name='arrancar' value='Arrancar Cronómetro' />
            <input type='submit' name='parar' value='Parar Cronómetro' />
            <input type='submit' name='mostrar' value='Mostrar Tiempo' />
            <input type='submit' name='resetear' value='Resetear Cronómetro' />
        </form>
    </section>

</body>
</html>