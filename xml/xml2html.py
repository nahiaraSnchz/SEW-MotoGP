import xml.etree.ElementTree as ET
from pathlib import Path

# -------------------------------------------------------
# Clase Html para generar el archivo InfoCircuito.html
# -------------------------------------------------------
class Html:
    def __init__(self, title, css_path, output_file):
        self.output_file = Path(output_file)
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
        # Aquí solo se usa la ruta relativa que pasamos
        self.content.append(f'  <link rel="stylesheet" href="{css_path}">')
        self.content.append('</head>')
        self.content.append('<body>')
        self.content.append(f'<header><h1>{title}</h1></header>')
        self.content.append('<main>')

    def add_section(self, title):
        """Añadir una sección con encabezado"""
        self.content.append(f'<section>')
        self.content.append(f'  <h3>{title}</h3>')

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
    ciudad = root.get('ciudad', 'Sin ciudad')
    pais = root.get('pais', 'Sin país')
    data['descripcion'] = f"El circuito se encuentra en {ciudad}, {pais}."
    data['localizacion'] = f"{ciudad}, {pais}".strip(', ')
    longitud = root.findtext('u:longitudCircuito', default='', namespaces=ns)
    vueltas = root.findtext('u:vueltas', default='', namespaces=ns)
    data['longitud_vueltas'] = f"La longitud del circuito es de {longitud} km y se correrá a {vueltas} vueltas."

    # Foto
    foto = root.findtext('u:foto', default='', namespaces=ns)
    if foto:
        data['foto'] = foto

    # Video
    video = root.findtext('u:video', default='', namespaces=ns)
    if video:
        data['video'] = video

    # Vencedor y tres clasificados
    data['vencedor'] = root.findtext('u:vencedor', default='', namespaces=ns)
    data['clasificados'] = [c.text for c in root.findall('u:tresClasificados/u:clasificado', namespaces=ns)]

    # Referencias
    data['referencias'] = [r.text for r in root.findall('u:referencias/u:referencia', namespaces=ns)]

    return data

# -------------------------------------------------------
# Función principal
# -------------------------------------------------------
def main():
    xml_file = 'circuitoEsquema.xml'
    html_file = 'InfoCircuito.html'
    css_file = '../estilo/estilo.css'

    data = parse_xml(xml_file)

    html = Html(data.get('nombre', 'Circuito'), css_file, html_file)

    # Sección de información general
    html.add_section("Información general")
    html.add_paragraph(data.get('descripcion', ''))
    html.add_paragraph(data.get('longitud_vueltas', ''))
    html.end_section()

    # Sección de localización
    html.add_section("Localización")
    html.add_paragraph(data.get('localizacion', ''))
    html.end_section()

    # Imagen si existe
    if 'foto' in data:
        html.add_section("Imagen del circuito")
        html.content.append(f'<img src="{data["foto"]}" alt="Imagen del circuito" style="max-width:100%;height:auto;">')
        html.end_section()

    # Video si existe
    if 'video' in data:
        html.add_section("Video del circuito")
        html.content.append(f'<video controls style="max-width:100%;height:auto;"><source src="../multimedia/{data["video"]}" type="video/mp4">Tu navegador no soporta video.</video>')
        html.end_section()

    # Sección de resultados
    html.add_section("Resultados")
    html.add_paragraph(f"Vencedor: {data.get('vencedor', '')}")
    html.content.append('<ol>')
    for c in data.get('clasificados', []):
        html.content.append(f'<li>{c}</li>')
    html.content.append('</ol>')
    html.end_section()

    # Sección de referencias
    html.add_section("Referencias")
    html.add_list([f'<a href="{r}" target="_blank">{r}</a>' for r in data.get('referencias', [])])
    html.end_section()

    # Guardar HTML
    html.save()
    print(f"Archivo {html_file} generado correctamente.")

if __name__ == "__main__":
    main()
