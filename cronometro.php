<?php
    // Para mantener el estado del cronómetro
    session_start();
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>MotoGP-Cronómetro</title>
    <meta name ="author" content ="Nahiara Sánchez García" /> 
    <meta name ="description" content ="Documento inicial (índice) del proyecto." /> 
    <meta name ="keywords" content ="motogp, cronómetro, tiempo, carreras" /> 
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" /> 

     <!-- Enlaces -->
     <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
     <link rel="stylesheet" type="text/css" href="estilo/layout.css" />

     <!--FavIcon-->
    <link rel="icon" href="multimedia/favicon.ico" />

</head>

<body>

    <header>
        <h1>MotoGP Desktop</h1>
        <nav>
            <a href="index.html" title="Página Inicio">Inicio</a>
            <a href="piloto.html" title="Página Piloto de MotoGP">Piloto</a>
            <a href="circuito.html" title="Página Circuito">Circuito</a>
            <a href="meteorologia.html" title="Página Meteorología  ">Meteorología</a>
            <a href="clasificaciones.php" title="Página Clasificaciones">Clasificaciones</a>
            <a href="juegos.html" title="Página Juegos">Juegos</a>
            <a href="ayuda.html" title="Página Ayuda">Ayuda</a>
        </nav>

    </header>

    <p>Estas en: <a href="index.html" title="Página Inicio">Inicio</a> | <strong>Cronómetro</strong></p>

    <h2>Cronómetro</h2>

    <?php
        class Cronometro {
            private $tiempo;
            private $inicio;

            public function __construct() {
                $this->tiempo = 0;
                $this->inicio = null;
            }

            public function arrancar() {
                $this->inicio = microtime(true);
            }

            public function parar() {
                if ($this->inicio !== null) {
                    $momentoFinal = microtime(true);
                    $tiempoTranscurrido = $momentoFinal - $this->inicio;
                    $this->tiempo += $tiempoTranscurrido;
                    $this->inicio = null;
                }
            }

            public function mostrar() {
                $tiempoTotal = $this->tiempo;

                // si el cronometro está activo
                if ($this->inicio !== null) {
                    $tiempoTotal = microtime(true) - $this->inicio;
                }

                $minutos = floor($tiempoTotal / 60);
                $segundosDecimal = $tiempoTotal - ($minutos * 60);

                $segundos = floor($segundosDecimal);
                $decimas = floor(($segundosDecimal - $segundos) * 10);

                $formatoMinutos = str_pad($minutos, 2, '0', STR_PAD_LEFT);
                $formatoSegundos = str_pad($segundos, 2, '0', STR_PAD_LEFT);

                return $formatoMinutos . ':' . $formatoSegundos . '.' . $decimas;
            }

            public function getInicio() { return $this->inicio; }
            public function getTiempo() { return $this->tiempo; }
        }

        // Lógica para manejar el cronómetro
        if (isset($_SESSION['miCronometro'])) {
            $miCronometro = $_SESSION['miCronometro'];
        }
        else {
            $miCronometro = new Cronometro();
        }

        $mensaje = "";

        // Manejar las acciones del formulario
        if (count($_POST) > 0) {
            if (isset($_POST['arrancar'])) {
                $miCronometro->arrancar();
                $mensaje = "Cronómetro arrancado.";
            } 
            if (isset($_POST['parar'])) {
                // verificar si el cronómetro está en marcha
                if ($miCronometro->getInicio() !== null) {
                    $miCronometro->parar();
                    $mensaje = "Cronómetro parado.";
                }
                else {
                    $mensaje = "El cronómetro no está en marcha.";
                }
            }
            if (isset($_POST['mostrar'])) {
                $tiempo_actual = $miCronometro->mostrar();
                $estado = ($miCronometro->getInicio() !== null) ? "en marcha" : "detenido";
                $mensaje = "Tiempo actual: " . $tiempo_actual . " (Cronómetro " . $estado . ").";
            }

            // guardar objeto completo en la sesión
            $_SESSION['miCronometro'] = $miCronometro;

            if ($mensaje) {
                echo "<p>" . $mensaje . "</p>";
            }
            
        }

    ?>

    <section>
            <h3>Control de Tiempo</h3>
            <p>Tiempo: 
                <?php echo $miCronometro->mostrar(); ?>
            </p>

            <form action="#" method="post" name="cronometro">
                <input type='submit' name='arrancar' value='Arrancar Cronómetro' />
                <input type='submit' name='parar' value='Parar Cronómetro' />
                <input type='submit' name='mostrar' value='Mostrar Tiempo' />
            </form>
    </section>

</body>
</html>