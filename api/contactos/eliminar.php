<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$usuario = verificarToken();
$db = (new Database())->getConnection();
$data = json_decode(file_get_contents("php://input"));
$id = $data->id ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID requerido"]);
    exit;
}

// Verificar pertenencia y obtener foto para borrarla
$stmt = $db->prepare("SELECT foto FROM contactos WHERE id = :id AND usuario_id = :uid");
$stmt->bindParam(':id', $id);
$stmt->bindParam(':uid', $usuario['id']);
$stmt->execute();
if ($stmt->rowCount() === 0) {
    echo json_encode(["success" => false, "message" => "Contacto no encontrado"]);
    exit;
}
$contacto = $stmt->fetch(PDO::FETCH_ASSOC);

if ($contacto['foto'] && file_exists(__DIR__ . '/../uploads/contactos/' . $contacto['foto'])) {
    unlink(__DIR__ . '/../uploads/contactos/' . $contacto['foto']);
}

$stmt = $db->prepare("DELETE FROM contactos WHERE id = :id AND usuario_id = :uid");
$stmt->bindParam(':id', $id);
$stmt->bindParam(':uid', $usuario['id']);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Contacto eliminado"]);