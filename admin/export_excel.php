<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Referral_Report.xls");

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
?>

<table border="1">

<tr>

<th>Student</th>
<th>Mentor</th>
<th>Counsellor</th>
<th>Priority</th>
<th>Status</th>
<th>Date</th>

</tr>

<?php while($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>

<tr>

<td><?= htmlspecialchars($row['student_name']) ?></td>

<td><?= htmlspecialchars($row['mentor_name']) ?></td>

<td><?= htmlspecialchars($row['counsellor_name'] ?? 'Not Assigned') ?></td>

<td><?= htmlspecialchars($row['priority']) ?></td>

<td><?= htmlspecialchars($row['status']) ?></td>

<td><?= $row['referral_date'] ?></td>

</tr>

<?php endwhile; ?>

</table>