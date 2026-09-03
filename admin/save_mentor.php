<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = trim($_POST['phone']);
    $department_id = $_POST['department_id'];

    try {

        // Start transaction
        $conn->beginTransaction();

        // Insert into users table
        $sql = "INSERT INTO users (fullname, email, password, role, status)
                VALUES (?, ?, ?, 'mentor', 'Active')";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $fullname,
            $email,
            $password
        ]);

        // Get new user id
        $user_id = $conn->lastInsertId();

        // Insert into mentors table
        $sql = "INSERT INTO mentors (user_id, department_id, phone)
                VALUES (?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $user_id,
            $department_id,
            $phone
        ]);

        // Commit transaction
        $conn->commit();

        header("Location: mentors.php");
        exit();

    } catch (Exception $e) {

        $conn->rollBack();

        die($e->getMessage());
    }
}
?>