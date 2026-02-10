<?php
session_start();
header('Content-Type: application/json');

// Leer el input de JS
$json = file_get_contents("php://input");
$datosRecibidos = json_decode($json, true);

if (is_array($datosRecibidos)) {
    // Guardamos en la sesión para usarlo después
    $_SESSION['carrito_variantes'] = $datosRecibidos;

    echo json_encode([
        "status" => "ok",
        "mensaje" => "Variantes actualizadas",
        "conteo" => count($datosRecibidos)
    ]);
} else {
    http_response_code(400);
    echo json_encode(["status" => "error", "mensaje" => "JSON no válido"]);
}