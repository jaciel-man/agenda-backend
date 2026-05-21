<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->nombre_de_usuario) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "Faltan campos"]);
    exit;
}

$db = (new Database())->getConnection();
$password_hash = password_hash($data->password, PASSWORD_BCRYPT);

$stmt = $db->prepare("INSERT INTO usuarios (nombre_de_usuario, password) VALUES (:usuario, :pass)");
$stmt->bindParam(':usuario', $data->nombre_de_usuario);
$stmt->bindParam(':pass', $password_hash);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Usuario registrado"]);
} else {
    echo json_encode(["success" => false, "message" => "Error al registrar"]);
}