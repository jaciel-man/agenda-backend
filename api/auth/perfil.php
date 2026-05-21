<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/auth.php';

$usuario = verificarToken();

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 3) . "/uploads/usuarios/";

echo json_encode([
    "success" => true,
    "usuario" => [
        "id" => $usuario['id'],
        "nombre_de_usuario" => $usuario['nombre_de_usuario'],
        "foto" => $usuario['foto'] ? $baseUrl . $usuario['foto'] : null
    ]
]);