<?php
/*
Plugin Name: Custom API Plugin - Productos
Description: Listar productos de otra base de datos.
Version: 1.0
Author: QS SA.
*/

// Evitar acceso directo
if ( !defined('ABSPATH') ) {
    exit;
}

// Registrar el endpoint para listar datos
add_action('rest_api_init', function () {
    register_rest_route('custom/v1', '/test', [
        'methods' => 'GET',
        'callback' => 'custom_api_list',
    ]);
});

// Conexión a la base de datos externa y retorno de resultados
function custom_api_list() {
    // Credenciales de la base de datos externa
    $host = '127.0.0.1';
    $usuario = 'root';
    $contraseña = 'root';
    $base_datos = 'practica';

    // Crear conexión usando mysqli
    $mysqli = new mysqli($host, $usuario, $contraseña, $base_datos);

    // Verificar conexión
    if ($mysqli->connect_error) {
        return new WP_Error('db_connection_error', 'Error de conexión a la base de datos', ['status' => 500]);
    }

    // Ejecutar consulta
    $query = "SELECT * FROM productos";
    $resultado = $mysqli->query($query);

    // Formatear resultados
    $datos = [];
    if ($resultado && $resultado->num_rows > 0) {
        while ($fila = $resultado->fetch_assoc()) {
            $datos[] = $fila;
        }
    }

    // Cerrar conexión
    $mysqli->close();

    // Retornar resultados como respuesta JSON
    return rest_ensure_response($datos);
}