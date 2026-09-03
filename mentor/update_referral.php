<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

$id = $_POST['id'];
$priority = $_POST['priority'];
$reason = trim($_POST['reason']);

$stmt = $conn->prepare("
UPDATE referrals
SET
priority = ?,
reason = ?
WHERE
id = ?
AND status='Pending'
");

$stmt->execute([
    $priority,
    $reason,
    $id
]);

header("Location: referrals.php");
exit();