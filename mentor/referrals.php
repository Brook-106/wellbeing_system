<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

$userId = $_SESSION['user_id'];

/* Get Mentor ID */

$stmt = $conn->prepare("
SELECT id
FROM mentors
WHERE user_id = ?
");

$stmt->execute([$userId]);

$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mentor) {
    die("Mentor not found.");
}

$mentorId = $mentor['id'];

/* Fetch Referrals */

$stmt = $conn->prepare("
SELECT
    r.id,
    u.fullname,
    s.register_no,
    r.priority,
    r.status,
    r.reason,
    r.referral_date
FROM referrals r

JOIN students s
ON r.student_id = s.id

JOIN users u
ON s.user_id = u.id

WHERE r.mentor_id = ?

ORDER BY r.referral_date DESC
");

$stmt->execute([$mentorId]);

$referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>
        <i class="fa-solid fa-list-check"></i>
        My Referrals
    </h2>

    <a href="create_referral.php" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i>
        New Referral
    </a>

</div>

<?php if(isset($_GET['success'])): ?>

<div class="alert alert-success">
    Referral submitted successfully.
</div>

<?php endif; ?>

<div class="card shadow-sm">

    <div class="card-body">

        <table class="table table-hover table-bordered">

            <thead class="table-primary">

                <tr>

                    <th>ID</th>
                    <th>Student</th>
                    <th>Register No</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Reason</th>
                    <th width="180">Action</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($referrals) > 0): ?>

                <?php foreach($referrals as $row): ?>

                <tr>

                    <td><?= $row['id'] ?></td>

                    <td><?= htmlspecialchars($row['fullname']) ?></td>

                    <td><?= htmlspecialchars($row['register_no']) ?></td>

                    <td>

                        <?php

                        if($row['priority']=="High"){
                            echo '<span class="badge bg-danger">High</span>';
                        }elseif($row['priority']=="Medium"){
                            echo '<span class="badge bg-warning text-dark">Medium</span>';
                        }else{
                            echo '<span class="badge bg-success">Low</span>';
                        }

                        ?>

                    </td>

                    <td>

                        <?php

                        if($row['status']=="Pending"){
                            echo '<span class="badge bg-warning text-dark">Pending</span>';
                        }elseif($row['status']=="Accepted"){
                            echo '<span class="badge bg-primary">Accepted</span>';
                        }elseif($row['status']=="Rejected"){
                            echo '<span class="badge bg-danger">Rejected</span>';
                        }else{
                            echo '<span class="badge bg-success">Completed</span>';
                        }

                        ?>

                    </td>

                    <td>
                        <?= date("d M Y", strtotime($row['referral_date'])) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars(substr($row['reason'],0,60)) ?>
                        <?= strlen($row['reason']) > 60 ? "..." : "" ?>
                    </td>

                    <td>

                        <a href="referral_details.php?id=<?= $row['id'] ?>"
                           class="btn btn-primary btn-sm">

                            <i class="fa-solid fa-eye"></i>

                            View

                        </a>

                        <?php if($row['status']=="Pending"): ?>

                        <a href="edit_referral.php?id=<?= $row['id'] ?>"
                           class="btn btn-warning btn-sm">

                            <i class="fa-solid fa-pen"></i>

                            Edit

                        </a>

                        <?php endif; ?>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="8" class="text-center text-danger">

                        No referrals found.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "../includes/footer.php"; ?>