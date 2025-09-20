<?php
function sendAlertEmail($email, $name, $username, $latitude, $longitude, $source) {
    $urlFastAPI = "http://149.50.133.15:8026/confirm";

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
    $result = @file_get_contents($urlFastAPI, false, $context);

    return $result !== false;
}
?>
