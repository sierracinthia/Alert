<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Location.php';
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../services/send.php';

class AlertController {

    public function send() {
        if (session_status() === PHP_SESSION_NONE) session_start();

        header('Content-Type: application/json');

        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $source = $input['source'] ?? null;

        // Determinar usuario
       //$id_user = $_SESSION['user_id'] ?? 1;           // Usuario de prueba si no hay sesión
       // $username = $_SESSION['username'] ?? 'UsuarioPrueba';
        $id_user = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? 'Usuario'; 
       
       if (isset($input['id_user'])) $id_user = intval($input['id_user']);

        $latitude = null;
        $longitude = null;

        try {
            if (!$id_user) throw new Exception("No se pudo determinar id_user");

            $locationModel = new Location();

            // Determinar origen de la alerta
            if ($source === 'device') {
                $gpsData = $locationModel->getGPSNow($id_user);
                if (!$gpsData) throw new Exception('No se pudo obtener ubicación del GPS');
                $latitude = $gpsData['latitude'];
                $longitude = $gpsData['longitude'];
            } elseif (isset($input['latitude'], $input['longitude'])) {
                $latitude = floatval($input['latitude']);
                $longitude = floatval($input['longitude']);
                $source = 'web';
            } else {
                throw new Exception('Datos de ubicación incompletos');
            }

            // Guardar alerta en DB
            $locationModel->addAlert($id_user, $latitude, $longitude);

            // Obtener contactos del usuario
            $contacts = $locationModel->getContactsByUser($id_user);

            $sendErrors = [];
            foreach ($contacts as $contact) {
                $result = sendAlertEmail(
                    $contact['contact_email'],
                    $contact['name'],
                    $username,
                    $latitude,
                    $longitude,
                    $source
                );
                if (!$result) $sendErrors[] = $contact['contact_email'];
            }

            // Devolver JSON válido
            echo json_encode([
                'status' => 'ok',
                'message' => 'Alerta enviada correctamente',
                'latitude' => $latitude,
                'longitude' => $longitude,
                'source' => $source,
                'input_received' => $input,
                'sendErrors' => $sendErrors
            ]);

        } catch (Exception $e) {
            // Captura cualquier error y devuelve JSON limpio
            error_log("Error en AlertController::send - " . $e->getMessage());
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage(),
                'input_received' => $input
            ]);
        }
    }
}
?>
