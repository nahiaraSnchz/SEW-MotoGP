class Carrusel {

    #busqueda;
    #actual;
    #maximo;
    #fotos = [];
    #img;

    constructor(busqueda) {
        this.#busqueda = busqueda;
        this.#actual = 0;
        this.#maximo = 4;
    }

    getFotografias() {
        const flickrAPI = "https://api.flickr.com/services/feeds/photos_public.gne?jsoncallback=?";

        return $.getJSON( flickrAPI,{
            tags: this.#busqueda,
            tagmode: "any",
            format: "json"

            }).then((data) => {
                return data.items;

            }).catch((error) => {
                console.error("Error al obtener las fotografías de Flickr: ", error);
                return [];
        });
    }

    procesarJSONFotografias(jsonFotos) {
        const numFotos = Math.min(5, jsonFotos.length);

        for (let i = 0; i < numFotos; i++) {
            const foto = jsonFotos[i];
            this.#fotos.push({
                titulo: foto.title,
                url: foto.media.m.replace('_m.', '_z.')
            });
        }
        return this.#fotos;
    }

    mostrarFotografias() {
        if(!this.#fotos || this.#fotos.length === 0) {
            console.warn("No hay fotografías para mostrar.");
            return;
        }

        const $article = $("<article></article>");

        const $h3 = $(`<h3>Imágenes del circuito de ${this.#busqueda}</h3>`);
        $article.append($h3);

        const primeraFoto = this.#fotos[this.#actual];
        this.#img = $("<img>").attr("src", primeraFoto.url).attr("alt", primeraFoto.titulo);
        $article.append(this.#img);

        $("body").append($article);

        // Cambio automático cada 3 segundos
        setInterval(this.cambiarFotografia.bind(this), 3000);
    }

    cambiarFotografia() {
        if(!this.#fotos || this.#fotos.length === 0) return;

        this.#actual = (this.#actual + 1) % this.#maximo;
        const nuevaFoto = this.#fotos[this.#actual];
        this.#img.attr("src", nuevaFoto.url).attr("alt", nuevaFoto.titulo);
    }
}

