<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: referrals.php");
    exit();
}

$referralId = $_GET['id'];

$stmt = $conn->prepare("
SELECT
    r.*,
    u.fullname AS student_name,
    s.register_no,
    cu.fullname AS counsellor_name
FROM referrals r

JOIN students s
ON r.student_id=s.id

JOIN users u
ON s.user_id=u.id

LEFT JOIN counsellors c
ON r.counsellor_id=c.id

LEFT JOIN users cu
ON c.user_id=cu.id

WHERE r.id=?
");

$stmt->execute([$referralId]);

$referral=$stmt->fetch(PDO::FETCH_ASSOC);

if(!$referral){
    die("Referral not found.");
}

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>

<i class="fa-solid fa-file-lines"></i>

Referral Details

</h3>

</div>

<div class="card-body">

<table class="table table-bordered">

<tr>

<th width="220">Student</th>

<td><?= htmlspecialchars($referral['student_name']) ?></td>

</tr>

<tr>

<th>Register Number</th>

<td><?= htmlspecialchars($referral['register_no']) ?></td>

</tr>

<tr>

<th>Priority</th>

<td><?= htmlspecialchars($referral['priority']) ?></td>

</tr>

<tr>

<th>Status</th>

<td><?= htmlspecialchars($referral['status']) ?></td>

</tr>

<tr>

<th>Referral Date</th>

<td><?= date("d M Y h:i A",strtotime($referral['referral_date'])) ?></td>

</tr>

<tr>

<th>Reason</th>

<td><?= nl2br(htmlspecialchars($referral['reason'])) ?></td>

</tr>

<tr>

<th>Counsellor</th>

<td>

<?= $referral['counsellor_name']
?? '<span class="text-muted">Not Assigned</span>' ?>

</td>

</tr>

<tr>

<th>Counsellor Notes</th>

<td>

<?= !empty($referral['counsellor_notes'])
? nl2br(htmlspecialchars($referral['counsellor_notes']))
: '<span class="text-muted">No Notes</span>' ?>

</td>

</tr>

</table>

<a href="referrals.php"
class="btn btn-secondary">

<i class="fa fa-arrow-left"></i>

Back

</a>

<?php if($referral['status']=="Pending"): ?>

<a href="edit_referral.php?id=<?= $referral['id'] ?>"
class="btn btn-warning">

<i class="fa fa-pen"></i>

Edit Referral

</a>

<?php endif; ?>

</div>

</div>

<?php include "../includes/footer.php"; ?>