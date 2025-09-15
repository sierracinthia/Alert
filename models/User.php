<?php
require_once __DIR__ . '/../config/db.php';

class User {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $this->conn = conectar();
    }

    // Verifica credenciales por email
    public function verifyCredentials($email, $password) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE LOWER(email) = :email LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $email_lower = strtolower(trim($email));
        $stmt->bindParam(":email", $email_lower);
        $stmt->execute();

        $user = $stmt->fetch();

        if ($user && password_verify($password, $user["password_hash"])) {
            return $user;
        }
        return false;
    }
}
