<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];

    $fullname = trim($_POST['fullname']);
    $department = $_POST['department'];
    $class = $_POST['class'];
    $mentor = $_POST['mentor'];
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = trim($_POST['address']);

    // Update users table
    $sql = "UPDATE users
            INNER JOIN students
            ON users.id = students.user_id
            SET users.fullname = ?
            WHERE students.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$fullname, $id]);

    // Update students table
    $sql = "UPDATE students
            SET
                department_id = ?,
                class_id = ?,
                mentor_id = ?,
                phone = ?,
                gender = ?,
                dob = ?,
                address = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        $department,
        $class,
        $mentor,
        $phone,
        $gender,
        $dob,
        $address,
        $id
    ]);

    header("Location: students.php");
    exit();
}