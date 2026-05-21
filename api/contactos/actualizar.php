<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$usuario = verificarToken();
$db = (new Database())->getConnection();
$id = $_POST['id'] ?? null;

if (!$id) {
    echo json_encode(["success" => false, "message" => "ID requerido"]);
    exit;
}

// Verificar pertenencia
$stmt = $db->prepare("SELECT foto FROM contactos WHERE id = :id AND usuario_id = :uid");
$stmt->bindParam(':id', $id);
$stmt->bindParam(':uid', $usuario['id']);
$stmt->execute();
if ($stmt->rowCount() === 0) {
    echo json_encode(["success" => false, "message" => "Contacto no encontrado"]);
    exit;
}
$contacto = $stmt->fetch(PDO::FETCH_ASSOC);
$fotoName = $contacto['foto'];

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array(strtolower($extension), $allowed)) {
        echo json_encode(["success" => false, "message" => "Formato no permitido"]);
        exit;
    }
    $newName = uniqid() . '.' . $extension;
    move_uploaded_file($_FILES['foto']['tmp_name'], __DIR__ . '/../uploads/contactos/' . $newName);
    // Eliminar anterior
    if ($contacto['foto'] && file_exists(__DIR__ . '/../uploads/contactos/' . $contacto['foto'])) {
        unlink(__DIR__ . '/../uploads/contactos/' . $contacto['foto']);
    }
    $fotoName = $newName;
}

$nombre = $_POST['nombre'] ?? $contacto['nombre'] ?? '';
$apellido = $_POST['apellido'] ?? $contacto['apellido'] ?? '';
$telefono = $_POST['telefono'] ?? $contacto['telefono'] ?? '';
$email = $_POST['email'] ?? $contacto['email'] ?? '';
$direccion = $_POST['direccion'] ?? $contacto['direccion'] ?? '';
$notas = $_POST['notas'] ?? $contacto['notas'] ?? '';

$stmt = $db->prepare("UPDATE contactos SET nombre=:nombre, apellido=:apellido, telefono=:telefono, email=:email, direccion=:direccion, notas=:notas, foto=:foto WHERE id=:id AND usuario_id=:uid");
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':apellido', $apellido);
$stmt->bindParam(':telefono', $telefono);
$stmt->bindParam(':email', $email);
$stmt->bindParam(':direccion', $direccion);
$stmt->bindParam(':notas', $notas);
$stmt->bindParam(':foto', $fotoName);
$stmt->bindParam(':id', $id);
$stmt->bindParam(':uid', $usuario['id']);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Contacto actualizado"]);