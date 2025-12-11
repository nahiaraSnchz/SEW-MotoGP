<?php

    $mensaje_estado = "";

    // Procesar formulario
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $respuestas_vacias = 0;
        $total_preguntas = 10;

        for ($i = 1; $i <= $total_preguntas; $i++) {
            if (empty($_POST["p$i"])) {
                $respuestas_vacias++;
            }
        }

        if ($respuestas_vacias > 0) {
            $mensaje_estado = "Por favor, responde a todas las preguntas antes de enviar el formulario.";
        } else {
            $respuestas_recibidas = $_POST;

            $mensaje_estado = "Formulario enviado correctamente. ¡Gracias por tu participación!";
        }
    }

?>

<!DOCTYPE HTML>

<html lang="es">
<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <title>MotoGP-Ayuda</title>
    <meta name ="author" content ="Nahiara Sánchez García" /> 
    <meta name ="description" content ="Documento con la información de ayuda del proyecto MotoGP-Desktop." /> 
    <meta name ="keywords" content ="motogp, moto, ayuda, información" /> 
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" /> 

     <!-- Enlaces -->
     <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
     <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />

     <!--FavIcon-->
    <link rel="icon" href="../multimedia/favicon.ico" />
     
</head>

<body>

    <header>

        <!-- Datos con el contenidos que aparece en el navegador -->
        <h1><a href="index.html" title="Página Inicio">MotoGP-Desktop</a></h1>

    </header>

    <p>Estas en: <a href="index.html" title="Página Inicio">Inicio</a> | <strong>Formulario Pruebas Usabilidad</strong></p>

    <main>
        <h2>Formulario de Pruebas de Usabilidad</h2>

        <?php echo $mensaje_estado; ?>

        <p>Por favor, completa el siguiente formulario para ayudarnos a mejorar la aplicación:</p>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            
            <h3>Pregunta 1:</h3>
            <label for="p1">¿Cuál es la primera noticia que aparece en la sección de noticias?</label>
            <input type="text" name="p1" required />

            <h3>Pregunta 2: Piloto (Identidad)</h3>
            <label for="p2">¿De qué piloto trata la aplicación?</label>
            <input type="text" name="p2" required />
            
            <h3>Pregunta 3: Piloto (Subcampeonatos)</h3>
            <label for="p3">¿Cuántas veces ha sido subcampeón del mundo?</label>
            <input type="number" name="p3" min="0" required />

            <h3>Pregunta 4: Piloto (Nacimiento)</h3>
            <label for="p4">¿Cuándo nació el piloto?</label>
            <input type="text" name="p4" placeholder="Ej: DD/MM/AAAA" required />

            <h3>Pregunta 5: Piloto (Dorsal)</h3>
            <label for="p5">¿Cuál es el número de su dorsal?</label>
            <input type="number" name="p5" min="1" required />

            <h3>Pregunta 6: Circuito (Población)</h3>
            <label for="p6">¿Cuál es la población de la ciudad de Assen?</label>
            <input type="text" name="p6" required />

            <h3>Pregunta 7: Juego (Tamaño Tablero)</h3>
            <label for="p7">¿De qué tamaño es el tablero del juego de cartas de memoria?</label>
            <input type="text" name="p7" placeholder="Ej: 4x4 o 6x6" required />

            <h3>Pregunta 8: Carrera (Ganador 2025 - TT Circuit Assen)</h3>
            <label for="p8">¿Quién fue el ganador en la carrera llevada a cabo en el circuito TT Circuit Assen en 2025?</label>
            <input type="text" name="p8" required />

            <h3>Pregunta 9: Carrera (Hora - TT Circuit Assen)</h3>
            <label for="p9">¿A qué hora fue la carrera en ese circuito?</label>
            <input type="text" name="p9" placeholder="Ej: HH:MM" required />

            <h3>Pregunta 10: Circuito (País)</h3>
            <label for="p10">¿En qué país se encuentra el circuito?</label>
            <input type="text" name="p10" required />

            <button type="submit">Finalizar y Enviar Respuestas</button>

        </form>

    </main>
    
    
</body>
</html>