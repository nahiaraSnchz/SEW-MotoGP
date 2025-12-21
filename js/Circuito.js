class Circuito {

    #soportaFile; // Indica si la API File está soportada
    #contenidoArchivo;
    #inputSection;

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
                $("<p>").text("ATENCIÓN: Su navegador no soporta la API File, algunas funciones no estarán disponibles."));
        }
        return this.#soportaFile;
    }

    getSoportaFile() {
        return this.#soportaFile;
    }

    // Metodo para leer un archivo HTML mediante un input
    leerArchivoHTML() {
        if (!this.#soportaFile) return;

        // Crear un section para el input y el título
        this.#inputSection = $('<section></section>');

        // Crear el titulo h3
        const $titulo = $('<h3>Introduzca archivo HTML:</h3>');
        this.#inputSection.append($titulo);
        // Crear el input
        // Se le asocia una etiqueta para mejor accesibilidad
        const $label = $('<label>Seleccione el archivo HTML del circuito:</label>');
        const $input = $('<input type="file" accept=".html">');

        $label.append($input);

        $input.on('change', (event) => {
            const archivo = event.target.files[0];
            if (!archivo) return;

            const lector = new FileReader();

            lector.onload = (e) => {
                this.#contenidoArchivo = e.target.result;
                this.loadInfo();

                // se indica que ya se cargo
                this.#inputSection.append($('<p>').text('Archivo cargado: ' + archivo.name));
                $input.remove();
            };

            lector.onerror = (e) => {
                console.error("Error al leer el archivo:", e);
            };

            lector.readAsText(archivo);
        });

        this.#inputSection.append($label);

        $("main h2").first().after(this.#inputSection);
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
    
        const $section = $('<section></section>');
    
        $(mainContent).children('section').each(function() {
            $section.append($(this).clone());
        });
        
        this.#inputSection.after($section);
    }

}

class CargadorSVG {

    #archivo;
    #soportaFile;
    #contenedor;

    constructor() {
        this.#archivo = null;
        this.comprobarApiFile();

    }

    comprobarApiFile() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.#soportaFile = true;
        } else {
            this.#soportaFile = false;
            $("body").prepend(
                $("<p>").text("ATENCIÓN: Su navegador no soporta la API File, algunas funciones no estarán disponibles.")
                        .css({ color: "red", "font-weight": "bold" })
            );
        }
        return this.#soportaFile;
    }

    /*
        Crear input y leer archivo SVG
    */
    leerArchivoSVG() {
        if (!this.#soportaFile) return;

        // Crear un section para el input para que quede separado
        const $inputSection = $('<section></section>');

        const $titulo = $('<h3>Introduzca archivo SVG:</h3>');
        $inputSection.append($titulo);

        // Se le asocia una etiqueta para mejor accesibilidad
        const $label = $('<label>Seleccione el archivo SVG del circuito:</label>');
        const $input = $('<input type="file" accept=".svg">');

        $label.append($input);

        $input.on("change", (event) => {
            const archivo = event.target.files[0];
            if (!archivo) return;

            this.#archivo = archivo;

            if (!archivo.name.toLowerCase().endsWith(".svg")) {
                alert("Debe seleccionar un archivo .svg");
                return;
            }

            const lector = new FileReader();

            lector.onload = (e) => {

                // Crear un contenedor para mostrar el SVG
                this.#contenedor = document.createElement("section");
                
                const contenido = e.target.result;
                this.insertarSVG(contenido);

                // se indica que ya se cargo
                $inputSection.after(this.#contenedor);
                $inputSection.append($('<p>').text('Archivo cargado: ' + archivo.name));
                $input.remove();
            };

            lector.onerror = (e) => {
                console.error("Error al leer el archivo:", e);
            };

            lector.readAsText(archivo);
        });

        $inputSection.append($label);
        $("main").append($inputSection); // Se añade el section al body para que quede separado
    }
    

    /*
        Insertar el SVG leído en el contenedor
    */
    insertarSVG(textoSVG) {
        this.#contenedor.innerHTML = "";

        const parser = new DOMParser();
        const docSVG = parser.parseFromString(textoSVG, "image/svg+xml");
        const svg = docSVG.documentElement;

        this.#contenedor.appendChild(svg);
    }
}

class CargadorKML {
    #archivo;
    #contenido;
    #soportaFile;
    #contenedor;
    #mapa;
    #puntos;

    constructor() {
        this.#archivo = null;
        this.#contenido = null;
        this.#puntos = [];
        this.comprobarApiFile();
    }

    comprobarApiFile() {
        if (window.File && window.FileReader && window.FileList && window.Blob) {
            this.#soportaFile = true;
        } else {
            this.#soportaFile = false;
            $("body").prepend(
                $("<p>").text("ATENCIÓN: Su navegador no soporta la API File.")
            );
        }
        return this.#soportaFile;
    }

    leerArchivoKML() {
        if (!this.#soportaFile) return;

        const $inputSection = $('<section></section>');
        const $titulo = $('<h3>Introduzca archivo KML:</h3>');
        $inputSection.append($titulo);

        // Se le asocia una etiqueta para mejor accesibilidad
        const $label = $('<label>Seleccione el archivo KML del circuito:</label>');
        const $input = $('<input type="file" accept=".kml">');

        $label.append($input);
        $inputSection.append($label);
        
        // Crear contenedor para el mapa
        this.#contenedor = document.createElement("div");
        $inputSection.append(this.#contenedor);

        $("main").append($inputSection);

        $input.on("change", async (event) => {
            const archivo = event.target.files[0];
            if (!archivo) return;

            this.#archivo = archivo;

            if (!archivo.name.toLowerCase().endsWith(".kml")) {
                alert("Debe seleccionar un archivo .kml");
                return;
            }

            const lector = new FileReader();
            lector.onload = async (e) => {
                this.#contenido = e.target.result;
                $titulo.text(`Archivo cargado: ${archivo.name}`);
                $input.remove();
                await this.procesarKML();
                await this.inicializarMapa();
            };
            lector.readAsText(archivo);
        });
    }

    async procesarKML() {
        if (!this.#contenido) return;

        const parser = new DOMParser();
        const xmlDoc = parser.parseFromString(this.#contenido, "text/xml");

        const lineStringNode = xmlDoc.querySelector("LineString > coordinates");
        if (!lineStringNode) return;

        this.#puntos = lineStringNode.textContent.trim().split(/\s+/).map(coord => {
            const [lng, lat] = coord.split(",");
            return { lat: parseFloat(lat), lng: parseFloat(lng) };
        });
    }

    async inicializarMapa() {
        if (!this.#puntos.length) return;

        const MI_MAP_ID = "ffc769ded9d9bada65851b32"; 

        const bounds = new google.maps.LatLngBounds(); 
        this.#puntos.forEach(p => bounds.extend(p)); 

        // 2. Creamos el mapa con el ID de demo
        this.#mapa = new google.maps.Map(this.#contenedor, {
            mapId: MI_MAP_ID,
            center: this.#puntos[0],
            zoom: 12
        });

        this.#mapa.fitBounds(bounds);

        // 3. Dibujamos la línea de la ruta
        const trazo = new google.maps.Polyline({
            path: this.#puntos,
            geodesic: true,
            strokeColor: "#FF0000",
            strokeOpacity: 1.0,
            strokeWeight: 2
        });
        trazo.setMap(this.#mapa);

        // 4. Cargamos la librería de marcadores y los añadimos (Forma moderna)
        const { AdvancedMarkerElement } = await google.maps.importLibrary("marker");

        this.#puntos.forEach(p => {
            new AdvancedMarkerElement({
                map: this.#mapa,
                position: p,
                title: "Punto de ruta"
            });
        });
    }
}

$(function() {
    try {
        const circ = new Circuito();
        circ.leerArchivoHTML();

        const svg = new CargadorSVG();
        svg.leerArchivoSVG();

        const kml = new CargadorKML();
        kml.leerArchivoKML();
        
    } catch (e) {
        console.error("Error en la inicialización:", e);
    }
});