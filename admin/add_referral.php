<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Fetch students
$stmt = $conn->query("
SELECT s.id, u.fullname
FROM students s
JOIN users u ON s.user_id = u.id
ORDER BY u.fullname
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch mentors
$stmt = $conn->query("
SELECT m.id, u.fullname
FROM mentors m
JOIN users u ON m.user_id = u.id
ORDER BY u.fullname
");
$mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch counsellors
$stmt = $conn->query("
SELECT c.id, u.fullname
FROM counsellors c
JOIN users u ON c.user_id = u.id
ORDER BY u.fullname
");
$counsellors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">Add Referral</h2>

<div class="card shadow">
<div class="card-body">

<form action="save_referral.php" method="POST">

<div class="mb-3">
<label>Student</label>

<select name="student_id" class="form-control" required>
<option value="">Select Student</option>

<?php foreach($students as $student): ?>
<option value="<?= $student['id']; ?>">
<?= htmlspecialchars($student['fullname']); ?>
</option>
<?php endforeach; ?>

</select>

</div>

<div class="mb-3">
<label>Mentor</label>

<select name="mentor_id" class="form-control" required>
<option value="">Select Mentor</option>

<?php foreach($mentors as $mentor): ?>
<option value="<?= $mentor['id']; ?>">
<?= htmlspecialchars($mentor['fullname']); ?>
</option>
<?php endforeach; ?>

</select>

</div>

<div class="mb-3">
<label>Counsellor</label>

<select name="counsellor_id" class="form-control">
<option value="">Not Assigned</option>

<?php foreach($counsellors as $counsellor): ?>
<option value="<?= $counsellor['id']; ?>">
<?= htmlspecialchars($counsellor['fullname']); ?>
</option>
<?php endforeach; ?>

</select>

</div>

<div class="mb-3">
<label>Priority</label>

<select name="priority" class="form-control">

<option value="Low">Low</option>
<option value="Medium" selected>Medium</option>
<option value="High">High</option>

</select>

</div>

<div class="mb-3">
<label>Reason</label>

<textarea
name="reason"
class="form-control"
rows="4"
required></textarea>

</div>

<button class="btn btn-success">
Save Referral
</button>

<a href="referrals.php" class="btn btn-secondary">
Cancel
</a>

</form>

</div>
</div>

<?php include "../includes/footer.php"; ?>