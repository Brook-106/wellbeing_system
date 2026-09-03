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

/* Fetch Appointments */

$stmt = $conn->prepare("
SELECT
    a.id,
    u.fullname,
    s.register_no,
    a.appointment_date,
    a.appointment_time,
    a.purpose,
    a.status
FROM appointments a

JOIN students s
ON a.student_id = s.id

JOIN users u
ON s.user_id = u.id

WHERE a.counsellor_id = ?

ORDER BY a.appointment_date ASC,
         a.appointment_time ASC
");

$stmt->execute([$counsellorId]);

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<h2 class="mb-4">

    <i class="fa-solid fa-calendar-check"></i>

    My Appointments

</h2>

<div class="card shadow">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            Appointment List

        </h5>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table table-hover table-bordered align-middle">

                <thead class="table-primary">

                    <tr>

                        <th>ID</th>

                        <th>Student</th>

                        <th>Register No</th>

                        <th>Date</th>

                        <th>Time</th>

                        <th>Purpose</th>

                        <th>Status</th>

                        <th width="170">Action</th>

                    </tr>

                </thead>

                <tbody>

                <?php if(count($appointments)>0): ?>

                    <?php foreach($appointments as $row): ?>

                    <tr>

                        <td><?= $row['id'] ?></td>

                        <td><?= htmlspecialchars($row['fullname']) ?></td>

                        <td><?= htmlspecialchars($row['register_no']) ?></td>

                        <td><?= date("d M Y",strtotime($row['appointment_date'])) ?></td>

                        <td><?= date("h:i A",strtotime($row['appointment_time'])) ?></td>

                        <td><?= htmlspecialchars($row['purpose']) ?></td>

                        <td>

                            <?php

                            if($row['status']=="Pending"){

                                echo '<span class="badge bg-warning text-dark">Pending</span>';

                            }elseif($row['status']=="Approved"){

                                echo '<span class="badge bg-primary">Approved</span>';

                            }elseif($row['status']=="Completed"){

                                echo '<span class="badge bg-success">Completed</span>';

                            }else{

                                echo '<span class="badge bg-danger">Rejected</span>';

                            }

                            ?>

                        </td>

                        <td>

                            <a href="edit_appointment.php?id=<?= $row['id'] ?>"

                               class="btn btn-sm btn-primary">

                                <i class="fa fa-edit"></i>

                            </a>

                            <a href="complete_appointment.php?id=<?= $row['id'] ?>"

                               class="btn btn-sm btn-success">

                                <i class="fa fa-check"></i>

                            </a>

                            <a href="delete_appointment.php?id=<?= $row['id'] ?>"

                               class="btn btn-sm btn-danger"

                               onclick="return confirm('Delete this appointment?')">

                                <i class="fa fa-trash"></i>

                            </a>

                        </td>

                    </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="8" class="text-center text-danger">

                            No appointments found.

                        </td>

                    </tr>

                <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>