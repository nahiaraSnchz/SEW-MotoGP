import xml.etree.ElementTree as ET

# -------------------------------------------------------
# Clase Html para generar el archivo InfoCircuito.html
# -------------------------------------------------------
class Html:
    def __init__(self, title, css_path, output_file):
        self.output_file = output_file
        self.content = []
        self.header(title, css_path)

    def header(self, title, css_path):
        """Encabezado del documento HTML"""
        self.content.append('<!DOCTYPE html>')
        self.content.append('<html lang="es">')
        self.content.append('<head>')
        self.content.append('  <meta charset="UTF-8">')
        self.content.append('  <meta name="viewport" content="width=device-width, initial-scale=1.0">')
        self.content.append(f'  <title>{title}</title>')
        self.content.append(f'  <link rel="stylesheet" href="{css_path}">')
        self.content.append('</head>')
        self.content.append('<body>')
        self.content.append(f'<header><h1>{title}</h1></header>')
        self.content.append('<main>')

    def add_section(self, title):
        """Añadir una sección con encabezado"""
        self.content.append(f'<section>')
        self.content.append(f'  <h2>{title}</h2>')

    def add_paragraph(self, text):
        """Añadir párrafo dentro de una sección"""
        self.content.append(f'  <p>{text}</p>')

    def add_list(self, items):
        """Añadir lista con elementos"""
        self.content.append('  <ul>')
        for item in items:
            self.content.append(f'    <li>{item}</li>')
        self.content.append('  </ul>')

    def end_section(self):
        """Cerrar una sección"""
        self.content.append('</section>')

    def footer(self):
        """Pie de página y cierre"""
        self.content.append('</main>')
        self.content.append('<footer><p>Generado automáticamente por xml2html.py</p></footer>')
        self.content.append('</body>')
        self.content.append('</html>')

    def save(self):
        """Guardar el archivo HTML"""
        self.footer()
        with open(self.output_file, 'w', encoding='utf-8') as f:
            f.write("\n".join(self.content))


# -------------------------------------------------------
# Función que extrae la información del XML usando XPath
# -------------------------------------------------------
def parse_xml(xml_path):
    tree = ET.parse(xml_path)
    root = tree.getroot()
    ns = {'u': 'http://www.uniovi.es'}

    data = {}

    # Información general del circuito
    data['nombre'] = root.get('nombre', 'Sin nombre')
    data['descripcion'] = "El circuito se encuentra en " + root.get('ciudad', 'Sin Ciudad') + ", " + root.get('pais', 'Sin país') + "."
    data['longitud'] = "La longitud del circuito es de " + root.findtext('.//u:longitudCircuito', default='', namespaces=ns) + "m y de un total de " + root.findtext('.//u.vueltas', default='', namespaces=ns) + "."

    # Localización
    pais = root.get('pais', 'Sin país')
    ciudad = root.get('ciudad', 'Sin ciudad')
    data['localizacion'] = f"{ciudad}, {pais}".strip(', ')

    # Imagen (si existe)
    img = root.findtext('.//u:imagen', default='', namespaces=ns)
    if img:
        data['imagen'] = img

    

    return data


# -------------------------------------------------------
# Función principal
# -------------------------------------------------------
def main():
    xml_file = 'circuitoEsquema.xml'
    html_file = 'InfoCircuito.html'
    css_path = 'css/estilo.css'

    data = parse_xml(xml_file)

    html = Html(data.get('nombre', 'Circuito'), css_path, html_file)

    # Sección de información general
    html.add_section("Información general")
    html.add_paragraph(data.get('descripcion', ''))
    html.add_paragraph(data.get('longitud'))
    html.end_section()

    # Sección de localización
    html.add_section("Localización")
    html.add_paragraph(data.get('localizacion', ''))
    html.end_section()

    # Imagen si existe
    if 'imagen' in data:
        html.add_section("Imagen del circuito")
        html.content.append(f'<img src="{data["imagen"]}" alt="Imagen del circuito" style="max-width:100%;height:auto;">')
        html.end_section()



    html.save()
    print(f"Archivo {html_file} generado correctamente.")


if __name__ == "__main__":
    main()