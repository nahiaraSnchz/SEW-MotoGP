<!DOCTYPE HTML>

<html lang="es">
<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>MotoGP-Clasificaciones</title>
    <meta name ="author" content ="Nahiara Sánchez García" /> 
    <meta name ="description" content ="Documento para representar las clasificaciones." /> 
    <meta name ="keywords" content ="motogp, moto, clasificación" /> 
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" /> 

     <!-- Enlaces -->
     <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
     <link rel="stylesheet" type="text/css" href="estilo/layout.css" />

     <!--FavIcon-->
    <link rel="icon" href="multimedia/favicon.ico" />
     
</head>

<body>

    <header>

        <!-- Datos con el contenidos que aparece en el navegador -->
        <h1><a href="index.html" title="Página Inicio">MotoGP-Desktop</a></h1>

        <nav>
            <a href="index.html" title="Página Inicio">Inicio</a>
            <a href="piloto.html" title="Página Piloto de MotoGP">Piloto</a>
            <a href="circuito.html" title="Página Circuito">Circuito</a>
            <a href="meteorologia.html" title="Página Meteorología  ">Meteorología</a>
            <a class="active" href="clasificaciones.php" title="Página Clasificaciones">Clasificaciones</a>
            <a href="juegos.html" title="Página Juegos">Juegos</a>
            <a href="ayuda.html" title="Página Ayuda">Ayuda</a>
        </nav>
    
    </header>

    <p>Estas en: <a href="piloto.html" title="Página Inicio">Inicio</a> | <strong>Clasificaciones</strong></p>

    <h2>Clasificaciones de MotoGP-Desktop</h2>
    
    <?php
        class Clasificacion {

            private $documento;

            public function __construct() {
                $this->documento = 'xml/circuitoEsquema.xml';
            }

            public function consultar() {
                $datos = file_get_contents($this->documento);

                if ($datos === false) {
                    return null;
                }

                $xml = new SimpleXMLElement($datos);

                return $xml;
            }

            public function mostrarGanadorYTiempo($xml) {
                if (!$xml) {
                    echo "<p>Error al cargar el archivo XML.</p>";
                    return;
                }

                $ganador = (string)$xml->vencedor;
                $tiempo = (string)$xml->tiempoVencedor;

                echo "<section>";
                echo "<h3>Ganador de la carrera y tiempo empleado:</h3>";
                echo "<ul>";
                echo "<li>Ganador: $ganador</li>";
                echo "<li>Tiempo: $tiempo</li>";
                echo "</ul>";
                echo "</section>";
            }

            public function mostrarClasificados($xml) {
                if (!$xml) {
                    echo "<p>Error al cargar el archivo XML.</p>";
                    return;
                }

                $clasificados = $xml->tresClasificados;

                echo "<section>";
                echo "<h3>Top 3 Clasificados:</h3>";
                echo "<ol>";
                $posicion = 1;

                foreach ($clasificados->clasificado as $piloto) {
                    echo "<li>Posición $posicion: $piloto</li>";
                    $posicion++;
                }
                echo "</ol>";
                echo "</section>";
            }

        }

        $clasificacion = new Clasificacion();
        $datos_xml = $clasificacion->consultar();
        $clasificacion->mostrarGanadorYTiempo($datos_xml);
        $clasificacion->mostrarClasificados($datos_xml);
        
    ?>


</body>
</html>