<?php
require_once __DIR__ . '/../models/Contact.php';

class ContactController {

    public function add() {
   //     session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $userId = $_SESSION['user_id'];

            if ($name && $email) {
                $contactModel = new Contact();
                $contactModel->addContact($userId, $name, $email);
            }
        }

        // Redirigir al dashboard a través del front controller
        header('Location: index.php?page=dashboard');
        exit;
    }
      public function delete() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        if (isset($_GET['id'])) {
            $id_contact = intval($_GET['id']);
            $userId = $_SESSION['user_id'];

            $contactModel = new Contact();
            $contactModel->deleteContact($userId, $id_contact);
        }

        // Redirigir al dashboard
        header('Location: index.php?page=dashboard');
    }
}
