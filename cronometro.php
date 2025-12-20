<?php
    class Cronometro {
        private $tiempo;
        private $inicio;

        public function __construct() {
            $this->tiempo = 0;
            $this->inicio = null;
        }

        public function arrancar() {
            if ($this->inicio === null) {
                $this->inicio = microtime(true);
                $this->tiempo = 0;
            }
        }

        public function parar() {
            if ($this->inicio !== null) {
                $momentoFinal = microtime(true);
                $this->tiempo = $momentoFinal - $this->inicio;
                $this->inicio = null;
            }
        }

        public function mostrar() {
            $tiempoTotal = $this->tiempo;

            // si el cronometro está activo
            if ($this->inicio !== null) {
                $tiempoTotal = (microtime(true) - $this->inicio);
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


    

?>