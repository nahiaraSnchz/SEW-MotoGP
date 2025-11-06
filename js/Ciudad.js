class Ciudad {

    #nombreCiudad;
    #pais;
    #gentilicio;
    #poblacion;
    #latitud;
    #longitud;
    #altitud;

    constructor(nombreCiudad, pais, gentilicio) {
        this.#nombreCiudad = nombreCiudad;
        this.#pais = pais;
        this.#gentilicio = gentilicio;
    }

    rellenarDatosSecundarios(poblacion, latitud, longitud, altitud) {
        this.setPoblacion(poblacion);
        this.setLongitud(longitud);
        this.setLatitud(latitud);
        this.setAltitud(altitud);
    }

    setPoblacion(poblacion) {
        this.#poblacion = poblacion;
    }

    setLongitud(longitud) {
        this.#longitud = longitud;
    }

    setLatitud(latitud) {
        this.#latitud = latitud;
    }

    setAltitud(altitud) {
        this.#altitud = altitud;
    }

    getNombreCiudad() {
        return this.#nombreCiudad;
    }

    getPais() {
        return this.#pais;
    }

    getCoordenadas() {
        return "Latitud: " + this.#latitud + " | Longitud: " + this.#longitud + " | Altitud: " + this.#altitud;
    }

    mostrarInformacionBasica() {
        var p = document.createElement("p");
        p.textContent = "El circuito está cerca de la " + this.getNombreCiudad() + ", en " + this.getPais();
        document.body.appendChild(p);
    }

    getInformacion() {
        var lista = document.createElement("ul");
        var infoCiudad = document.createElement("li");
        infoCiudad.textContent = "Ciudad: " + this.#nombreCiudad;
        var infoGentilicio = document.createElement("li");
        infoGentilicio.textContent = "Gentilicio: " + this.#gentilicio;
        var infoPoblacion = document.createElement("li");
        infoPoblacion.textContent = "Población: " + this.#poblacion + " habitantes.";
        lista.appendChild(infoCiudad);
        lista.appendChild(infoGentilicio);
        lista.appendChild(infoPoblacion);
        document.body.appendChild(lista);
    }

    mostrarCoordenadas() {
        var h4 = document.createElement("h4");
        h4.textContent = "Coordenadas de la ciudad:";
        document.body.appendChild(h4);
        var p = document.createElement("p");
        p.textContent = this.getCoordenadas();
        document.body.appendChild(p);
    }
}