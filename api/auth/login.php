<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/database.php';

$data = json_decode(file_get_contents("php://input"));

if (!isset($data->nombre_de_usuario) || !isset($data->password)) {
    echo json_encode(["success" => false, "message" => "Credenciales incompletas"]);
    exit;
}

$db = (new Database())->getConnection();
$stmt = $db->prepare("SELECT id, nombre_de_usuario, password, foto FROM usuarios WHERE nombre_de_usuario = :usuario");
$stmt->bindParam(':usuario', $data->nombre_de_usuario);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo json_encode(["success" => false, "message" => "Usuario no encontrado"]);
    exit;
}

$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!password_verify($data->password, $usuario['password'])) {
    echo json_encode(["success" => false, "message" => "Contraseña incorrecta"]);
    exit;
}

$token = bin2hex(random_bytes(32));
$expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

$updateStmt = $db->prepare("UPDATE usuarios SET token = :token, token_expiracion = :expiracion WHERE id = :id");
$updateStmt->bindParam(':token', $token);
$updateStmt->bindParam(':expiracion', $expiracion);
$updateStmt->bindParam(':id', $usuario['id']);
$updateStmt->execute();

// Construir URL base dinámica (para local y producción)
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 3) . "/uploads/usuarios/";

echo json_encode([
    "success" => true,
    "token" => $token,
    "usuario" => [
        "id" => $usuario['id'],
        "nombre_de_usuario" => $usuario['nombre_de_usuario'],
        "foto" => $usuario['foto'] ? $baseUrl . $usuario['foto'] : null
    ]
]);