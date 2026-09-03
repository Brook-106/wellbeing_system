<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: mentors.php");
    exit();
}

$id = $_GET['id'];

// Get mentor details
$sql = "
SELECT
    m.*,
    u.fullname,
    u.email
FROM mentors m
JOIN users u ON m.user_id = u.id
WHERE m.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);

$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mentor) {
    header("Location: mentors.php");
    exit();
}

// Get departments
$departments = $conn->query("SELECT * FROM departments ORDER BY department_name")
                    ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>

<head>

<title>Edit Mentor</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-primary text-white">
<h3>Edit Mentor</h3>
</div>

<div class="card-body">

<form action="update_mentor.php" method="POST">

<input type="hidden" name="id" value="<?= $mentor['id']; ?>">

<div class="mb-3">
<label>Full Name</label>
<input
type="text"
name="fullname"
class="form-control"
value="<?= htmlspecialchars($mentor['fullname']); ?>"
required>
</div>

<div class="mb-3">
<label>Phone</label>
<input
type="text"
name="phone"
class="form-control"
value="<?= htmlspecialchars($mentor['phone']); ?>">
</div>

<div class="mb-3">
<label>Department</label>

<select name="department_id" class="form-select">

<?php foreach($departments as $department): ?>

<option
value="<?= $department['id']; ?>"
<?= ($department['id'] == $mentor['department_id']) ? 'selected' : ''; ?>>

<?= htmlspecialchars($department['department_name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<button class="btn btn-success">
Update Mentor
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