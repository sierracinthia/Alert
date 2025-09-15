<?php
// controllers/AlertController.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/Contact.php';
//session_estart(); 
class AlertController {

    public function send() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        header('Content-Type: application/json');


        $id_user = 1; // ID de usuario de prueba
        
        $username = 'UsuarioPrueba';
      //$id_user = $_SESSION['user_id'] ?? null;
       //$username = $_SESSION['username'] ?? 'Usuario';

       //if (!$id_user) {
        //echo json_encode(['status' => 'error', 'message' => 'Usuario no autenticado']);
        //return;
        //}

        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) $input = [];

        $source = $input['source'] ?? null;
        $latitude = null;
        $longitude = null;

        try {
            // Determinar origen
            if ($source === 'device') {
                $locationModel = new Location();
                $gpsData = $locationModel->getGPSNow($id_user);

                if (!$gpsData || !isset($gpsData['latitude'], $gpsData['longitude'])) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'No se pudo obtener ubicación del GPS',
                        'input' => $input,
                        'gpsData' => $gpsData
                    ]);
                    return;
                }

                $latitude = $gpsData['latitude'];
                $longitude = $gpsData['longitude'];

            } elseif (isset($input['latitude'], $input['longitude'])) {
                $latitude = floatval($input['latitude']);
                $longitude = floatval($input['longitude']);
                $source = 'web';
            } else {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Datos de ubicación incompletos',
                    'input' => $input
                ]);
                return;
            }

            // Guardar alerta en DB
            $pdo = conectar();
            if (!$pdo) {
                echo json_encode([
                    'status' => 'error',
                    'message' => 'No se pudo conectar a la DB',
                    'input' => $input
                ]);
                return;
            }

            $stmt = $pdo->prepare(
                "INSERT INTO alerts (id_user, latitude, longitude) VALUES (?, ?, ?)"
            );
            $stmt->execute([$id_user, $latitude, $longitude]);

            // Obtener contactos
            $stmt = $pdo->prepare("SELECT contact_email, name FROM contacts WHERE id_user = ?");
            $stmt->execute([$id_user]);
            $contacts = $stmt->fetchAll();
            cerrarConexion($pdo);

            // Notificar contactos (FastAPI)
            $urlFastAPI = "http://149.50.133.15:8026/confirm";
            foreach ($contacts as $contact) {
                $email = $contact['contact_email'];
                $name = $contact['name'];

                $data = [
                    "email" => $email,
                    "asunto" => "Alerta de ubicación de $username",
                    "cuerpo" => "<p>Hola $name,</p>
                                 <p>$username ha enviado una alerta.</p>
                                 <p>Ubicación: <strong>Lat:</strong> $latitude, <strong>Lng:</strong> $longitude</p>
                                 <p>Origen: <strong>$source</strong></p>"
                ];

                $options = [
                    'http' => [
                        'header'  => "Content-Type: application/json\r\n",
                        'method'  => 'POST',
                        'content' => json_encode($data),
                        'timeout' => 5
                    ]
                ];

                $context = stream_context_create($options);
                @file_get_contents($urlFastAPI, false, $context);
            }

            echo json_encode([
                'status' => 'ok',
                'message' => 'Alerta enviada correctamente',
                'latitude' => $latitude,
                'longitude' => $longitude,
                'source' => $source,
                'input_received' => $input
            ]);

        } catch (Exception $e) {
            // Devuelve toda la info para depuración desde el dashboard
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al enviar alerta',
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'input_received' => $input
            ]);
        }
    }
}
?>
