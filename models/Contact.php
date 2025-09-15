<?php
require_once '../config/db.php';

class Contact {
    private $pdo;

    public function __construct() {
        //require_once __DIR__ . '/../config/db.php';
        $this->pdo = conectar(); // 👈 crear la conexión directamente
        if (!$this->pdo) {
            die("Error: no se pudo conectar a la base de datos");
        }
    }

    public function addContact($userId, $name, $email) {
        $stmt = $this->pdo->prepare("INSERT INTO contacts (id_user, contact_email, name) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $email, $name]);
    }

    public function getByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM contacts WHERE id_user = ?");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteContact($userId, $contactId) {
    $stmt = $this->pdo->prepare("DELETE FROM contacts WHERE id_contact = ? AND id_user = ?");
    $stmt->execute([$contactId, $userId]);
}

}
