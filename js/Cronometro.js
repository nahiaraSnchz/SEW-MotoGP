class Cronometro {

    #tiempo;
    #inicio = null;
    #corriendo = null;

    constructor() {
        this.#tiempo = 0;
    }

    arrancar() {
        try {
            this.#inicio = Temporal.Now.instant();
        }
        catch (error) {
            this.#inicio = new Date();
        }
        this.#corriendo = setInterval(this.actualizar.bind(this), 100);
        this.mostrar();
    }

    actualizar() {
        try {
            let ahora = Temporal.Now.instant();
            let diferencia = ahora.epochMilliseconds - this.#inicio.epochMilliseconds;
            this.#tiempo = diferencia;
        }
        catch (error) {
            let ahora = new Date();
            let diferencia = ahora - this.#inicio;
            this.#tiempo = diferencia;
        }
        this.mostrar();
    }

    mostrar() {
        let totalMillis = this.#tiempo;
        let minutos = parseInt(totalMillis / 60000);
        let segundos = parseInt((totalMillis % 60000) / 1000);
        let decimas = parseInt((totalMillis % 1000) / 100);

        let texto = String(minutos).padStart(2, '0') + ":" +
                    String(segundos).padStart(2, '0') + "." +
                    String(decimas);
        
        let parrafo = document.querySelector("main p");
        if (parrafo) {
            parrafo.textContent = texto;
        }
    }

    parar() {
        clearInterval(this.#corriendo)
    }

    reiniciar() {
        clearInterval(this.#corriendo);
        this.#tiempo = 0;
        this.mostrar();
    }

}