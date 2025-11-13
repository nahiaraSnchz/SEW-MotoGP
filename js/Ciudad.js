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

    getLatitud() {
        return this.#latitud;
    }
    
    getLongitud() {
        return this.#longitud;
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

    getMeteorologia(fecha) {
        const url = `https://archive-api.open-meteo.com/v1/archive?latitude=${this.getLatitud()}&longitude=${this.getLongitud()}&start_date=${fecha}&end_date=${fecha}&hourly=temperature_2m,apparent_temperature,rain,relativehumidity_2m,windspeed_10m,winddirection_10m&daily=sunrise,sunset&timezone=Europe/Madrid`;

        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            success: (datosJSON) => {
                const info = this.procesarJSONCarrera(datosJSON);
                this.mostrarEnHTML(info);
            },
            error: (err) => {
                console.error("Error al obtener los datos meteorológicos:", err);
            }
        });
    }

    procesarJSONCarrera(datosJSON) {
        let info = {
            sunrise: datosJSON.daily.sunrise[0],
            sunset: datosJSON.daily.sunset[0],
            hourly: []
        };

        for (let i = 0; i < datosJSON.hourly.time.length; i++) {
            info.hourly.push({
                hora: datosJSON.hourly.time[i],
                temperatura: datosJSON.hourly.temperature_2m[i],
                sensacionTermica: datosJSON.hourly.apparent_temperature[i],
                lluvia: datosJSON.hourly.rain[i],
                humedad: datosJSON.hourly.relativehumidity_2m[i],
                vientoVelocidad: datosJSON.hourly.windspeed_10m[i],
                vientoDireccion: datosJSON.hourly.winddirection_10m[i]
            });
        }

        return info;
    }

    mostrarEnHTML(info) {
        // Seleccionamos el segundo <section> sin usar id ni class
        let contenedor = $("section").eq(1);
        contenedor.empty(); // Limpiamos contenido previo

        contenedor.append($("<h2>").text(`Datos meteorológicos para ${this.getNombreCiudad()}, ${this.getPais()} el día 5 de junio de 2025`));

        // Salida y puesta del sol
        contenedor.append($("<h3>").text("Salida y puesta de sol"));

        let listaMeteorologia = $("<ul>");

        // Añadimos los elementos como <li>
        listaMeteorologia.append($("<li>").text("Salida del sol: " + info.sunrise));
        listaMeteorologia.append($("<li>").text("Puesta del sol: " + info.sunset)); 

        contenedor.append(listaMeteorologia);

        contenedor.append($("<h3>").text("Datos por horas"));
        // Datos por hora
        info.hourly.forEach(hora => {
            let p = $("<p>").text(
                `${hora.hora} → Temp: ${hora.temperatura}°C, Sensación: ${hora.sensacionTermica}°C, Lluvia: ${hora.lluvia}mm, Humedad: ${hora.humedad}%, Viento: ${hora.vientoVelocidad} m/s, Dir: ${hora.vientoDireccion}°`
            );
            contenedor.append(p);
        });
    }



}