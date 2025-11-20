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

    getMeteorologiaCarrera(fecha) {
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

        contenedor.append($("<h2>").text(`Datos meteorológicos para ${this.getNombreCiudad()}, ${this.getPais()} el día 29 de junio de 2025`));

        // Salida y puesta del sol
        contenedor.append($("<h3>").text("Salida y puesta de sol"));

        let listaMeteorologia = $("<ul>");

        // Añadimos los elementos como <li>
        listaMeteorologia.append($("<li>").text("Salida del sol: " + info.sunrise));
        listaMeteorologia.append($("<li>").text("Puesta del sol: " + info.sunset)); 

        contenedor.append(listaMeteorologia);

        contenedor.append($("<h3>").text("Datos por horas"));
        let listaHoras = $("<ul>");
        // Datos por hora
        info.hourly.forEach(hora => {
            let li = $("<li>").text(
                `${hora.hora} → Temp: ${hora.temperatura}°C, `
                + `Sensación: ${hora.sensacionTermica}°C, `
                + `Lluvia: ${hora.lluvia}mm, `
                + `Humedad: ${hora.humedad}%, `
                + `Viento: ${hora.vientoVelocidad} m/s, `
                + `Dir: ${hora.vientoDireccion}°`
            );
            listaHoras.append(li);
        });
        contenedor.append(listaHoras);
    }


    getMeteorologiaEntrenos(fechaCarrera) {

        // Obtener las fechas de los 3 días anteriores
        let fecha = new Date(fechaCarrera);
        let fechas = [];
    
        for (let i = 1; i <= 3; i++) {
            let f = new Date(fecha);
            f.setDate(f.getDate() - i);
            fechas.push(f.toISOString().split("T")[0]);
        }
    
        // El rango es desde el día más antiguo al más reciente
        let start_date = fechas[2];
        let end_date = fechas[0];
    
        const url = `https://archive-api.open-meteo.com/v1/archive?latitude=${this.getLatitud()}&longitude=${this.getLongitud()}&start_date=${start_date}&end_date=${end_date}&hourly=temperature_2m,rain,relativehumidity_2m,windspeed_10m&timezone=Europe/Madrid`;
    
        $.ajax({
            url: url,
            method: "GET",
            dataType: "json",
            success: (datosJSON) => {
                const info = this.procesarJSONEntrenos(datosJSON);
                this.mostrarEnHTMLEntrenos(info);
            },
            error: (err) => {
                console.error("Error al obtener los datos meteorológicos de entrenos:", err);
            }
        });

    }

    procesarJSONEntrenos(datosJSON) {
        // Objeto donde guardaremos los datos agrupados por día
        let dias = {};
    
        let tiempos = datosJSON.hourly.time;
        let temps = datosJSON.hourly.temperature_2m;
        let lluvias = datosJSON.hourly.rain;
        let humedades = datosJSON.hourly.relativehumidity_2m;
        let vientos = datosJSON.hourly.windspeed_10m;
    
        // Recorremos todos los datos horarios
        for (let i = 0; i < tiempos.length; i++) {
    
            // Extraemos la fecha (YYYY-MM-DD) de la hora completa
            let fecha = tiempos[i].split("T")[0];
    
            // Si ese día no existe aún en el objeto, lo creamos
            if (!dias[fecha]) {
                dias[fecha] = {
                    temperatura: [],
                    lluvia: [],
                    humedad: [],
                    viento: []
                };
            }
    
            // Guardamos cada dato en su array correspondiente
            dias[fecha].temperatura.push(temps[i]);
            dias[fecha].lluvia.push(lluvias[i]);
            dias[fecha].humedad.push(humedades[i]);
            dias[fecha].viento.push(vientos[i]);
        }
    
        // Ahora calculamos las medias de cada día
        let resultado = {
            dias: []
        };
    
        for (let fecha in dias) {
    
            let datos = dias[fecha];
    
            // Función interna para calcular media con dos decimales
            const media = arr => (arr.reduce((a, b) => a + b, 0) / arr.length).toFixed(2);
    
            resultado.dias.push({
                fecha: fecha,
                temperaturaMedia: parseFloat(media(datos.temperatura)),
                lluviaMedia: parseFloat(media(datos.lluvia)),
                humedadMedia: parseFloat(media(datos.humedad)),
                vientoMedio: parseFloat(media(datos.viento))
            });
        }
    
        return resultado;
    }


    mostrarEnHTMLEntrenos(info) {
        // Seleccionamos el tercer <section> para entrenos (por ejemplo)
        let contenedor = $("section").eq(2);
        contenedor.empty();
    
        // Título
        contenedor.append(
            $("<h2>").text(`Medias meteorológicas de los días de entrenamientos`)
        );
    
        // Recorremos cada uno de los 3 días
        info.dias.forEach(dia => {
    
            // Subtítulo del día
            contenedor.append(
                $("<h3>").text(`Día ${dia.fecha}`)
            );
    
            // Lista con las medias
            let lista = $("<ul>");
    
            lista.append(
                $("<li>").text(`Temperatura media: ${dia.temperaturaMedia} °C`)
            );
            lista.append(
                $("<li>").text(`Lluvia media: ${dia.lluviaMedia} mm`)
            );
            lista.append(
                $("<li>").text(`Humedad media: ${dia.humedadMedia} %`)
            );
            lista.append(
                $("<li>").text(`Viento medio: ${dia.vientoMedio} m/s`)
            );

            contenedor.append(lista);
        });
    }



}