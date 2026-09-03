<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$sql = "
SELECT
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
ORDER BY r.referral_date DESC
";

$stmt = $conn->query($sql);
$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Referral Report</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    margin:30px;
}

</style>

</head>

<body>

<h2 class="text-center mb-4">
Student Wellbeing Management System
</h2>

<h4 class="text-center mb-4">
Referral Report
</h4>

<table class="table table-bordered">

<thead class="table-dark">

<tr>

<th>Student</th>
<th>Mentor</th>
<th>Counsellor</th>
<th>Priority</th>
<th>Status</th>
<th>Date</th>

</tr>

</thead>

<tbody>

<?php foreach($reports as $row): ?>

<tr>

<td><?= htmlspecialchars($row['student_name']) ?></td>

<td><?= htmlspecialchars($row['mentor_name']) ?></td>

<td><?= htmlspecialchars($row['counsellor_name'] ?? 'Not Assigned') ?></td>

<td><?= htmlspecialchars($row['priority']) ?></td>

<td><?= htmlspecialchars($row['status']) ?></td>

<td><?= date("d M Y", strtotime($row['referral_date'])) ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<script>

window.print();

</script>

</body>

</html>