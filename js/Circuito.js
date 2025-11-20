class Circuito {

    #soportaFile; // Indica si la API File está soportada
    #contenidoArchivo;

    constructor() {
        this.comprobarApiFile();
    }

    // Método para comprobar si el navegador soporta la API File
    comprobarApiFile() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.#soportaFile = true;
        } else {
            this.#soportaFile = false;
            $("body").prepend(
                $("<p>").text("ATENCIÓN: Su navegador no soporta la API File, algunas funciones no estarán disponibles.")
                        .css({color: "red", "font-weight": "bold"})
            );
        }
        return this.#soportaFile;
    }

    getSoportaFile() {
        return this.#soportaFile;
    }

    // Método para leer un archivo HTML mediante un input
    leerArchivoHTML() {
        if (!this.#soportaFile) return;

        const $input = $('<input type="file" accept=".html">');

        // Evento cuando se selecciona un archivo
        $input.on('change', (event) => {
            const archivo = event.target.files[0];
            if (!archivo) return;

            const lector = new FileReader();
            
            lector.onload = (e) => {
                this.#contenidoArchivo = e.target.result;
                this.loadInfo();
            };

            lector.onerror = (e) => {
                console.error("Error al leer el archivo:", e);
            };

            lector.readAsText(archivo);
        });

        // Añadir el input al body o a una sección de la página
        $("body").append($input);
    }


    getContenidoArchivo() {
        return this.#contenidoArchivo;
    }

    loadInfo() {
        if (!this.#contenidoArchivo) return;
    
        const parser = new DOMParser();
        const doc = parser.parseFromString(this.#contenidoArchivo, 'text/html');
    
        const mainContent = doc.querySelector('main');
        if (!mainContent) return;
    
        const $section = $('section').first();
    
        $(mainContent).children('section').each(function() {
            $section.append($(this).clone());
        });
    

    
    }

}