<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = trim($_POST['phone']);
    $specialization = trim($_POST['specialization']);
    $department_id = $_POST['department_id'];

    // For now we'll keep specialization empty
    $specialization = "";

    try {

        $conn->beginTransaction();

        // Insert into users table
        $stmt = $conn->prepare("
            INSERT INTO users (fullname, email, password, role, status)
            VALUES (?, ?, ?, 'counsellor', 'Active')
        ");

        $stmt->execute([
            $fullname,
            $email,
            $password
        ]);

        $user_id = $conn->lastInsertId();

        // Insert into counsellors table
        $stmt = $conn->prepare("
            INSERT INTO counsellors(user_id, department_id, phone, specialization)
            VALUES(?, ?, ?, ?)
        ");

        $stmt->execute([
            $user_id,
            $department_id,
            $specialization,
            $phone
        ]);

        $conn->commit();

        header("Location: counsellors.php");
        exit();

    } catch (PDOException $e) {

        $conn->rollBack();

        die("Error: " . $e->getMessage());
    }
}
?>