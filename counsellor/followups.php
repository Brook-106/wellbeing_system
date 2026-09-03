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

if (!$counsellor) {
    die("Counsellor not found.");
}

$counsellorId = $counsellor['id'];

/* Fetch Follow-ups */

$stmt = $conn->prepare("
SELECT

f.id,

a.id AS appointment_id,

u.fullname,

s.register_no,

f.followup_date,

f.followup_time,

f.remarks,

f.status

FROM followups f

JOIN appointments a
ON f.appointment_id = a.id

JOIN students s
ON a.student_id = s.id

JOIN users u
ON s.user_id = u.id

WHERE a.counsellor_id = ?

ORDER BY
f.followup_date ASC,
f.followup_time ASC
");

$stmt->execute([$counsellorId]);

$followups = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";

?>

<div class="card shadow">

    <div class="card-header bg-primary text-white">

        <div class="d-flex justify-content-between align-items-center">

            <h4>

                <i class="fa-solid fa-user-clock"></i>

                Follow-ups

            </h4>

            <a href="add_followup.php" class="btn btn-light">

                <i class="fa-solid fa-plus"></i>

                New Follow-up

            </a>

        </div>

    </div>

    <div class="card-body">

        <table class="table table-hover table-bordered">

            <thead class="table-primary">

                <tr>

                    <th>ID</th>

                    <th>Student</th>

                    <th>Register No</th>

                    <th>Date</th>

                    <th>Time</th>

                    <th>Status</th>

                    <th>Remarks</th>

                    <th width="180">Action</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($followups)>0): ?>

                <?php foreach($followups as $row): ?>

                <tr>

                    <td><?= $row['id'] ?></td>

                    <td><?= htmlspecialchars($row['fullname']) ?></td>

                    <td><?= htmlspecialchars($row['register_no']) ?></td>

                    <td><?= date("d M Y",strtotime($row['followup_date'])) ?></td>

                    <td><?= date("h:i A",strtotime($row['followup_time'])) ?></td>

                    <td>

                    <?php

                    if($row['status']=="Pending"){

                        echo '<span class="badge bg-warning text-dark">Pending</span>';

                    }else{

                        echo '<span class="badge bg-success">Completed</span>';

                    }

                    ?>

                    </td>

                    <td><?= htmlspecialchars($row['remarks']) ?></td>

                    <td>

                        <a
                        href="edit_followup.php?id=<?= $row['id'] ?>"
                        class="btn btn-sm btn-primary">

                        <i class="fa fa-edit"></i>

                        </a>

                        <a
                        href="complete_followup.php?id=<?= $row['id'] ?>"
                        class="btn btn-sm btn-success">

                        <i class="fa fa-check"></i>

                        </a>

                        <a
                        href="delete_followup.php?id=<?= $row['id'] ?>"
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Delete Follow-up?')">

                        <i class="fa fa-trash"></i>

                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="8" class="text-center text-danger">

                        No follow-ups found.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "../includes/footer.php"; ?>