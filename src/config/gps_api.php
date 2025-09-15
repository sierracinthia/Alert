<?php
// gps_api.php

function callGPSAPI($endpoint, $params = []) {
    $baseUrl = "http://149.50.133.15:5000";  // URL base de tu GPS
    $username = "admin";
    $password = "password";

    // IMEI fijo para pruebas
    $imei = "4208298709"; 

    $url = $baseUrl . $endpoint;
    if ($imei !== null) {
        $url .= "/" . $imei;
    }

    if (!empty($params)) {
        $url .= "?" . http_build_query($params);
    }

    $options = [
        "http" => [
            "header"  => "Authorization: Basic " . base64_encode("$username:$password") . "\r\n",
            "method"  => "GET",
            "timeout" => 10
        ]
    ];

    $context = stream_context_create($options);

    $response = @file_get_contents($url, false, $context);

    if ($response === false) return null;

    return json_decode($response, true);
}
