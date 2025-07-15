<?php
require_once '../../config/db.php';
require_once '../../controllers/DeanController.php';

$controller = new DeanController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['offering_id'])) {
    $controller->submitOffering($_POST['offering_id']);
} else {
    echo "Invalid request.";
}
?>
