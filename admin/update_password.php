<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Check if new passwords match
    if ($new_password !== $confirm_password) {
        header("Location: change_password.php?error=" . urlencode("New passwords do not match."));
        exit();
    }

    // Get current password
    $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        header("Location: change_password.php?error=" . urlencode("User not found."));
        exit();
    }

    // Verify current password
    if (!password_verify($current_password, $user['password'])) {
        header("Location: change_password.php?error=" . urlencode("Current password is incorrect."));
        exit();
    }

    // Hash new password
    $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);

    // Update password
    $stmt = $conn->prepare("
        UPDATE users
        SET password = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $hashedPassword,
        $_SESSION['user_id']
    ]);

    // Redirect back to Settings page
    header("Location: settings.php?password=success");
    exit();
}
?>