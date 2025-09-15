<?php
//session_start();
require_once __DIR__ . '/../models/User.php';

class AuthController {
    public function login() {
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $userModel = new User();
            $user = $userModel->verifyCredentials($email, $password);

            if ($user) {
                session_regenerate_id(true); // seguridad
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_email'] = $user['email'];

                header("Location: index.php?page=dashboard");
                exit();
            } else {
                $error = "Credenciales incorrectas";
            }
        }

        require __DIR__ . '/../views/login.php';
    }

    public function logout() {
        session_unset();
        session_destroy();
        header("Location: index.php?page=login");
        exit();
    }
}
