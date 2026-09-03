<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if(!isset($_GET['id'])){
    die("Follow-up ID missing.");
}

$id = $_GET['id'];

$stmt = $conn->prepare("
DELETE FROM followups
WHERE id=?
");

$stmt->execute([$id]);

header("Location: followups.php?deleted=1");
exit();

?>