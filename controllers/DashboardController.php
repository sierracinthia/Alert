<?php
require_once __DIR__ . '/../models/Contact.php';
require_once __DIR__ . '/../models/Location.php';

class DashboardController {

    public function show() {
       // session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $contactModel = new Contact();
        $contacts = $contactModel->getByUser($_SESSION['user_id']);

        $locationModel = new Location();
        $alerts = $locationModel->getAlertsByUser($_SESSION['user_id']);

        // Pasamos datos a la vista
        require '../views/dashboard.php';
    }
}
