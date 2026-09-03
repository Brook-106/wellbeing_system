<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    die("Referral ID missing.");
}

$referralId = $_GET['id'];

/* Update referral status */

$stmt = $conn->prepare("
UPDATE referrals
SET status='Accepted'
WHERE id=?
");

$stmt->execute([$referralId]);

header("Location: referrals.php");
exit();