<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: counsellors.php");
    exit();
}

$id = $_GET['id'];

try {

    $conn->beginTransaction();

    // Get the related user_id
    $stmt = $conn->prepare("
        SELECT user_id
        FROM counsellors
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    $counsellor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$counsellor) {
        throw new Exception("Counsellor not found.");
    }

    $user_id = $counsellor['user_id'];

    // Delete from counsellors table
    $stmt = $conn->prepare("
        DELETE FROM counsellors
        WHERE id = ?
    ");
    $stmt->execute([$id]);

    // Delete from users table
    $stmt = $conn->prepare("
        DELETE FROM users
        WHERE id = ?
    ");
    $stmt->execute([$user_id]);

    $conn->commit();

    header("Location: counsellors.php");
    exit();

} catch (Exception $e) {

    $conn->rollBack();

    die("Error: " . $e->getMessage());
}
?>