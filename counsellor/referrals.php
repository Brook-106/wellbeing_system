<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

$userId = $_SESSION['user_id'];

/* Logged-in Counsellor */

$stmt = $conn->prepare("
SELECT id
FROM counsellors
WHERE user_id=?
");

$stmt->execute([$userId]);

$counsellor = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$counsellor){
    die("Counsellor not found.");
}

$counsellorId = $counsellor['id'];

/* Fetch Referrals */

$stmt = $conn->prepare("
SELECT

r.id,
u.fullname,
s.register_no,
r.reason,
r.priority,
r.status,
r.referral_date

FROM referrals r

JOIN students s
ON r.student_id=s.id

JOIN users u
ON s.user_id=u.id

WHERE r.counsellor_id=?

ORDER BY r.referral_date DESC
");

$stmt->execute([$counsellorId]);

$referrals=$stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";

?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fa-solid fa-list-check"></i>

My Referrals

</h4>

</div>

<div class="card-body">

<div class="table-responsive">

<table class="table table-hover table-bordered">

<thead class="table-primary">

<tr>

<th>ID</th>

<th>Student</th>

<th>Register No</th>

<th>Priority</th>

<th>Reason</th>

<th>Status</th>

<th>Date</th>

<th width="260">Action</th>

</tr>

</thead>

<tbody>

<?php if(count($referrals)>0): ?>

<?php foreach($referrals as $row): ?>

<tr>

<td><?= $row['id'] ?></td>

<td><?= htmlspecialchars($row['fullname']) ?></td>

<td><?= htmlspecialchars($row['register_no']) ?></td>

<td>

<?php

if($row['priority']=="High"){

echo '<span class="badge bg-danger">High</span>';

}elseif($row['priority']=="Medium"){

echo '<span class="badge bg-warning text-dark">Medium</span>';

}else{

echo '<span class="badge bg-success">Low</span>';

}

?>

</td>

<td><?= htmlspecialchars($row['reason']) ?></td>

<td>

<?php

switch($row['status']){

case "Pending":

echo '<span class="badge bg-warning text-dark">Pending</span>';

break;

case "Accepted":

echo '<span class="badge bg-primary">Accepted</span>';

break;

case "Rejected":

echo '<span class="badge bg-danger">Rejected</span>';

break;

default:

echo '<span class="badge bg-success">Completed</span>';

}

?>

</td>

<td><?= date("d M Y",strtotime($row['referral_date'])) ?></td>

<td>

<?php if($row['status']=="Pending"): ?>

<a href="accept_referral.php?id=<?= $row['id'] ?>"
class="btn btn-success btn-sm">

<i class="fa fa-check"></i>

Accept

</a>

<a href="reject_referral.php?id=<?= $row['id'] ?>"
class="btn btn-danger btn-sm">

<i class="fa fa-times"></i>

Reject

</a>

<?php elseif($row['status']=="Accepted"): ?>

<a href="create_appointment.php?referral_id=<?= $row['id'] ?>"
class="btn btn-primary btn-sm">

<i class="fa-solid fa-calendar-plus"></i>

Schedule Appointment

</a>

<?php elseif($row['status']=="Completed"): ?>

<span class="badge bg-success">

Completed

</span>

<?php else: ?>

-

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="8" class="text-center text-danger">

No referrals found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>