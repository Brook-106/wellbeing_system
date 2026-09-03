<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: change_password.php");
    exit();
}

$userId = $_SESSION['user_id'];

$currentPassword = $_POST['current_password'];
$newPassword = $_POST['new_password'];
$confirmPassword = $_POST['confirm_password'];

/* Check if new passwords match */

if ($newPassword !== $confirmPassword) {
    header("Location: change_password.php?error=New passwords do not match.");
    exit();
}

/* Get current password hash */

$stmt = $conn->prepare("
SELECT password
FROM users
WHERE id = ?
");

$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: change_password.php?error=User not found.");
    exit();
}

/* Verify current password */

if (!password_verify($currentPassword, $user['password'])) {
    header("Location: change_password.php?error=Current password is incorrect.");
    exit();
}

/* Hash new password */

$newHash = password_hash($newPassword, PASSWORD_DEFAULT);

/* Update password */

$stmt = $conn->prepare("
UPDATE users
SET password = ?
WHERE id = ?
");

$stmt->execute([$newHash, $userId]);

header("Location: change_password.php?success=1");
exit();

?>