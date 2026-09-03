<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if ($_SESSION['role'] != "counsellor") {
    header("Location: ../login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Invalid Note ID");
}

$id = intval($_GET['id']);

/* Fetch Note */
$stmt = $conn->prepare("
SELECT *
FROM counsellor_notes
WHERE id = ?
");
$stmt->execute([$id]);

$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    die("Note not found.");
}

/* Update */
if (isset($_POST['update'])) {

    $title = trim($_POST['title']);
    $note_text = trim($_POST['note']);

    $stmt = $conn->prepare("
    UPDATE counsellor_notes
    SET title = ?,
        note = ?
    WHERE id = ?
    ");

    $stmt->execute([
        $title,
        $note_text,
        $id
    ]);

    header("Location: view_note.php?id=" . $note['student_id']);
    exit();
}

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<div class="container mt-4">

<div class="card shadow">

<div class="card-header bg-warning text-dark">

<h4>Edit Counselling Note</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label class="form-label">Title</label>

<input
type="text"
name="title"
class="form-control"
value="<?= htmlspecialchars($note['title']) ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">Note</label>

<textarea
name="note"
rows="8"
class="form-control"
required><?= htmlspecialchars($note['note']) ?></textarea>

</div>

<button
type="submit"
name="update"
class="btn btn-warning">

<i class="fa fa-save"></i>
Update Note

</button>

<a
href="view_note.php?id=<?= $note['student_id'] ?>"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

</div>

<?php include "../includes/footer.php"; ?>