<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $mentor_id = $_POST['id'];
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $department_id = $_POST['department_id'];

    // Get user_id from mentor
    $stmt = $conn->prepare("
        SELECT user_id
        FROM mentors
        WHERE id = ?
    ");

    $stmt->execute([$mentor_id]);

    $mentor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$mentor) {
        header("Location: mentors.php");
        exit();
    }

    $user_id = $mentor['user_id'];

    try {

        $conn->beginTransaction();

        // Update users table
        $stmt = $conn->prepare("
            UPDATE users
            SET fullname = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $fullname,
            $user_id
        ]);

        // Update mentors table
        $stmt = $conn->prepare("
            UPDATE mentors
            SET
                phone = ?,
                department_id = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $phone,
            $department_id,
            $mentor_id
        ]);

        $conn->commit();

        header("Location: mentors.php");
        exit();

    } catch (Exception $e) {

        $conn->rollBack();

        die($e->getMessage());
    }
}
?>