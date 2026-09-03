<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if(!isset($_GET['id'])){
    header("Location: students.php");
    exit();
}

$studentId = $_GET['id'];

$stmt = $conn->prepare("
SELECT
    s.*,
    u.fullname,
    u.email,
    u.profile_image,
    d.department_name,
    c.class_name,
    mu.fullname AS mentor_name
FROM students s

JOIN users u
ON s.user_id = u.id

LEFT JOIN departments d
ON s.department_id = d.id

LEFT JOIN classes c
ON s.class_id = c.id

LEFT JOIN mentors m
ON s.mentor_id = m.id

LEFT JOIN users mu
ON m.user_id = mu.id

WHERE s.id=?
");

$stmt->execute([$studentId]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$student){
    die("Student not found.");
}

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h3>

<i class="fa-solid fa-user-graduate"></i>

Student Profile

</h3>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3 text-center">

<?php if(!empty($student['profile_image'])): ?>

<img
src="../uploads/profile/<?= $student['profile_image']; ?>"
class="img-fluid rounded-circle border"
width="170">

<?php else: ?>

<i class="fa-solid fa-circle-user text-primary"
style="font-size:170px;"></i>

<?php endif; ?>

</div>

<div class="col-md-9">

<table class="table table-bordered">

<tr>

<th width="220">Full Name</th>

<td><?= htmlspecialchars($student['fullname']) ?></td>

</tr>

<tr>

<th>Email</th>

<td><?= htmlspecialchars($student['email']) ?></td>

</tr>

<tr>

<th>Register Number</th>

<td><?= htmlspecialchars($student['register_no']) ?></td>

</tr>

<tr>

<th>Phone</th>

<td><?= htmlspecialchars($student['phone']) ?></td>

</tr>

<tr>

<th>Gender</th>

<td><?= htmlspecialchars($student['gender']) ?></td>

</tr>

<tr>

<th>Date of Birth</th>

<td><?= htmlspecialchars($student['dob']) ?></td>

</tr>

<tr>

<th>Department</th>

<td><?= htmlspecialchars($student['department_name']) ?></td>

</tr>

<tr>

<th>Class</th>

<td><?= htmlspecialchars($student['class_name']) ?></td>

</tr>

<tr>

<th>Mentor</th>

<td><?= htmlspecialchars($student['mentor_name']) ?></td>

</tr>

<tr>

<th>Address</th>

<td><?= nl2br(htmlspecialchars($student['address'])) ?></td>

</tr>

</table>

<a href="create_referral.php"
class="btn btn-warning">

<i class="fa-solid fa-share-nodes"></i>

Create Referral

</a>

<a href="students.php"
class="btn btn-secondary">

Back

</a>

</div>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>