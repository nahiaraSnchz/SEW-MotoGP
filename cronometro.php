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

    <!-- jQuery 3.7.1 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"
        integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo="
        crossorigin="anonymous"></script>

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
            }

            public function arrancar() {
                $this->inicio = microtime(true);
            }

            public function parar() {
                $momentoFinal = microtime(true);
                $tiempoTranscurrido = $momentoFinal - $this->inicio;
                $this->tiempo += $tiempoTranscurrido;
                $this->inicio = null;
            }

            public function mostrar() {
                $totalSegundos = $this->tiempo;

                $minutos = floor($totalSegundos / 60);
                $segundosDecimal = $totalSegundos - ($minutos * 60);

                $formatoMinutos = str_pad($minutos, 2, '0', STR_PAD_LEFT);
                $formatoSegundos = number_format($segundosDecimal, 3, '.', '');

                if ($segundosDecimal < 10) {
                    $formatoSegundos = '0' . $formatoSegundos;
                }

                return $formatoMinutos . ':' . $formatoSegundos;
            }

            public function getInicio() { return $this->inicio; }
            public function getTiempo() { return $this->tiempo; }
            public function setInicio($inicio) { $this->inicio = $inicio; }
            public function setTiempo($tiempo) { $this->tiempo = $tiempo; }
        }

        // Lógica para manejar el cronómetro
        if (isset($_SESSION['cronometro_obj'])) {
            $crono = $_SESSION['cronometro_obj'];
            // Reconstruir el objeto Cronometro
            $temp = new Cronometro();
            $temp->setTiempo($crono['tiempo']);
            $temp->setInicio($crono['inicio']);
            $miCronometro = $temp;
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
                $miCronometro->parar();
                $mensaje = "Cronómetro parado.";
            }

            $_SESSION['cronometro_obj'] = [
                'tiempo' => $miCronometro->getTiempo(),
                'inicio' => $miCronometro->getInicio()
            ];

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