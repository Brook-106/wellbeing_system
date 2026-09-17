<?php

require_once "../includes/session.php";
require_once "../includes/db.php";


/* Only allow POST requests */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: appointments.php");
    exit();
}


/* Get appointment ID */
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header("Location: appointments.php?error=invalid");
    exit();
}


/* Check whether appointment exists */
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


/* Delete appointment */
$stmt = $conn->prepare("
    DELETE FROM appointments
    WHERE id = ?
");

$stmt->execute([$id]);


/* Return to appointment list */
header("Location: appointments.php?success=deleted");
exit();

?>
