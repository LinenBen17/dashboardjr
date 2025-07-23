# printer_server.py
from flask import Flask, request
import win32print
import win32api
import textwrap
import datetime
from flask_cors import CORS

app = Flask(__name__)
CORS(app)  # ← Esto permite todas las solicitudes CORS

ESC = b'\x1B'
LF = b'\x0A'

def set_position(x_spaces, y_lines):
    return LF * y_lines + b' ' * x_spaces

def truncar_con_puntos(texto, max_len=26):
    return texto if len(texto) <= max_len else texto[:max_len - 3] + '...'

hora_actual = datetime.datetime.now()

@app.route('/status', methods=['GET'])
def status():
    return {'status': 'ok'}

@app.route('/print', methods=['POST'])
def print_job():
    print("Recibiendo solicitud de impresión...")
    content = request.json
    print("Contenido recibido:", content)

    if not content:
        return {'error': 'No content'}, 400

    # Extraer datos
    forma_pago = content.get('payment_method', '').upper()
    nombre_remitente = content.get('sender_name', '').upper()
    direccion_remitente = content.get('sender_address', '').upper()
    numero_remitente = content.get('sender_phone', '').upper()
    origen = content.get('prefix_origin', '').upper()
    nombre_destinatario = content.get('receiver_name', '').upper()
    direccion_destinatario = content.get('receiver_address', '').upper()
    numero_destinatario = content.get('receiver_phone', '').upper()
    destino = content.get('prefix_destination', '').upper()
    descripcion_producto = content.get('product_description', '').upper()
    tarifa = str(content.get('total', '')).upper()
    codigo_cliente = (str(content.get('sender_code', '')) + str(content.get('receiver_code', ''))).upper()
    no_piezas = str(content.get('pieces', '')).upper()
    usuario = content.get('user', '').upper()
    dia = str(content.get('dia', '')).upper()
    mes = str(content.get('mes', '')).upper()
    anio = str(content.get('anio', '')).upper()

    # Dividir líneas
    lineasNombreRem = textwrap.wrap(nombre_remitente, width=37)
    lineasNombreDes = textwrap.wrap(nombre_destinatario, width=40)
    lineasDireccionRem = textwrap.wrap(direccion_remitente, width=37)
    lineasDireccionDes = textwrap.wrap(direccion_destinatario, width=40)

    # Comenzar datos RAW
    raw_data = ESC + b'@'  # Reset
    raw_data += ESC + b'\x4D' + ESC + b'E'  # Fuente B + Negrita

    # Forma de pago y nombre destinatario
    raw_data += set_position(65, 3) + forma_pago.encode('latin1')
    if len(lineasNombreDes) > 0:
        raw_data += set_position(42, 2) + lineasNombreDes[0].encode('latin1')

    # Nombre del remitente
    nL, nH = 15, 0
    raw_data += ESC + b'$' + bytes([nL, nH]) + lineasNombreRem[0].encode('latin1')

    if (len(lineasNombreRem) > 1) or (len(lineasNombreDes) > 1):
        raw_data += LF

    if len(lineasNombreRem) > 1:
        raw_data += ESC + b'$' + bytes([nL, nH]) + lineasNombreRem[1].encode('latin1')

    if len(lineasNombreDes) > 1:
        if len(lineasNombreRem) > 1:
            raw_data += set_position(39 - len(lineasNombreRem[1]), 0) + lineasNombreDes[1].encode('latin1')
        else:
            raw_data += set_position(42, 0) + lineasNombreDes[1].encode('latin1')

    raw_data += LF

    # Dirección remitente y destinatario
    lineaRem1 = lineasDireccionRem[0]
    lineaDes1 = lineasDireccionDes[0]
    raw_data += ESC + b'$' + bytes([nL, nH])
    raw_data += lineaRem1.encode('latin1') + set_position(39 - len(lineaRem1), 0) + lineaDes1.encode('latin1')

    if (len(lineasDireccionRem) > 1) or (len(lineasDireccionDes) > 1):
        raw_data += LF

    if len(lineasDireccionRem) > 1:
        lineaRem2 = truncar_con_puntos(lineasDireccionRem[1])
        raw_data += ESC + b'$' + bytes([nL, nH]) + lineaRem2.encode('latin1')
        raw_data += set_position(30 - len(lineaRem2), 0) + origen.encode('latin1')

    if len(lineasDireccionDes) > 1:
        lineaDes2 = truncar_con_puntos(lineasDireccionDes[1])
        if len(lineasDireccionRem) > 1:
            raw_data += set_position(6, 0) + lineaDes2.encode('latin1')
            raw_data += set_position(31 - len(lineaDes2), 0) + destino.encode('latin1')
            raw_data += LF
            raw_data += ESC + b'$' + bytes([nL, nH]) + numero_remitente.encode('latin1')
            raw_data += set_position(39 - len(numero_remitente), 0) + numero_destinatario.encode('latin1')
        else:
            raw_data += ESC + b'$' + bytes([nL, nH]) + numero_remitente.encode('latin1')
            raw_data += set_position(30 - len(numero_remitente), 0) + origen.encode('latin1')
            raw_data += set_position(6, 0) + lineaDes2.encode('latin1')
            raw_data += set_position(31 - len(lineaDes2), 0) + destino.encode('latin1')
            raw_data += LF
            raw_data += set_position(42, 0) + numero_destinatario.encode('latin1')
    else:
        if len(lineasDireccionRem) > 1:
            raw_data += set_position(6, 0) + numero_destinatario.encode('latin1')
            raw_data += set_position(31 - len(numero_destinatario), 0) + destino.encode('latin1')
            raw_data += LF
            raw_data += ESC + b'$' + bytes([nL, nH]) + numero_remitente.encode('latin1')
        else:
            raw_data += LF
            raw_data += ESC + b'$' + bytes([nL, nH]) + numero_remitente.encode('latin1')
            raw_data += set_position(30 - len(numero_remitente), 0) + origen.encode('latin1')
            raw_data += set_position(6, 0) + numero_destinatario.encode('latin1')
            raw_data += set_position(31 - len(numero_destinatario), 0) + destino.encode('latin1')
    
    # DESCRIPCIÓN DEL ENVIO
    raw_data += LF
    raw_data += LF
    raw_data += ESC + b'$' + bytes([nL, nH]) + truncar_con_puntos(descripcion_producto, 33).encode('latin1')

    # TARIFA DEL ENVIO
    raw_data += set_position(36 - len(truncar_con_puntos(descripcion_producto, 33)), 0) + tarifa.encode('latin1')

    # CODIGO DE CLIENTE
    raw_data += set_position(10, 0) + codigo_cliente.encode('latin1')

    # NO. PIEZAS
    raw_data += LF
    raw_data += LF
    raw_data += set_position(6, 0) + no_piezas.encode('latin1')

    # USUARIO
    raw_data += set_position(45 - len(no_piezas), 0) + usuario.encode('latin1')

    # DIA / MES / AÑO / HORA DE IMPRESION
    hora_formateada = hora_actual.strftime('%H:%M')
    raw_data += set_position(5, 0) + dia.encode('latin1') + set_position(2, 0) + mes.encode('latin1') + set_position(3, 0) + anio[-2:].encode('latin1') + set_position(1, 0) + hora_formateada.encode('latin1')

    # AVANZA A LA SIGUIENTE PAGINA
    raw_data += LF * 4
    raw_data += ESC + b'J' + bytes([18])  # Avanza 0.5 líneas (2.115 mm ≈ 15/180 pulgadas)

    # Enviar a impresora
    printer_name = win32print.GetDefaultPrinter()
    hPrinter = win32print.OpenPrinter(printer_name)
    try:
        hJob = win32print.StartDocPrinter(hPrinter, 1, ("Guía de Envío", None, "RAW"))
        win32print.StartPagePrinter(hPrinter)
        win32print.WritePrinter(hPrinter, raw_data)
        win32print.EndPagePrinter(hPrinter)
        win32print.EndDocPrinter(hPrinter)
        return {'status': 'printed'}
    finally:
        win32print.ClosePrinter(hPrinter)

if __name__ == '__main__':
    app.run(port=9000)