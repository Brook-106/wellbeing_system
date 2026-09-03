<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Get departments
$departments = $conn->query("SELECT * FROM departments ORDER BY department_name")
                    ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>

<title>Add Mentor</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">
<h3>Add Mentor</h3>
</div>

<div class="card-body">

<form action="save_mentor.php" method="POST">

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
<label>Department</label>

<select name="department_id" class="form-select" required>

<option value="">Select Department</option>

<?php foreach($departments as $department): ?>

<option value="<?= $department['id']; ?>">
    <?= htmlspecialchars($department['department_name']); ?>
</option>

<?php endforeach; ?>

</select>

</div>

<button class="btn btn-success">
Save Mentor
</button>

<a href="mentors.php" class="btn btn-secondary">
Cancel
</a>

</form>

</div>

</div>

</div>

</body>

</html>