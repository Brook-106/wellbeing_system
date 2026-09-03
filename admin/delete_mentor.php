<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: mentors.php");
    exit();
}

$mentor_id = $_GET['id'];

// Find the linked user_id
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

    // Delete mentor
    $stmt = $conn->prepare("
    DELETE FROM mentors
    WHERE id = ?
    ");

    $stmt->execute([$mentor_id]);

    // Delete user
    $stmt = $conn->prepare("
    DELETE FROM users
    WHERE id = ?
    ");

    $stmt->execute([$user_id]);

    $conn->commit();

    header("Location: mentors.php");
    exit();

} catch (Exception $e) {

    $conn->rollBack();
    die($e->getMessage());
}
?>