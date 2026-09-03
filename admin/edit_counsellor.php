<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: counsellors.php");
    exit();
}

$id = $_GET['id'];

// Get counsellor details
$stmt = $conn->prepare("
SELECT
    c.*,
    u.fullname,
    u.email
FROM counsellors c
JOIN users u ON c.user_id = u.id
WHERE c.id = ?
");
$stmt->execute([$id]);
$counsellor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$counsellor) {
    header("Location: counsellors.php");
    exit();
}

// Get departments
$stmt = $conn->query("SELECT * FROM departments ORDER BY department_name");
$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">Edit Counsellor</h2>

<div class="card shadow">
<div class="card-body">

<form action="update_counsellor.php" method="POST">

<input type="hidden" name="id" value="<?= $counsellor['id']; ?>">

<div class="mb-3">
<label>Full Name</label>
<input
type="text"
name="fullname"
class="form-control"
value="<?= htmlspecialchars($counsellor['fullname']); ?>"
required>
</div>

<div class="mb-3">
<label>Phone</label>
<input
type="text"
name="phone"
class="form-control"
value="<?= htmlspecialchars($counsellor['phone']); ?>">
</div>

<div class="mb-3">
<label>Specialization</label>
<input
type="text"
name="specialization"
class="form-control"
value="<?= htmlspecialchars($counsellor['specialization']); ?>">
</div>

<div class="mb-3">
<label>Department</label>

<select name="department_id" class="form-control">

<?php foreach($departments as $department): ?>

<option
value="<?= $department['id']; ?>"
<?= ($department['id'] == $counsellor['department_id']) ? "selected" : ""; ?>>

<?= htmlspecialchars($department['department_name']); ?>

</option>

<?php endforeach; ?>

</select>

</div>

<button class="btn btn-success">Update Counsellor</button>

<a href="counsellors.php" class="btn btn-secondary">Cancel</a>

</form>

</div>
</div>

<?php include "../includes/footer.php"; ?>