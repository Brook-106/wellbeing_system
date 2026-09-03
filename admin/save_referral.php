<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $student_id = $_POST['student_id'];
    $mentor_id = $_POST['mentor_id'];

    // Counsellor is optional
    $counsellor_id = !empty($_POST['counsellor_id']) ? $_POST['counsellor_id'] : null;

    $priority = $_POST['priority'];
    $reason = trim($_POST['reason']);

    try {

        $stmt = $conn->prepare("
            INSERT INTO referrals
            (
                student_id,
                mentor_id,
                counsellor_id,
                reason,
                priority
            )
            VALUES
            (?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $student_id,
            $mentor_id,
            $counsellor_id,
            $reason,
            $priority
        ]);

        header("Location: referrals.php");
        exit();

    } catch(PDOException $e) {

        die("Error: " . $e->getMessage());

    }
}
?>