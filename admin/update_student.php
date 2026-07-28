<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $fullname = trim($_POST['fullname']);

    // Update users table
    $sql = "UPDATE users
            INNER JOIN students ON users.id = students.user_id
            SET users.fullname = ?
            WHERE students.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$fullname, $id]);

    header("Location: students.php");
    exit();
}
?>