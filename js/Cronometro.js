class Cronometro {

    #tiempo;
    #inicio = null;
    #corriendo = null;
    #elemento;

    constructor(elemento) {
        this.#elemento = elemento;
        this.#tiempo = 0;
    }

    arrancar() {
        if (this.#corriendo) {
            return; // ya está corriendo
        }
        try {
            // this.#inicio = Temporal.Now.instant();
            this.#inicio = Temporal.Now.instant().subtract({ milliseconds: this.#tiempo });
        }
        catch (error) {
            // this.#inicio = new Date();
            this.#inicio = new Date() - this.#tiempo;
        }
        this.#corriendo = setInterval(this.actualizar.bind(this), 100);
        this.mostrar();
        console.log("Cronómetro arrancado");
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
        if (this.#elemento) this.#elemento.textContent = texto;
    }

    parar() {
        if (this.#corriendo) {
            clearInterval(this.#corriendo);
            this.#corriendo = null;
        }
    }

    reiniciar() {
        clearInterval(this.#corriendo);
        this.#tiempo = 0;
        this.mostrar();
    }

}

document.addEventListener('DOMContentLoaded', () => {
    
    // Seleccionamos los botones
    const pantalla = document.querySelector("main p");
    const botones = document.querySelectorAll("main button");
    const cronometro = new Cronometro(pantalla);

    botones[0].addEventListener('click', () => cronometro.arrancar());
    botones[1].addEventListener('click', () => cronometro.parar());
    botones[2].addEventListener('click', () => cronometro.reiniciar());
    
    console.log("Listeners asignados correctamente.");
});