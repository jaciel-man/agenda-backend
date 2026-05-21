<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$usuario = verificarToken();
$db = (new Database())->getConnection();

$stmt = $db->prepare("UPDATE usuarios SET token = NULL, token_expiracion = NULL WHERE id = :id");
$stmt->bindParam(':id', $usuario['id']);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Sesión cerrada"]);