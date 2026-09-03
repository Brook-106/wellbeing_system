<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $system_name = trim($_POST['system_name']);
    $college_name = trim($_POST['college_name']);
    $admin_email = trim($_POST['admin_email']);

    $stmt = $conn->prepare("
        UPDATE settings
        SET
            system_name = ?,
            college_name = ?,
            admin_email = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $system_name,
        $college_name,
        $admin_email,
        $id
    ]);

    header("Location: settings.php");
    exit();
}
?>