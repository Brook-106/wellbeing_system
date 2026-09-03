<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: create_referral.php");
    exit();
}

$userId = $_SESSION['user_id'];

/* Get Mentor ID */

$stmt = $conn->prepare("
SELECT id
FROM mentors
WHERE user_id = ?
");

$stmt->execute([$userId]);

$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mentor) {
    die("Mentor not found.");
}

$mentorId = $mentor['id'];

/* Get Form Data */

$studentId = $_POST['student_id'];
$counsellorId = $_POST['counsellor_id'];
$priority = $_POST['priority'];
$reason = trim($_POST['reason']);

/* Validate */

if (
    empty($studentId) ||
    empty($counsellorId) ||
    empty($reason)
) {
    die("Please complete all required fields.");
}

/* Insert Referral */

$stmt = $conn->prepare("
INSERT INTO referrals
(
    student_id,
    mentor_id,
    counsellor_id,
    reason,
    priority,
    status
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    ?,
    'Pending'
)
");

$stmt->execute([
    $studentId,
    $mentorId,
    $counsellorId,
    $reason,
    $priority
]);

header("Location: referrals.php?success=1");
exit();