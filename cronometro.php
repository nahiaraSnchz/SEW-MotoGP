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

        public function resetear() {
            $this->tiempo = 0;
            $tiempoTotal = 0;
            $this->inicio = null;
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
        if (isset($_POST['resetear'])) {
            $miCronometro->resetear();
            $mensaje = "Cronómetro reiniciado.";
        }

        // guardar objeto completo en la sesión
        $_SESSION['miCronometro'] = $miCronometro;

        if ($mensaje) {
            echo "<p>" . $mensaje . "</p>";
        }
        
    }

?>