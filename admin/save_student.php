<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $register_no = trim($_POST["register_no"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $gender = $_POST["gender"];
    $dob = $_POST["dob"];
    $address = trim($_POST["address"]);
    $department_id = $_POST["department_id"];
    $class_id = $_POST["class_id"];
    $mentor_id = $_POST["mentor_id"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    try {

        // Insert into users table
        $sql = "INSERT INTO users (fullname, email, password, role, status)
                VALUES (?, ?, ?, 'student', 'Active')";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$fullname, $email, $password]);

        // Get inserted user ID
        $user_id = $conn->lastInsertId();

        // Insert into students table
        $sql = "INSERT INTO students
                (user_id, register_no, department_id, class_id, mentor_id, phone, gender, dob, address)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $user_id,
            $register_no,
            $department_id,
            $class_id,
            $mentor_id,
            $phone,
            $gender,
            $dob,
            $address
        ]);

        header("Location: students.php?success=1");
        exit();

    } catch (PDOException $e) {

        die("Error: " . $e->getMessage());

    }

} else {

    header("Location: add_student.php");
    exit();
}