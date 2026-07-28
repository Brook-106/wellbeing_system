<?php

session_start();

require_once "db.php";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $sql = "SELECT * FROM users WHERE email = ? AND status = 'Active'";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);

    if ($stmt->rowCount() == 1) {

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['role'] = $user['role'];

            switch ($user['role']) {

                case "admin":
                    header("Location: ../admin/dashboard.php");
                    break;

                case "mentor":
                    header("Location: ../mentor/dashboard.php");
                    break;

                case "counsellor":
                    header("Location: ../counsellor/dashboard.php");
                    break;

                case "student":
                    header("Location: ../student/dashboard.php");
                    break;
            }

            exit();

        } else {

            die("Incorrect Password");

        }

    } else {

        die("User Not Found");

    }

} else {

    header("Location: ../login.php");
}