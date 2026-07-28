<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Fetch students
$sql = "
SELECT
    s.id,
    u.fullname,
    s.register_no,
    u.email,
    d.department_name,
    c.class_name
FROM students s
JOIN users u ON s.user_id = u.id
LEFT JOIN departments d ON s.department_id = d.id
LEFT JOIN classes c ON s.class_id = c.id
ORDER BY s.id DESC
";

$stmt = $conn->query($sql);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Students</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
    background:#f5f7fb;
}

.sidebar{
    width:250px;
    height:100vh;
    position:fixed;
    background:#0d6efd;
    color:white;
}

.sidebar h3{
    padding:20px;
    text-align:center;
}

.sidebar a{
    display:block;
    color:white;
    text-decoration:none;
    padding:15px 25px;
}

.sidebar a:hover{
    background:#0b5ed7;
}

.content{
    margin-left:250px;
    padding:30px;
}

</style>

</head>

<body>

<div class="sidebar">

<h3>Wellbeing</h3>

<a href="dashboard.php">🏠 Dashboard</a>
<a href="students.php">👨‍🎓 Students</a>
<a href="mentors.php">👨‍🏫 Mentors</a>
<a href="counsellors.php">🩺 Counsellors</a>
<a href="departments.php">🏢 Departments</a>
<a href="reports.php">📊 Reports</a>
<a href="settings.php">⚙ Settings</a>
<a href="../logout.php">🚪 Logout</a>

</div>

<div class="content">

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>Students</h2>

<a href="add_student.php" class="btn btn-primary">
+ Add Student
</a>

</div>

<div class="card shadow">

<div class="card-body">

<table class="table table-bordered table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Name</th>
<th>Register No</th>
<th>Email</th>
<th>Department</th>
<th>Class</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php if(count($students) > 0): ?>

<?php foreach($students as $student): ?>

<tr>

<td><?= $student['id']; ?></td>

<td><?= htmlspecialchars($student['fullname']); ?></td>

<td><?= htmlspecialchars($student['register_no']); ?></td>

<td><?= htmlspecialchars($student['email']); ?></td>

<td><?= htmlspecialchars($student['department_name']); ?></td>

<td><?= htmlspecialchars($student['class_name']); ?></td>

<td>

<a href="edit_student.php?id=<?= $student['id']; ?>" class="btn btn-warning btn-sm">
Edit
</a>

<a href="delete_student.php?id=<?= $student['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this student?');">
Delete
</a>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="7" class="text-center text-danger">
No students found
</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</body>

</html>