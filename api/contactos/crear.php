<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$usuario = verificarToken();
$db = (new Database())->getConnection();

$nombre = $_POST['nombre'] ?? null;
$apellido = $_POST['apellido'] ?? null;
$telefono = $_POST['telefono'] ?? null;
$email = $_POST['email'] ?? null;
$direccion = $_POST['direccion'] ?? null;
$notas = $_POST['notas'] ?? null;

if (!$nombre || !$telefono) {
    echo json_encode(["success" => false, "message" => "Nombre y teléfono obligatorios"]);
    exit;
}

$fotoName = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array(strtolower($extension), $allowed)) {
        echo json_encode(["success" => false, "message" => "Formato no permitido"]);
        exit;
    }
    $fotoName = uniqid() . '.' . $extension;
    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../uploads/contactos/' . $fotoName);
}

$stmt = $db->prepare("INSERT INTO contactos (usuario_id, nombre, apellido, telefono, email, direccion, notas, foto) VALUES (:uid, :nombre, :apellido, :telefono, :email, :direccion, :notas, :foto)");
$stmt->bindParam(':uid', $usuario['id']);
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':apellido', $apellido);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':direccion', $direccion);
$stmt->bindParam(':notas', $notas);
$stmt->bindParam(':foto', $fotoName);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Contacto creado"]);