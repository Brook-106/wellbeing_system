<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');

require_once "../includes/session.php";
require_once "../includes/db.php";

/* =========================================================
   LOGGED-IN STUDENT
========================================================= */

$userId = $_SESSION['user_id'] ?? null;

if (!$userId) {
    die("User session not found.");
}

/* Get student record */

$stmt = $conn->prepare("
    SELECT
        s.id,
        u.fullname,
        u.email,
        s.register_no
    FROM students s
    INNER JOIN users u
        ON s.user_id = u.id
    WHERE s.user_id = ?
");

$stmt->execute([$userId]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student record not found.");
}

$studentId = (int)$student['id'];


/* =========================================================
   GET STUDENT APPOINTMENTS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.purpose,
        a.status,
        u.fullname AS counsellor_name
    FROM appointments a
    INNER JOIN counsellors c
        ON a.counsellor_id = c.id
    INNER JOIN users u
        ON c.user_id = u.id
    WHERE a.student_id = ?
    ORDER BY
        a.appointment_date DESC,
        a.appointment_time DESC
");

$stmt->execute([$studentId]);

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   APPOINTMENT COUNTS
========================================================= */

$totalAppointments = count($appointments);

$pendingAppointments = 0;
$approvedAppointments = 0;
$completedAppointments = 0;
$rejectedAppointments = 0;

foreach ($appointments as $appointment) {

    switch ($appointment['status']) {

        case 'Pending':
            $pendingAppointments++;
            break;

        case 'Approved':
            $approvedAppointments++;
            break;

        case 'Completed':
            $completedAppointments++;
            break;

        case 'Rejected':
            $rejectedAppointments++;
            break;
    }
}


/* =========================================================
   LAYOUT
========================================================= */

include "../includes/header.php";
include "../includes/student_sidebar.php";

?>

<div class="container-fluid">

    <!-- ================= PAGE HEADER ================= -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h2 class="fw-bold mb-2">

                        <i class="fa-solid fa-calendar-check text-primary"></i>

                        My Appointments

                    </h2>

                    <p class="text-muted mb-0">

                        View your counselling appointments and their current status.

                    </p>

                </div>

                <div class="col-md-4 text-center">

                    <i class="fa-solid fa-calendar-days fa-5x text-primary"></i>

                </div>

            </div>

        </div>

    </div>


    <!-- ================= SUMMARY CARDS ================= -->

    <div class="row g-3 mb-4">

        <!-- Total -->

        <div class="col-md-3">

            <div class="card shadow-sm border-start border-primary border-4 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="text-muted mb-1">
                                Total
                            </h6>

                            <h2 class="fw-bold mb-0">
                                <?= $totalAppointments ?>
                            </h2>

                        </div>

                        <i class="fa-solid fa-calendar-check fa-2x text-primary"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Pending -->

        <div class="col-md-3">

            <div class="card shadow-sm border-start border-warning border-4 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="text-muted mb-1">
                                Pending
                            </h6>

                            <h2 class="fw-bold mb-0">
                                <?= $pendingAppointments ?>
                            </h2>

                        </div>

                        <i class="fa-solid fa-clock fa-2x text-warning"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Approved -->

        <div class="col-md-3">

            <div class="card shadow-sm border-start border-success border-4 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="text-muted mb-1">
                                Approved
                            </h6>

                            <h2 class="fw-bold mb-0">
                                <?= $approvedAppointments ?>
                            </h2>

                        </div>

                        <i class="fa-solid fa-circle-check fa-2x text-success"></i>

                    </div>

                </div>

            </div>

        </div>


        <!-- Completed -->

        <div class="col-md-3">

            <div class="card shadow-sm border-start border-info border-4 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <h6 class="text-muted mb-1">
                                Completed
                            </h6>

                            <h2 class="fw-bold mb-0">
                                <?= $completedAppointments ?>
                            </h2>

                        </div>

                        <i class="fa-solid fa-check-double fa-2x text-info"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ================= APPOINTMENTS TABLE ================= -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">

                <i class="fa-solid fa-calendar-check"></i>

                Appointment History

            </h5>

        </div>


        <div class="card-body">

            <?php if ($appointments): ?>

                <div class="table-responsive">

                    <table class="table table-hover table-bordered align-middle mb-0">

                        <thead class="table-light">

                            <tr>

                                <th>#</th>

                                <th>Date</th>

                                <th>Time</th>

                                <th>Counsellor</th>

                                <th>Purpose</th>

                                <th>Status</th>

                            </tr>

                        </thead>


                        <tbody>

                            <?php foreach ($appointments as $index => $row): ?>

                                <tr>

                                    <td>
                                        <?= $index + 1 ?>
                                    </td>


                                    <td>

                                        <i class="fa-solid fa-calendar-day text-primary me-1"></i>

                                        <?= date(
                                            "d M Y",
                                            strtotime($row['appointment_date'])
                                        ) ?>

                                    </td>


                                    <td>

                                        <i class="fa-solid fa-clock text-secondary me-1"></i>

                                        <?= date(
                                            "h:i A",
                                            strtotime($row['appointment_time'])
                                        ) ?>

                                    </td>


                                    <td>

                                        <i class="fa-solid fa-user-doctor text-success me-1"></i>

                                        <?= htmlspecialchars(
                                            $row['counsellor_name']
                                        ) ?>

                                    </td>


                                    <td>

                                        <?= htmlspecialchars(
                                            $row['purpose'] ?? 'Not specified'
                                        ) ?>

                                    </td>


                                    <td>

                                        <?php if ($row['status'] === 'Pending'): ?>

                                            <span class="badge bg-warning text-dark">

                                                <i class="fa-solid fa-clock"></i>

                                                Pending

                                            </span>


                                        <?php elseif ($row['status'] === 'Approved'): ?>

                                            <span class="badge bg-primary">

                                                <i class="fa-solid fa-circle-check"></i>

                                                Approved

                                            </span>


                                        <?php elseif ($row['status'] === 'Completed'): ?>

                                            <span class="badge bg-success">

                                                <i class="fa-solid fa-check-double"></i>

                                                Completed

                                            </span>


                                        <?php elseif ($row['status'] === 'Rejected'): ?>

                                            <span class="badge bg-danger">

                                                <i class="fa-solid fa-circle-xmark"></i>

                                                Rejected

                                            </span>


                                        <?php else: ?>

                                            <span class="badge bg-secondary">

                                                <?= htmlspecialchars(
                                                    $row['status']
                                                ) ?>

                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>


            <?php else: ?>

                <div class="text-center text-muted py-5">

                    <i class="fa-solid fa-calendar-xmark fa-4x mb-3"></i>

                    <h5>
                        No appointments found
                    </h5>

                    <p class="mb-0">
                        You currently have no counselling appointments.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ================= BACK BUTTON ================= -->

    <div class="mb-4">

        <a href="dashboard.php" class="btn btn-secondary">

            <i class="fa-solid fa-arrow-left"></i>

            Back to Dashboard

        </a>

    </div>

</div>


<?php include "../includes/footer.php"; ?>
