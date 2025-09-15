<?php
function conectar() {
    try {
        // Datos de conexión para Docker
        $host     = getenv("DB_HOST") ?: "mysql";       // <- nombre del servicio MySQL
        $dbname   = getenv("DB_NAME") ?: "alertas_db";
        $usuario  = getenv("DB_USER") ?: "admin";
        $password = getenv("DB_PASSWORD") ?: "admin123";
        $dbport   = getenv("DB_PORT") ?: "3306";

        // Opciones PDO
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        ];

        // Crear conexión
        $pdo = new PDO(
            "mysql:host=$host;port=$dbport;dbname=$dbname;charset=utf8mb4",
            $usuario,
            $password,
            $options
        );

        return $pdo;
    } catch (PDOException $e) {
        error_log("Error de conexión a la base de datos: " . $e->getMessage());
        die("No se pudo conectar a la base de datos.");
    }
}

function cerrarConexion($conexion) {
    $conexion = null;
}
?>
