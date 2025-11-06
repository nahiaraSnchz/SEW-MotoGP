    class Memoria {

        #tablero_bloqueado;
        #primera_carta;
        #segunda_carta;
        #cronometro;


        constructor() {
            this.#tablero_bloqueado = true;
            this.#primera_carta = null;
            this.#segunda_carta = null;
            this.#cronometro = new Cronometro();
            this.barajarCartas();
            this.#tablero_bloqueado = false;
            this.#cronometro.arrancar();
        }

        voltearCarta(carta) {
            if (this.#tablero_bloqueado) return;
            if (carta.dataset.estado === "revelada") return;
            if (carta.dataset.estado === "volteada") return;

            carta.dataset.estado = "volteada";

            if (!this.#primera_carta) {
                this.#primera_carta = carta;
                return;
            }
            this.#segunda_carta = carta;
            this.#tablero_bloqueado = true;
            this.comprobarPareja();
        }

        barajarCartas() {
            var contenedor = document.querySelector("main");
            var cartas = Array.from(contenedor.querySelectorAll("article"));
            for(let i = cartas.length - 1; i > 0; i--) {
                let j = Math.floor(Math.random() * (i + 1));
                contenedor.appendChild(cartas[j]);
            }
        }

        reiniciarAtributos() {
            this.#tablero_bloqueado = false;
            this.#primera_carta = null;
            this.#segunda_carta = null;
        }

        deshabilitarCartas() {
            if (this.#primera_carta && this.#segunda_carta) {
                this.#primera_carta.dataset.estado = "revelada";
                this.#segunda_carta.dataset.estado = "revelada";
            }
            this.comprobarJuego();
            this.reiniciarAtributos();
        }

        comprobarJuego() {
            var cartas = document.querySelectorAll("main article");
            var todas_reveladas = Array.from(cartas).every(carta => carta.dataset.estado === "revelada");
            if (todas_reveladas) {
                this.#cronometro.parar();
                alert("¡Felicidades! Has conseguido finalizar el juego de memoria.");
            }
        }

        cubrirCartas() {
            this.#tablero_bloqueado = true;
            // establecer retardo 
            setTimeout(() => {
                if (this.#primera_carta) this.#primera_carta.dataset.estado = "";
                if (this.#segunda_carta) this.#segunda_carta.dataset.estado = "";

                this.reiniciarAtributos();

            }, 1500);
        }

        comprobarPareja() {
            if (!this.#primera_carta || !this.#segunda_carta) return;
            
            var img1 = this.#primera_carta.querySelector("img").getAttribute("src");
            var img2 = this.#segunda_carta.querySelector("img").getAttribute("src");

            img1 === img2 ? this.deshabilitarCartas() : this.cubrirCartas();
        }
    }


    document.addEventListener("DOMContentLoaded", () => {
        const memoria = new Memoria();
        const cartas = document.querySelectorAll("main article");

        cartas.forEach(carta => {
            carta.addEventListener("click", (evento) => {
                memoria.voltearCarta(evento.currentTarget);
            });
        });
    });