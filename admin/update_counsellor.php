<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];
    $fullname = trim($_POST['fullname']);
    $phone = trim($_POST['phone']);
    $specialization = trim($_POST['specialization']);
    $department_id = $_POST['department_id'];

    try {

        $conn->beginTransaction();

        // Get user_id from counsellors table
        $stmt = $conn->prepare("
            SELECT user_id
            FROM counsellors
            WHERE id = ?
        ");

        $stmt->execute([$id]);

        $counsellor = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$counsellor) {
            die("Counsellor not found.");
        }

        $user_id = $counsellor['user_id'];

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

        // Update counsellors table
        $stmt = $conn->prepare("
            UPDATE counsellors
            SET
                phone = ?,
                specialization = ?,
                department_id = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $phone,
            $specialization,
            $department_id,
            $id
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