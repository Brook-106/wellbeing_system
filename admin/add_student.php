<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Load departments
$departments = $conn->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);

// Load classes
$classes = $conn->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC);

// Load mentors
$mentors = $conn->query("
    SELECT mentors.id, users.fullname
    FROM mentors
    JOIN users ON mentors.user_id = users.id
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Add Student</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>Add New Student</h3>

</div>

<div class="card-body">

<form action="save_student.php" method="POST">

<div class="row">

<div class="col-md-6 mb-3">

<label class="form-label">Full Name</label>

<input type="text" name="fullname" class="form-control" required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Register Number</label>

<input type="text" name="register_no" class="form-control" required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Email</label>

<input type="email" name="email" class="form-control" required>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Phone</label>

<input type="text" name="phone" class="form-control">

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Gender</label>

<select name="gender" class="form-select">

<option>Male</option>
<option>Female</option>
<option>Other</option>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Date of Birth</label>

<input type="date" name="dob" class="form-control">

</div>

<div class="col-12 mb-3">

<label class="form-label">Address</label>

<textarea name="address" class="form-control"></textarea>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">Department</label>

<select name="department_id" class="form-select">

<?php foreach($departments as $department){ ?>

<option value="<?= $department['id']; ?>">
<?= $department['department_name']; ?>
</option>

<?php } ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">Class</label>

<select name="class_id" class="form-select">

<?php foreach($classes as $class){ ?>

<option value="<?= $class['id']; ?>">
<?= $class['class_name']; ?>
</option>

<?php } ?>

</select>

</div>

<div class="col-md-4 mb-3">

<label class="form-label">Mentor</label>

<select name="mentor_id" class="form-select">

<?php foreach($mentors as $mentor){ ?>

<option value="<?= $mentor['id']; ?>">
<?= $mentor['fullname']; ?>
</option>

<?php } ?>

</select>

</div>

<div class="col-md-6 mb-3">

<label class="form-label">Password</label>

<input type="password" name="password" class="form-control" required>

</div>

</div>

<button type="submit" class="btn btn-success">

Save Student

</button>

<a href="students.php" class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

</div>

</body>

</html>