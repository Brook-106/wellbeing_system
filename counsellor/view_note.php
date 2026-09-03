<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: notes.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("
SELECT
    cn.id,
    cn.note,
    cn.created_at,
    u.fullname,
    s.register_no,
    a.appointment_date
FROM counselling_notes cn
JOIN appointments a ON cn.appointment_id = a.id
JOIN students s ON a.student_id = s.id
JOIN users u ON s.user_id = u.id
WHERE cn.id = ?
");

$stmt->execute([$id]);

$note = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$note) {
    die("Note not found.");
}

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<div class="card shadow">

    <div class="card-header bg-info text-white">

        <h4>
            <i class="fa-solid fa-eye"></i>
            View Counselling Note
        </h4>

    </div>

    <div class="card-body">

        <table class="table table-bordered">

            <tr>
                <th width="220">Student</th>
                <td><?= htmlspecialchars($note['fullname']) ?></td>
            </tr>

            <tr>
                <th>Register Number</th>
                <td><?= htmlspecialchars($note['register_no']) ?></td>
            </tr>

            <tr>
                <th>Appointment Date</th>
                <td><?= date("d M Y", strtotime($note['appointment_date'])) ?></td>
            </tr>

            <tr>
                <th>Created On</th>
                <td><?= date("d M Y h:i A", strtotime($note['created_at'])) ?></td>
            </tr>

            <tr>
                <th>Session Note</th>
                <td style="white-space:pre-wrap;">
                    <?= htmlspecialchars($note['note']) ?>
                </td>
            </tr>

        </table>

        <a href="notes.php" class="btn btn-secondary">
            <i class="fa-solid fa-arrow-left"></i>
            Back
        </a>

        <a href="edit_note.php?id=<?= $note['id'] ?>" class="btn btn-primary">
            <i class="fa-solid fa-pen"></i>
            Edit
        </a>

    </div>

</div>

<?php include "../includes/footer.php"; ?>