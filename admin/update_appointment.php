<?php

require_once "../includes/session.php";
require_once "../includes/db.php";


/* Only allow POST */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: appointments.php");
    exit();
}


/* Get values */
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
$counsellor_id = filter_input(INPUT_POST, 'counsellor_id', FILTER_VALIDATE_INT);

$appointment_date = trim($_POST['appointment_date'] ?? '');
$appointment_time = trim($_POST['appointment_time'] ?? '');
$purpose = trim($_POST['purpose'] ?? '');
$status = trim($_POST['status'] ?? 'Pending');


/* Basic validation */
if (
    !$id ||
    !$student_id ||
    !$counsellor_id ||
    $appointment_date === '' ||
    $appointment_time === ''
) {

    header("Location: appointments.php?error=missing");
    exit();

}


/* Validate status */
$allowedStatuses = [
    'Pending',
    'Approved',
    'Rejected',
    'Completed'
];

if (!in_array($status, $allowedStatuses, true)) {

    header("Location: appointments.php?error=status");
    exit();

}


/* Validate date */
$dateObject = DateTime::createFromFormat(
    'Y-m-d',
    $appointment_date
);

if (
    !$dateObject ||
    $dateObject->format('Y-m-d') !== $appointment_date
) {

    header("Location: appointments.php?error=date");
    exit();

}


/* Validate time */
$timeObject = DateTime::createFromFormat(
    'H:i',
    $appointment_time
);

if (
    !$timeObject ||
    $timeObject->format('H:i') !== $appointment_time
) {

    header("Location: appointments.php?error=time");
    exit();

}


/* Check appointment exists */
$stmt = $conn->prepare("
    SELECT id
    FROM appointments
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

if (!$stmt->fetch()) {

    header("Location: appointments.php?error=notfound");
    exit();

}


/* Check student exists */
$stmt = $conn->prepare("
    SELECT id
    FROM students
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$student_id]);

if (!$stmt->fetch()) {

    header("Location: appointments.php?error=student");
    exit();

}


/* Check counsellor exists */
$stmt = $conn->prepare("
    SELECT id
    FROM counsellors
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$counsellor_id]);

if (!$stmt->fetch()) {

    header("Location: appointments.php?error=counsellor");
    exit();

}


/* Update appointment */
$stmt = $conn->prepare("
    UPDATE appointments
    SET
        student_id = ?,
        counsellor_id = ?,
        appointment_date = ?,
        appointment_time = ?,
        purpose = ?,
        status = ?
    WHERE id = ?
");

$stmt->execute([
    $student_id,
    $counsellor_id,
    $appointment_date,
    $appointment_time,
    $purpose !== '' ? $purpose : null,
    $status,
    $id
]);


/* Return to appointments */
header("Location: appointments.php?success=updated");
exit();

?>
