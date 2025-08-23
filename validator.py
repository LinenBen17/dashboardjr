""" import mysql.connector
from rapidfuzz import fuzz, process
import json

# Configuración de conexión MySQL
config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'laravel11crud'
}

def obtener_destinos():
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()
    cursor.execute("SELECT direccion FROM historico")
    destinos = [row[0] for row in cursor.fetchall()]
    cursor.close()
    conn.close()
    return destinos

def validar_cobertura(direccion, destinos, umbral=80):
    coincidencia = process.extractOne(direccion, destinos, scorer=fuzz.token_sort_ratio)
    if coincidencia and coincidencia[1] >= umbral:
        resultado = {
            "cobertura": True,
            "lugar": coincidencia[0],
            "similitud": coincidencia[1]
        }
    else:
        resultado = {
            "cobertura": False,
            "mensaje": "No hay registros previos en esa ubicación"
        }
    return json.dumps(resultado, ensure_ascii=False)

if __name__ == "__main__":
    destinos = obtener_destinos()
    direccion_a_validar = input("Ingresa la dirección a validar: ")
    resultado_json = validar_cobertura(direccion_a_validar, destinos)
    print(resultado_json) """


import requests
import mysql.connector
import time

config = {
    'user': 'root',
    'password': '',
    'host': 'localhost',
    'database': 'laravel11crud'
}

def geocodificar_osm(direccion):
    url = "https://nominatim.openstreetmap.org/search"
    params = {
        'q': direccion,
        'format': 'json',
        'limit': 1,
        'addressdetails': 0
    }
    response = requests.get(url, params=params)
    if response.status_code == 200 and response.json():
        data = response.json()[0]
        return float(data['lat']), float(data['lon'])
    else:
        return None, None


def actualizar_coordenadas():
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()
    cursor.execute("SELECT idfactura, direccion FROM historico WHERE latitud IS NULL OR longitud IS NULL")
    filas = cursor.fetchall()

    for fila in filas:
        id_envio, direccion = fila
        lat, lon = geocodificar_osm(direccion)
        if lat and lon:
            cursor.execute("UPDATE historico SET latitud=%s, longitud=%s WHERE id=%s", (lat, lon, id_envio))
            conn.commit()
            print(f"Actualizado {id_envio}: {lat}, {lon}")
        else:
            print(f"No se pudo geocodificar: {direccion}")
        time.sleep(1)  # Para no saturar el servicio

    cursor.close()
    conn.close()

if __name__ == "__main__":
    actualizar_coordenadas()