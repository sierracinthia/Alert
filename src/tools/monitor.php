<?php

    $imei = "4208298709";
    $id_user = 1;
    $lastTimestamp = null;

    while (true) {
        $url = "http://149.50.133.15:5000/gpsnow/$imei";
        $auth = base64_encode("admin:password");

        $opts = [
            "http" => [
                "header" => "Authorization: Basic $auth\r\n",
                "method" => "GET",
                "timeout" => 10
            ]
        ];

        $context = stream_context_create($opts);
        $response = file_get_contents($url, false, $context);
        $data = json_decode($response, true);

        if ($data && isset($data[0]['timestamp'])) {
            $currentTimestamp = $data[0]['timestamp'];

            if ($lastTimestamp !== null && $currentTimestamp !== $lastTimestamp) {
                $alert = [
                    "source" => "device",
                    "id_user" => $id_user
                ];

                $alertContext = stream_context_create([
                    "http" => [
                        "header" => "Content-Type: application/json\r\n",
                        "method" => "POST",
                        "content" => json_encode($alert)
                    ]
                ]);

                file_get_contents("http://localhost:8080/public/index.php?page=alert", false, $alertContext);
                error_log("Alerta enviada por botón físico");
            }

            $lastTimestamp = $currentTimestamp;
        } else {
        echo "[" . date("Y-m-d H:i:s") . "] Error: respuesta inválida o sin timestamp\n";
    }

        sleep(20);
    }
?>