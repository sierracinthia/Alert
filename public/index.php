<?php
    session_start();
    require_once __DIR__ . '/../controllers/AuthController.php';
    require_once __DIR__ . '/../controllers/DashboardController.php';
    require_once __DIR__ . '/../controllers/ContactController.php';
    require_once __DIR__ . '/../controllers/AlertController.php';



    $page = $_GET['page'] ?? 'login';

    switch($page) {
        case 'login':
            $auth = new AuthController();
            $auth->login();
            break;
        case 'dashboard':
            $dash = new DashboardController();
            $dash->show();
            break;
        case 'contacts':
            $contact = new ContactController();
            $contact->list();
            break;
        case 'alert':
            $alert = new AlertController();
            $alert->send();
            break;
        case 'add_contact':
        $contact = new ContactController();
        $contact->add();
        break;
        case 'delete_contact':
        $contact = new ContactController();
        $contact->delete();
        break;

        default:
            echo "Página no encontrada";

    }
?>
