<?php
$host = "mysql-jaciel12.alwaysdata.net";
$dbname = "jaciel12_agenda";
$username = "jaciel12_admin";
$password = "clave123";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    echo "Conexión exitosa";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}