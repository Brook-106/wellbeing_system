<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

/* Logged-in Counsellor */

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT id
FROM counsellors
WHERE user_id = ?
");

$stmt->execute([$userId]);

$counsellor = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$counsellor){
    die("Counsellor record not found.");
}

$counsellorId = $counsellor['id'];

/* Fetch Notes */

$stmt = $conn->prepare("
SELECT

cn.id,

u.fullname,

a.appointment_date,

cn.note,

cn.created_at

FROM counselling_notes cn

JOIN appointments a
ON cn.appointment_id = a.id

JOIN students s
ON a.student_id = s.id

JOIN users u
ON s.user_id = u.id

WHERE cn.counsellor_id = ?

ORDER BY cn.created_at DESC
");

$stmt->execute([$counsellorId]);

$notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<h2 class="mb-4">

    <i class="fa-solid fa-note-sticky"></i>

    Counselling Notes

</h2>

<div class="card shadow">

    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            All Session Notes

        </h5>

        <a href="add_note.php" class="btn btn-light btn-sm">

            <i class="fa-solid fa-plus"></i>

            Add Note

        </a>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover table-bordered align-middle">

                <thead class="table-primary">

                    <tr>

                        <th>ID</th>

                        <th>Student</th>

                        <th>Appointment</th>

                        <th>Session Note</th>

                        <th>Created</th>

                        <th width="160">Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php if(count($notes)>0): ?>

                    <?php foreach($notes as $row): ?>

                    <tr>

                        <td><?= $row['id'] ?></td>

                        <td><?= htmlspecialchars($row['fullname']) ?></td>

                        <td><?= date("d M Y",strtotime($row['appointment_date'])) ?></td>

                        <td>

                            <?= nl2br(htmlspecialchars(substr($row['note'],0,80))) ?>

                            <?= strlen($row['note'])>80 ? "..." : "" ?>

                        </td>

                        <td>

                            <?= date("d M Y",strtotime($row['created_at'])) ?>

                        </td>

                        <td>

                            <a href="view_note.php?id=<?= $row['id'] ?>"

                               class="btn btn-info btn-sm">

                                <i class="fa fa-eye"></i>

                            </a>

                            <a href="edit_note.php?id=<?= $row['id'] ?>"

                               class="btn btn-primary btn-sm">

                                <i class="fa fa-edit"></i>

                            </a>

                            <a href="delete_note.php?id=<?= $row['id'] ?>"

                               class="btn btn-danger btn-sm"

                               onclick="return confirm('Delete this note?')">

                                <i class="fa fa-trash"></i>

                            </a>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" class="text-center text-danger">

                            No counselling notes available.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>