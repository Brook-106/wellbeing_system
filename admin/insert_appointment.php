<?php

require_once "../includes/session.php";
require_once "../includes/db.php";


/* Only allow POST requests */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

    header("Location: appointments.php");
    exit();

}


/* Get submitted values */
$student_id = filter_input(INPUT_POST, 'student_id', FILTER_VALIDATE_INT);
$counsellor_id = filter_input(INPUT_POST, 'counsellor_id', FILTER_VALIDATE_INT);

$appointment_date = trim($_POST['appointment_date'] ?? '');
$appointment_time = trim($_POST['appointment_time'] ?? '');
$purpose = trim($_POST['purpose'] ?? '');
$status = trim($_POST['status'] ?? 'Pending');


/* Validate required fields */
if (
    !$student_id ||
    !$counsellor_id ||
    $appointment_date === '' ||
    $appointment_time === ''
) {

    header("Location: add_appointment.php?error=missing");
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

    header("Location: add_appointment.php?error=status");
    exit();

}


/* Validate date */
$dateObject = DateTime::createFromFormat('Y-m-d', $appointment_date);

if (
    !$dateObject ||
    $dateObject->format('Y-m-d') !== $appointment_date
) {

    header("Location: add_appointment.php?error=date");
    exit();

}


/* Validate time */
$timeObject = DateTime::createFromFormat('H:i', $appointment_time);

if (
    !$timeObject ||
    $timeObject->format('H:i') !== $appointment_time
) {

    header("Location: add_appointment.php?error=time");
    exit();

}


/* Make sure student exists */
$stmt = $conn->prepare("
    SELECT id
    FROM students
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$student_id]);

if (!$stmt->fetch()) {

    header("Location: add_appointment.php?error=student");
    exit();

}


/* Make sure counsellor exists */
$stmt = $conn->prepare("
    SELECT id
    FROM counsellors
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$counsellor_id]);

if (!$stmt->fetch()) {

    header("Location: add_appointment.php?error=counsellor");
    exit();

}


/* Insert appointment */
$stmt = $conn->prepare("
    INSERT INTO appointments
    (
        student_id,
        counsellor_id,
        appointment_date,
        appointment_time,
        purpose,
        status
    )
    VALUES
    (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $student_id,
    $counsellor_id,
    $appointment_date,
    $appointment_time,
    $purpose !== '' ? $purpose : null,
    $status
]);


/* Redirect back to appointments */
header("Location: appointments.php?success=added");
exit();

?>
