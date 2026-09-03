<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit();
}

$id = $_GET['id'];

// Find the linked user_id
$stmt = $conn->prepare("
SELECT user_id
FROM students
WHERE id = ?
");

$stmt->execute([$id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    header("Location: students.php");
    exit();
}

$user_id = $student['user_id'];
// Delete student record
$stmt = $conn->prepare("
DELETE FROM students
WHERE id = ?
");

$stmt->execute([$id]);
// Delete linked user account
$stmt = $conn->prepare("
DELETE FROM users
WHERE id = ?
");

$stmt->execute([$user_id]);
header("Location: students.php");
exit();