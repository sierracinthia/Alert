<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/gps_api.php';

class Location {
    private $pdo;

    public function __construct() {
        $this->pdo = conectar();
        if (!$this->pdo) {
            throw new Exception("No se pudo conectar a la base de datos");
        }
        
        date_default_timezone_set('America/Argentina/Buenos_Aires');

    }

    public function addAlert($userId, $lat, $lng) {
                $sentAt = date('Y-m-d H:i:s'); // hora local

        $stmt = $this->pdo->prepare("INSERT INTO alerts (id_user, latitude, longitude) VALUES (?, ?, ?)");
        $stmt->execute([$userId, $lat, $lng]);
    }

    public function getAlertsByUser($userId) {
        $stmt = $this->pdo->prepare("SELECT * FROM alerts WHERE id_user = ? ORDER BY sent_at DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGPSNow($userId) {
        $imei = $this->getImeiByUser($userId);
        $url = "http://149.50.133.15:5000/gpsnow/" . $imei;
        $username = "admin";
        $password = "password";

        $options = [
            "http" => [
                "header"  => "Authorization: Basic " . base64_encode("$username:$password") . "\r\n",
                "method"  => "GET",
                "timeout" => 10
            ]
        ];

        $context = stream_context_create($options);
        $response = @file_get_contents($url, false, $context);

        if ($response === false) {
            error_log("Error al obtener datos del GPS para IMEI $imei");
            return null;
        }

        $data = json_decode($response, true);

        if (!$data || !isset($data[0]['latitude'], $data[0]['longitude'])) {
            return null;
        }

        return [
            'latitude' => floatval($data[0]['latitude']),
            'longitude' => floatval($data[0]['longitude']),
            'timestamp' => $data[0]['timestamp'] ?? null
        ];
    }

    private function getImeiByUser($userId) {
        // Ejemplo simple: hardcodeado
        return "4208298709";
    }

    public function getContactsByUser($userId) {
        $contactModel = new Contact();
        return $contactModel->getByUser($userId);
    }

    public function __destruct() {
        cerrarConexion($this->pdo);
    }
}
?>
