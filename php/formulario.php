<?php
    
    // Clase cronómetro
    require_once '../cronometro.php';
    require_once 'Configuracion.php';

    // Iniciar sesion
    session_start();

    // CARGAR PREGUNTAS DEL TXT
    $archivo = 'preguntas.txt'; // Ajusta la ruta si es necesario
    $preguntas = [];
    if (file_exists($archivo)) {
        $lineas = file($archivo, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lineas as $linea) {
            // Ignoramos la línea de título "PREGUNTAS:"
            if (stripos($linea, 'PREGUNTAS:') !== false) continue;
            
            // Limpiamos el número inicial (ej: "1. ") usando expresión regular
            $pregunta_limpia = preg_replace('/^\d+\.\s*/', '', $linea);
            if (!empty($pregunta_limpia)) {
                $preguntas[] = $pregunta_limpia;
            }
        }
    }
    $total_preguntas = count($preguntas);

    // Gestion cronometro
    if (isset($_SESSION['pruebaCronometro'])) {
        $miCronometro = $_SESSION['pruebaCronometro'];
    }
    else {
        $miCronometro = new Cronometro();
    }

    $mensaje_estado = "";
    $formulario_visible = false;
    $datos_usuario_visible = false;
    $observador_visible = false;

    // 4. LÓGICA DE NAVEGACIÓN (Fases: inicio, test, datos, observador)
    if (!isset($_SESSION['fase'])) {
        $_SESSION['fase'] = 'inicio';
    }

    // recuperar datos temporales de la sesion si existen
    $datos_temporales = isset($_SESSION['datos_temporales']) ? $_SESSION['datos_temporales'] : [];
    $estado_final = isset($_SESSION['estado_final_prueba']) ? $_SESSION['estado_final_prueba'] : [];

    // LOGICA BOTON INICIAR PRUEBA
    if (isset($_POST['iniciar'])) {
        $miCronometro->resetear();
        $miCronometro->arrancar();
        $mensaje_estado = "Prueba iniciada. Por favor, completa el formulario a continuación.";
        $formulario_visible = true;
    }
    // MANEJAR ENVIO DATOS PERSONALES Y VALORACION
    else if (isset($_POST['guardar_datos_completos'])) {
        
        // recoger datos del formulario
        $datos_temporales = [
            'profesion' => $_POST['profesion'],
            'edad' => (int)$_POST['edad'],
            'genero' => $_POST['genero'],
            'periciaInformatica' => (int)$_POST['periciaInformatica'],
            'dispositivoId' => (int)$_POST['dispositivoId'],
            'comentario' => $_POST['comentario'],
            'propuestaMejora' => $_POST['propuestaMejora'],
            'valoracion' => (int)$_POST['valoracion']
        ];

        // guardar datos en sesion
        $_SESSION['datos_temporales'] = $datos_temporales;

        $datos_usuario_visible = false;
        $observador_visible = true;
        $mensaje_estado = "Datos personales y valoración guardados.";

    }
    // MANEJAR ENVIO DE OBSERVACIONES (OBSERVADOR)
    else if (isset($_POST['finalizar_observacion'])) {
        $db_manager = new Configuracion();
        try {
            $db_manager->guardarResultadoCompleto(
                $_SESSION['datos_temporales'],
                $_SESSION['datos_temporales']['dispositivoId'],
                $_SESSION['estado_final_prueba']['tiempo'],
                $_SESSION['estado_final_prueba']['completada'],
                $_POST['observacion_texto']
            );
            $mensaje_estado = "Datos guardados con éxito.";
        } catch (\Exception $e) {
            $mensaje_estado = "Error al guardar: " . $e->getMessage();
        }

        // LIMPIEZA TOTAL PARA REINICIAR EL ESTADO
        $miCronometro->resetear();
        session_unset(); 
        session_destroy(); 
        
        $formulario_visible = false; 
        $datos_usuario_visible = false;
        $observador_visible = false;
    }

    // Procesar formulario
    else if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $miCronometro->parar();
        
        $respuestas_vacias = 0;
        $total_preguntas = 10;

        for ($i = 1; $i <= $total_preguntas; $i++) {
            if (empty($_POST["p$i"])) {
                $respuestas_vacias++;
            }
        }

        $tarea_completada = ($respuestas_vacias === 0);
        $tiempo_final = $miCronometro->getTiempo();
        $_SESSION['estado_final_prueba'] = ['tiempo' => $tiempo_final, 'completada' => $tarea_completada];

        $formulario_visible = false;
        $observador_visible = false;
        $datos_usuario_visible = true;

        if ($tarea_completada) {
            $mensaje_estado = "Formulario enviado correctamente. ¡Gracias por tu participación!";
        } else {
            $mensaje_estado = "Formulario enviado, pero no se completaron todas las preguntas.";
        }
    }
    else if ($miCronometro->getInicio() !== null && !$observador_visible) {
        $formulario_visible = true;
        $mensaje_estado = "Prueba en curso. Por favor, completa el formulario a continuación.";
    }
    else if ($observador_visible) {
        $mensaje_estado = "Por favor, Observador, introduzca sus comentarios";
    }

    // Guardar el estado del cronómetro en la sesión
    $_SESSION['pruebaCronometro'] = $miCronometro;

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
        <h1><a href="../index.html" title="Página Inicio">MotoGP-Desktop</a></h1>

    </header>

    <p>Estas en: <a href="../index.html" title="Página Inicio">Inicio</a> | Formulario Pruebas Usabilidad</p>

    <main>
        <h2>Formulario de Pruebas de Usabilidad</h2>

        <?php echo $mensaje_estado; ?>

        <?php if (!$formulario_visible && !$datos_usuario_visible && !$observador_visible): ?>
            <p>Presiona Iniciar Prueba para comenzar.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <button type="submit" name="iniciar">Iniciar Prueba</button>
            </form>
        <?php endif; ?>

        <?php if ($formulario_visible): ?>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            
                <?php foreach ($preguntas as $indice => $texto_pregunta): ?>
                    <?php $numero = $indice + 1; ?>
                    <section>
                        <h3>Pregunta <?php echo $numero; ?>:</h3>
                        <label>
                            <?php echo htmlspecialchars($texto_pregunta); ?>
                            <input type="text" name="p<?php echo $numero; ?>" />
                        </label>
                    </section>
                <?php endforeach; ?>

                <button type="submit">Finalizar y Enviar Respuestas</button>

            </form>
        <?php endif; ?>

        <?php if ($datos_usuario_visible): ?>
            <h3>Datos Personales y Valoración</h3>
            <p>Por favor, introduzca sus datos personales y valoración de la prueba.</p>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <h4>Datos Personales</h4>
                <label>Profesión:</label><input type="text" name="profesion" required />
                <label>Edad:</label><input type="number" name="edad" min="1" required />
                <label>Género: 
                    <select name="genero" required>
                        <option value="" disabled selected>Seleccione una opción...</option>
                        <option value="Masculino">Masculino</option>
                        <option value="Femenino">Femenino</option>
                        <option value="Otro">Otro</option>
                    </select>
                </label>
                <label>Pericia Informática (1-10):</label><input type="number" name="periciaInformatica" min="1" max="10" required />

                <h4>Valoración de la Prueba</h4>
                <label>Dispositivo utilizado:</label>
                    <select name="dispositivoId" required>
                        <option value="" disabled selected>Seleccione un dispositivo...</option>
                        <option value="1">Ordenador</option>
                        <option value="2">Tablet</option>
                        <option value="3">Teléfono</option>
                    </select>
                <label>Comentarios del Usuario:</label><textarea name="comentario" rows="3" required></textarea>
                <label>Propuestas de Mejora:</label><textarea name="propuestaMejora" rows="3" required></textarea>
                <label>Valoración (0-10):</label><input type="number" name="valoracion" min="0" max="10" required />

                <button type="submit" name="guardar_datos_completos">Guardar Datos y Continuar a Observación</button>

            </form>
        <?php endif; ?>

        <?php if ($observador_visible): ?>
            <h3>Fase de observación</h3>
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                <label>
                    Anotaciones del observador:
                    <textarea name="observacion_texto" rows="5" cols="50" placeholder="Escribe tus observaciones aquí..."></textarea>
                </label>

                <button type="submit" name="finalizar_observacion">Finalizar Observación</button>
            </form>
        <?php endif; ?>

    </main>
    
    
</body>
</html>