class Noticias {

    #busqueda;
    #url;
    #apiKey

    constructor(busqueda) {
        this.#busqueda = busqueda;
        this.#url = "https://api.thenewsapi.com/v1/news/all";
        this.#apiKey = "IO83AMZM1KVoyy9qyyOfB81JBmCayHMr91sCIuSk";
    }

    // Método público para obtener la búsqueda
    getBusqueda() {
        return this.#busqueda;
    }

    // Método público para obtener la URL base
    getUrl() {
        return this.#url;
    }

    // Método para realizar la búsqueda de noticias con fetch()
    buscar() {
        // URL
        const fullUrl = `${this.#url}?api_token=${this.#apiKey}&search=${encodeURIComponent(this.#busqueda)}&language=es`; 
        return fetch(fullUrl)
            .then(response => response.json())
            .then(data => {
                const noticias = this.procesarInformacion(data);
                this.mostrarNoticias(noticias);
            })
            .catch(error => console.error("Error al obtener noticias:", error));
    }


    procesarInformacion(json) {
        if (!json.data || !Array.isArray(json.data)) {
            console.error("Formato de JSON inesperado", json);
            return [];
        }

        // Información relevante de cada noticia
        const noticiasProcesadas = json.data.map(noticia => ({
            titulo: noticia.title,
            descripcion: noticia.description,
            fuente: noticia.source,
            enlace: noticia.url,
            fecha: noticia.published_at
        }));

        return noticiasProcesadas;
    }

    mostrarNoticias(noticias) {
        // Seleccionamos la sección donde vamos a poner las noticias
        let contenedor = $("section").last(); 
        contenedor.empty(); // Limpiamos contenido previo

        contenedor.append($("<h2>").text(`Noticias de MotoGP`));

        noticias.forEach(noticia => {
            // Creamos un div por cada noticia
            let noticiaDiv = $("<section>").addClass("noticia");

            noticiaDiv.append($("<h3>").text(noticia.titulo));
            noticiaDiv.append($("<p>").text(noticia.descripcion));
            noticiaDiv.append($("<p>").html(`Fuente: ${noticia.fuente} | <a href="${noticia.enlace}" target="_blank">Leer más</a>`));

            contenedor.append(noticiaDiv);
        });
    }

}