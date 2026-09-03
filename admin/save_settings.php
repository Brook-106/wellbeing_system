<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $system_name = trim($_POST['system_name']);
    $college_name = trim($_POST['college_name']);
    $admin_email = trim($_POST['admin_email']);

    $stmt = $conn->prepare("
        UPDATE settings
        SET
            system_name = ?,
            college_name = ?,
            admin_email = ?
        WHERE id = 1
    ");

    $stmt->execute([
        $system_name,
        $college_name,
        $admin_email
    ]);

    header("Location: settings.php?success=1");
    exit();
}
?>