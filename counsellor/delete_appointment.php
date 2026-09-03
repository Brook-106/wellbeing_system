<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: appointments.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("
DELETE FROM appointments
WHERE id=?
");

$stmt->execute([$id]);

header("Location: appointments.php");
exit();

?>