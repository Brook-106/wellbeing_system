<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: students.php");
    exit();
}

$id = $_GET['id'];

$sql = "
SELECT
    s.*,
    u.fullname,
    u.email
FROM students s
JOIN users u ON s.user_id = u.id
WHERE s.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->execute([$id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);
// Load Departments
$departments = $conn->query("SELECT * FROM departments")->fetchAll(PDO::FETCH_ASSOC);

// Load Classes
$classes = $conn->query("SELECT * FROM classes")->fetchAll(PDO::FETCH_ASSOC);

// Load Mentors
$mentors = $conn->query("
SELECT mentors.id, users.fullname
FROM mentors
JOIN users ON mentors.user_id = users.id
")->fetchAll(PDO::FETCH_ASSOC);


?>
<!DOCTYPE html>
<html>

<head>

    <title>Edit Student</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3>Edit Student</h3>

        </div>

        <div class="card-body">
            <form action="update_student.php" method="POST">

    <input type="hidden" name="id" value="<?= $student['id']; ?>">

    <div class="mb-3">

        <label class="form-label">Full Name</label>

        <input
            type="text"
            name="fullname"
            class="form-control"
            value="<?= htmlspecialchars($student['fullname']); ?>">

    </div>

<div class="mb-3">

<label class="form-label">Department</label>

<select name="department_id" class="form-select">

<?php foreach($departments as $department): ?>

<option
    value="<?= $department['id']; ?>"
    <?= ($student['department_id'] == $department['id']) ? 'selected' : ''; ?>>

    <?= htmlspecialchars($department['department_name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>
<div class="mb-3">

<label class="form-label">Class</label>

<select name="class_id" class="form-select">

<?php foreach($classes as $class): ?>

<option
    value="<?= $class['id']; ?>"
    <?= ($student['class_id'] == $class['id']) ? 'selected' : ''; ?>>

    <?= htmlspecialchars($class['class_name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>
<div class="mb-3">

<label class="form-label">Mentor</label>

<select name="mentor_id" class="form-select">

<?php foreach($mentors as $mentor): ?>

<option
value="<?= $mentor['id']; ?>"
<?= ($student['mentor_id'] == $mentor['id']) ? 'selected' : ''; ?>>

<?= htmlspecialchars($mentor['fullname']); ?>

</option>

<?php endforeach; ?>

</select>

</div>
<div class="mb-3">
    <label class="form-label">Phone</label>
    <input type="text"
           name="phone"
           class="form-control"
           value="<?= htmlspecialchars($student['phone']); ?>">
</div>
<div class="mb-3">
    <label class="form-label">Gender</label>

    <select name="gender" class="form-select">

        <option value="Male"
        <?= $student['gender']=="Male" ? "selected" : ""; ?>>
        Male
        </option>

        <option value="Female"
        <?= $student['gender']=="Female" ? "selected" : ""; ?>>
        Female
        </option>

        <option value="Other"
        <?= $student['gender']=="Other" ? "selected" : ""; ?>>
        Other
        </option>

    </select>

</div>
<div class="mb-3">
    <label class="form-label">Date of Birth</label>

    <input
        type="date"
        name="dob"
        class="form-control"
        value="<?= $student['dob']; ?>">

</div>
<div class="mb-3">
    <label class="form-label">Address</label>

    <textarea
        name="address"
        class="form-control"
        rows="3"><?= htmlspecialchars($student['address']); ?></textarea>

</div>

        <button class="btn btn-success">
        Update Student
    </button>

    <a href="students.php" class="btn btn-secondary">
        Cancel
    </a>

</form>

        </div>

    </div>

</div>

</body>

</html>