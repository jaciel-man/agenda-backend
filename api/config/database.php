<?php
class Database {
   private $host = "mysql-jaciel12.alwaysdata.net";
    private $db_name = "jaciel12_agenda";
    private $username = "jaciel12_admin";   // el nuevo usuario
    private $password = "clave123";     // Cambia en producción
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("set names utf8");
        } catch(PDOException $exception) {
            echo json_encode(["success" => false, "message" => "Error de conexión"]);
            exit;
        }
        return $this->conn;
    }
}