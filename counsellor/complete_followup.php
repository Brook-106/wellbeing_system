<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if(!isset($_GET['id'])){
    die("Follow-up ID missing.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("
UPDATE followups
SET status='Completed'
WHERE id=?
");

$stmt->execute([$id]);

header("Location: followups.php?completed=1");
exit();

?>