<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $student_id = $_POST['student_id'];
    $mentor_id = $_POST['mentor_id'];

    $counsellor_id = !empty($_POST['counsellor_id'])
        ? $_POST['counsellor_id']
        : null;

    $priority = $_POST['priority'];
    $status = $_POST['status'];
    $reason = trim($_POST['reason']);

    // New field
    $counsellor_notes = trim($_POST['counsellor_notes']);

    try {

        $stmt = $conn->prepare("
            UPDATE referrals
            SET
                student_id = ?,
                mentor_id = ?,
                counsellor_id = ?,
                priority = ?,
                status = ?,
                reason = ?,
                counsellor_notes = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $student_id,
            $mentor_id,
            $counsellor_id,
            $priority,
            $status,
            $reason,
            $counsellor_notes,
            $id
        ]);

        header("Location: referrals.php");
        exit();

    } catch(PDOException $e) {

        die("Error: " . $e->getMessage());

    }

}
?>