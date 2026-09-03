<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Fetch referrals
$sql = "
SELECT
    r.id,
    su.fullname AS student_name,
    mu.fullname AS mentor_name,
    cu.fullname AS counsellor_name,
    r.priority,
    r.status,
    r.referral_date
FROM referrals r

JOIN students s ON r.student_id = s.id
JOIN users su ON s.user_id = su.id

JOIN mentors m ON r.mentor_id = m.id
JOIN users mu ON m.user_id = mu.id

LEFT JOIN counsellors c ON r.counsellor_id = c.id
LEFT JOIN users cu ON c.user_id = cu.id

ORDER BY r.id DESC
";

$stmt = $conn->query($sql);
$referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Referrals</h2>

    <a href="add_referral.php" class="btn btn-primary">
        + Add Referral
    </a>

</div>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Student</th>
<th>Mentor</th>
<th>Counsellor</th>
<th>Priority</th>
<th>Status</th>
<th>Date</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php if(count($referrals)>0): ?>

<?php foreach($referrals as $r): ?>

<tr>

<td><?= $r['id']; ?></td>

<td><?= htmlspecialchars($r['student_name']); ?></td>

<td><?= htmlspecialchars($r['mentor_name']); ?></td>

<td><?= htmlspecialchars($r['counsellor_name'] ?? 'Not Assigned'); ?></td>

<td><?= htmlspecialchars($r['priority']); ?></td>

<td><?= htmlspecialchars($r['status']); ?></td>

<td><?= $r['referral_date']; ?></td>

<td>

<a href="edit_referral.php?id=<?= $r['id']; ?>" class="btn btn-warning btn-sm">
Edit
</a>

<a href="delete_referral.php?id=<?= $r['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this referral?');">
Delete
</a>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="8" class="text-center text-danger">
No referrals found
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<?php include "../includes/footer.php"; ?>