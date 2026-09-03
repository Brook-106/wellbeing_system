<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: profile.php");
    exit();
}

$userId = $_SESSION['user_id'];

$fullname = trim($_POST['fullname']);
$email    = trim($_POST['email']);
$phone    = trim($_POST['phone']);

/* Basic Validation */

if (empty($fullname) || empty($email)) {
    header("Location: profile.php?error=Please fill all required fields.");
    exit();
}

/* Check if email already exists for another user */

$stmt = $conn->prepare("
SELECT id
FROM users
WHERE email = ?
AND id != ?
");

$stmt->execute([$email, $userId]);

if ($stmt->fetch()) {
    header("Location: profile.php?error=Email already exists.");
    exit();
}

try {

    $conn->beginTransaction();

    /* Update users table */

    $stmt = $conn->prepare("
    UPDATE users
    SET
        fullname = ?,
        email = ?
    WHERE id = ?
    ");

    $stmt->execute([
        $fullname,
        $email,
        $userId
    ]);

    /* Update mentors table */

    $stmt = $conn->prepare("
    UPDATE mentors
    SET phone = ?
    WHERE user_id = ?
    ");

    $stmt->execute([
        $phone,
        $userId
    ]);

    $conn->commit();

    /* Update session so header/dashboard show new name */

    $_SESSION['fullname'] = $fullname;

    header("Location: profile.php?success=1");
    exit();

} catch (Exception $e) {

    $conn->rollBack();

    die("Error: " . $e->getMessage());

}