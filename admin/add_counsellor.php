<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$stmt = $conn->query("SELECT * FROM departments ORDER BY department_name");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>
<h2 class="mb-4">Add Counsellor</h2>

<div class="card shadow">
<div class="card-body">

<form action="save_counsellor.php" method="POST">

<div class="mb-3">
<label>Full Name</label>
<input type="text" name="fullname" class="form-control" required>
</div>

<div class="mb-3">
<label>Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="mb-3">
<label>Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="mb-3">
<label>Phone</label>
<input type="text" name="phone" class="form-control">
</div>
<div class="mb-3">
<label>Specialization</label>

<input
type="text"
name="specialization"
class="form-control"
placeholder="Enter Specialization"
required>

</div>

<div class="mb-3">
<label>Department</label>

<select name="department_id" class="form-control" required>

<?php foreach($departments as $department): ?>

<option value="<?= $department['id']; ?>">

<?= htmlspecialchars($department['department_name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<button class="btn btn-success">
Save Counsellor
</button>

<a href="counsellors.php" class="btn btn-secondary">
Cancel
</a>

</form>

</div>
</div>

<?php include "../includes/footer.php"; ?>