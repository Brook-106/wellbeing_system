<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $department_name = trim($_POST['department_name']);
    $department_code = trim($_POST['department_code']);
    $hod_name = trim($_POST['hod_name']);

    $stmt = $conn->prepare("
        UPDATE departments
        SET
            department_name = ?,
            department_code = ?,
            hod_name = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $department_name,
        $department_code,
        $hod_name,
        $id
    ]);

    header("Location: departments.php");
    exit();
}
?>