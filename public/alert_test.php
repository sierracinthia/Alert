<?php
require_once __DIR__ . '/../src/services/send.php';

$email = "destinatario@ejemplo.com";
$name = "Contacto de prueba";
$username = "Cinthia";
$latitude = -34.707403;
$longitude = -58.714312;
$source = "web";

$result = sendAlertEmail($email, $name, $username, $latitude, $longitude, $source);

if ($result === false) {
    echo "❌ Error al enviar el correo";
} else {
    echo "✅ Correo enviado correctamente";
}
