class Ciudad {

    constructor(nombreCiudad, pais, gentilicio) {
        this.nombreCiudad = nombreCiudad;
        this.pais = pais;
        this.gentilicio = gentilicio;
    }

    rellenarDatosSecundarios(poblacion, latitud, longitud, altitud) {
        this.setPoblacion(poblacion);
        this.setLongitud(longitud);
        this.setLatitud(latitud);
        this.setAltitud(altitud);
    }

    setPoblacion(poblacion) {
        this.poblacion = poblacion;
    }

    setLongitud(longitud) {
        this.longitud = longitud;
    }

    setLatitud(latitud) {
        this.latitud = latitud;
    }

    setAltitud(altitud) {
        this.altitud = altitud;
    }

    getNombreCiudad() {
        return this.nombreCiudad + "";
    }

    getPais() {
        return this.pais + "";
    }

    getCoordenadas() {
        return "<p>Latitud: " + this.latitud + " | Longitud: " + this.longitud + " | Altitud: " + this.altitud + "</p>";
    }

    getInformacion() {
        var info = "<ul>"
        + "<li>Ciudad: " + this.nombreCiudad + ".</li>"
        + "<li>Gentilicio: " + this.gentilicio + "</li>"
        + "<li>Población " + this.poblacion + " habitantes.</li>"
        + "</ul>";
        return info;
    }

    mostrarCoordenadas() {
        document.write(this.getCoordenadas());
    }
}