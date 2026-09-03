<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: departments.php");
    exit();
}

$id = $_GET['id'];

try {

    $stmt = $conn->prepare("
        DELETE FROM departments
        WHERE id = ?
    ");

    $stmt->execute([$id]);

    header("Location: departments.php");
    exit();

} catch (PDOException $e) {

    die("Error: " . $e->getMessage());

}
?>