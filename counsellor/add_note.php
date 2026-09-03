<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

/* Logged-in Counsellor */

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT id FROM counsellors WHERE user_id=?");
$stmt->execute([$userId]);
$counsellor = $stmt->fetch(PDO::FETCH_ASSOC);

$counsellorId = $counsellor['id'];

/* Fetch Completed Appointments */

$stmt = $conn->prepare("
SELECT
a.id,
u.fullname,
a.appointment_date
FROM appointments a
JOIN students s ON a.student_id=s.id
JOIN users u ON s.user_id=u.id
WHERE a.counsellor_id=?
ORDER BY a.appointment_date DESC
");

$stmt->execute([$counsellorId]);

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Save Note */

if(isset($_POST['save'])){

    $appointment_id = $_POST['appointment_id'];
    $note = trim($_POST['note']);

    $stmt = $conn->prepare("
    INSERT INTO counselling_notes
    (appointment_id,counsellor_id,note)
    VALUES (?,?,?)
    ");

    $stmt->execute([
        $appointment_id,
        $counsellorId,
        $note
    ]);

    header("Location: notes.php");
    exit();
}

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>Add Counselling Note</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label class="form-label">

Appointment

</label>

<select name="appointment_id" class="form-select" required>

<option value="">Select Appointment</option>

<?php foreach($appointments as $a): ?>

<option value="<?= $a['id'] ?>">

<?= htmlspecialchars($a['fullname']) ?>

(<?= date("d M Y",strtotime($a['appointment_date'])) ?>)

</option>

<?php endforeach; ?>

</select>

</div>

<div class="mb-3">

<label class="form-label">

Counselling Note

</label>

<textarea
name="note"
rows="8"
class="form-control"
required></textarea>

</div>

<button
class="btn btn-success"
name="save">

Save Note

</button>

<a
href="notes.php"
class="btn btn-secondary">

Back

</a>

</form>

</div>

</div>

<?php include "../includes/footer.php"; ?>