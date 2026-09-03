<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: referrals.php");
    exit();
}

$id = $_GET['id'];

try {

    $stmt = $conn->prepare("
        DELETE FROM referrals
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: referrals.php");
    exit();

} catch(PDOException $e) {

    die("Error: " . $e->getMessage());

}
?>