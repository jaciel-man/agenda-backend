<?php
require_once __DIR__ . '/database.php';

function verificarToken() {
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token requerido"]);
        exit;
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    $database = new Database();
    $db = $database->getConnection();

    $stmt = $db->prepare("SELECT id, nombre_de_usuario, foto, token_expiracion FROM usuarios WHERE token = :token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    if ($stmt->rowCount() === 0) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token inválido"]);
        exit;
    }

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    if (strtotime($usuario['token_expiracion']) < time()) {
        http_response_code(401);
        echo json_encode(["success" => false, "message" => "Token expirado"]);
        exit;
    }

    return $usuario; // Devuelve id, nombre_de_usuario, foto
}