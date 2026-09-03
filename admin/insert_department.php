<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $department_name = trim($_POST['department_name']);
    $department_code = trim($_POST['department_code']);
    $hod_name = trim($_POST['hod_name']);

    $stmt = $conn->prepare("
        INSERT INTO departments
        (department_name, department_code, hod_name)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([
        $department_name,
        $department_code,
        $hod_name
    ]);

    header("Location: departments.php");
    exit();
}
?>