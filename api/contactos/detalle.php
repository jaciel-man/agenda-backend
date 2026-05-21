<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$usuario = verificarToken();
$db = (new Database())->getConnection();
$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID requerido"]);
    exit;
}

$stmt = $db->prepare("SELECT * FROM contactos WHERE id = :id AND usuario_id = :uid");
$stmt->bindParam(':id', $id);
$stmt->bindParam(':uid', $usuario['id']);
$stmt->execute();

if ($stmt->rowCount() === 0) {
    echo json_encode(["success" => false, "message" => "Contacto no encontrado"]);
    exit;
}

$contacto = $stmt->fetch(PDO::FETCH_ASSOC);
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 3) . "/uploads/contactos/";
$contacto['foto'] = $contacto['foto'] ? $baseUrl . $contacto['foto'] : null;

echo json_encode(["success" => true, "contacto" => $contacto]);