<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$usuario = verificarToken();
$db = (new Database())->getConnection();

$stmt = $db->prepare("SELECT * FROM contactos WHERE usuario_id = :uid ORDER BY fecha_creacion DESC");
$stmt->bindParam(':uid', $usuario['id']);
$stmt->execute();
$contactos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME'], 3) . "/uploads/contactos/";

foreach ($contactos as &$c) {
    $c['foto'] = $c['foto'] ? $baseUrl . $c['foto'] : null;
}

echo json_encode(["success" => true, "contactos" => $contactos]);