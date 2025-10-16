import xml.etree.ElementTree as ET

# Clase Svg para crear el archivo altimetría.svg
class Svg:
    def __init__(self, width, height, output_file):
        self.width = width
        self.height = height
        self.output_file = output_file
        self.content = '<?xml version="1.0" encoding="UTF-8"?>\n'
        self.content += f'<svg width="60%" height="100%" viewBox="0 0 {width} {height}" '
        self.content += 'preserveAspectRatio="xMinYMin meet" xmlns="http://www.w3.org/2000/svg">\n'

    def add_polyline(self, points, color="red", fill="lightgray"):
        """Dibuja la polilínea cerrada (efecto suelo)"""
        pts = " ".join(f"{x},{y}" for x, y in points)
        self.content += f'<polyline points="{pts}" style="fill:{fill};stroke:{color};stroke-width:2" />\n'

    def add_line(self, x1, y1, x2, y2, color="red"):
        """Dibuja la línea de base"""
        self.content += f'<line x1="{x1}" y1="{y1}" x2="{x2}" y2="{y2}" style="stroke:{color};stroke-width:2" />\n'

    def close(self):
        """Cierra el archivo SVG"""
        self.content += '</svg>\n'
        with open(self.output_file, 'w', encoding='utf-8') as f:
            f.write(self.content)


# Función que obtiene las altitudes desde el XML usando XPath
def parse_xml(file_path):
    tree = ET.parse(file_path)
    root = tree.getroot()

    # Usamos expresiones XPath obligatoriamente
    alturas = [0.0]
    for altitud in root.findall('.//{http://www.uniovi.es}puntos/{http://www.uniovi.es}punto/{http://www.uniovi.es}coordenada/{http://www.uniovi.es}altitud'):
        try:
            alturas.append(float(altitud.text))
        except (ValueError, TypeError):
            pass
    alturas.append(0.0)
    return alturas


# Función que genera el SVG de la altimetría
def create_svg(alturas, output_file):
    margin_left = 20
    max_alt = max(alturas)
    min_alt = min(alturas)

    svg_height = 200
    svg_width = len(alturas) * 10 + margin_left
    scale_y = 160 / (max_alt - min_alt if max_alt != min_alt else 1)

    # Crear objeto SVG
    svg = Svg(svg_width, svg_height, output_file)

    # Generar puntos escalados
    points = []
    for i, altura in enumerate(alturas):
        x = i * 10 + margin_left
        y = svg_height - (altura - min_alt) * scale_y - 20
        points.append((x, y))

    # Cerrar la polilínea para el "efecto suelo"
    base_y = svg_height - 20
    points.append((points[-1][0], base_y))
    points.insert(0, (margin_left, base_y))

    # Dibujar polilínea rellena y línea base
    svg.add_polyline(points, color="red", fill="lightgray")
    svg.add_line(margin_left, base_y, points[-1][0], base_y, color="red")

    svg.close()



def main():
    xml_file = 'circuitoEsquema.xml'
    svg_file = 'altimetria.svg'

    alturas = parse_xml(xml_file)
    create_svg(alturas, svg_file)


if __name__ == "__main__":
    main()