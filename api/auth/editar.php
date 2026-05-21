<?php
require_once __DIR__ . '/../config/cors.php';
require_once __DIR__ . '/../config/auth.php';
require_once __DIR__ . '/../config/database.php';

$usuario = verificarToken();
$db = (new Database())->getConnection();

$nombre = $_POST['nombre_de_usuario'] ?? $usuario['nombre_de_usuario'];
$fotoName = $usuario['foto'];

if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $allowed = ['jpg', 'jpeg', 'png', 'webp'];
    if (!in_array(strtolower($extension), $allowed)) {
        echo json_encode(["success" => false, "message" => "Formato no permitido"]);
        exit;
    }
    $newName = uniqid() . '.' . $extension;
    $destino = __DIR__ . '/../uploads/usuarios/' . $newName;
    move_uploaded_file($_FILES['foto']['tmp_name'], $destino);
    // Eliminar foto anterior si existe
    if ($usuario['foto'] && file_exists(__DIR__ . '/../uploads/usuarios/' . $usuario['foto'])) {
        unlink(__DIR__ . '/../uploads/usuarios/' . $usuario['foto']);
    }
    $fotoName = $newName;
}

$stmt = $db->prepare("UPDATE usuarios SET nombre_de_usuario = :nombre, foto = :foto WHERE id = :id");
$stmt->bindParam(':nombre', $nombre);
$stmt->bindParam(':foto', $fotoName);
$stmt->bindParam(':id', $usuario['id']);
$stmt->execute();

echo json_encode(["success" => true, "message" => "Perfil actualizado"]);