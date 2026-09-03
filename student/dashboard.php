<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

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
        s.user_id,
        s.register_no,
        s.department_id,
        s.class_id,
        s.mentor_id,
        s.phone,
        s.gender,
        s.dob,
        s.address,
        u.fullname,
        u.email,
        u.profile_image
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
   REFERRAL STATISTICS
========================================================= */

$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM referrals
    WHERE student_id = ?
");
$stmt->execute([$studentId]);
$totalReferrals = (int)$stmt->fetchColumn();


$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM referrals
    WHERE student_id = ?
    AND status = 'Pending'
");
$stmt->execute([$studentId]);
$pendingReferrals = (int)$stmt->fetchColumn();


$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM referrals
    WHERE student_id = ?
    AND status = 'Accepted'
");
$stmt->execute([$studentId]);
$acceptedReferrals = (int)$stmt->fetchColumn();


$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM referrals
    WHERE student_id = ?
    AND status = 'Completed'
");
$stmt->execute([$studentId]);
$completedReferrals = (int)$stmt->fetchColumn();


/* =========================================================
   APPOINTMENT STATISTICS
========================================================= */

$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM appointments
    WHERE student_id = ?
");
$stmt->execute([$studentId]);
$totalAppointments = (int)$stmt->fetchColumn();


$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM appointments
    WHERE student_id = ?
    AND status = 'Pending'
");
$stmt->execute([$studentId]);
$pendingAppointments = (int)$stmt->fetchColumn();


$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM appointments
    WHERE student_id = ?
    AND status = 'Approved'
");
$stmt->execute([$studentId]);
$approvedAppointments = (int)$stmt->fetchColumn();


$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM appointments
    WHERE student_id = ?
    AND status = 'Completed'
");
$stmt->execute([$studentId]);
$completedAppointments = (int)$stmt->fetchColumn();


/* =========================================================
   UPCOMING APPOINTMENT
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
    AND a.status = 'Approved'
    AND a.appointment_date >= CURDATE()
    ORDER BY a.appointment_date ASC, a.appointment_time ASC
    LIMIT 1
");
$stmt->execute([$studentId]);

$upcomingAppointment = $stmt->fetch(PDO::FETCH_ASSOC);


/* =========================================================
   RECENT REFERRALS
========================================================= */

$stmt = $conn->prepare("
    SELECT
        r.id,
        r.reason,
        r.priority,
        r.status,
        r.referral_date,
        u.fullname AS counsellor_name
    FROM referrals r
    LEFT JOIN counsellors c
        ON r.counsellor_id = c.id
    LEFT JOIN users u
        ON c.user_id = u.id
    WHERE r.student_id = ?
    ORDER BY r.referral_date DESC
    LIMIT 5
");
$stmt->execute([$studentId]);

$recentReferrals = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   RECENT APPOINTMENTS
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
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
    LIMIT 5
");
$stmt->execute([$studentId]);

$recentAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================================================
   LAYOUT
========================================================= */

include "../includes/header.php";
if (file_exists("../includes/student_sidebar.php")) {\n    include "../includes/student_sidebar.php";\n}\n
?>

<div class="container-fluid">

    <!-- ================= PAGE HEADER ================= -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-body">

            <div class="row align-items-center">

                <div class="col-md-8">

                    <h2 class="fw-bold mb-2">
                        👋 Welcome Back,
                        <?= htmlspecialchars($student['fullname']) ?>
                    </h2>

                    <h5 class="text-primary mb-2">
                        Student Dashboard
                    </h5>

                    <p class="text-muted mb-0">
                        <?= date("l, d F Y") ?>
                    </p>

                </div>

                <div class="col-md-4 text-center">

                    <?php if (!empty($student['profile_image'])): ?>

                        <img
                            src="../<?= htmlspecialchars($student['profile_image']) ?>"
                            alt="Profile"
                            class="rounded-circle"
                            style="width:100px;height:100px;object-fit:cover;"
                        >

                    <?php else: ?>

                        <i class="fa-solid fa-user-graduate fa-5x text-primary"></i>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>


    <!-- ================= STUDENT INFORMATION ================= -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                <i class="fa-solid fa-id-card"></i>
                My Information
            </h5>

        </div>

        <div class="card-body">

            <div class="row">

                <div class="col-md-4 mb-3">
                    <strong>Register No</strong>
                    <div>
                        <?= htmlspecialchars($student['register_no'] ?? 'Not available') ?>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Email</strong>
                    <div>
                        <?= htmlspecialchars($student['email']) ?>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Phone</strong>
                    <div>
                        <?= htmlspecialchars($student['phone'] ?? 'Not available') ?>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Gender</strong>
                    <div>
                        <?= htmlspecialchars($student['gender'] ?? 'Not available') ?>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Department ID</strong>
                    <div>
                        <?= htmlspecialchars($student['department_id'] ?? 'Not assigned') ?>
                    </div>
                </div>

                <div class="col-md-4 mb-3">
                    <strong>Class ID</strong>
                    <div>
                        <?= htmlspecialchars($student['class_id'] ?? 'Not assigned') ?>
                    </div>
                </div>

            </div>

        </div>

    </div>


    <!-- ================= STATISTICS ================= -->

    <div class="row">

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card shadow-sm border-start border-primary border-5 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>
                            <h6 class="text-muted">My Referrals</h6>
                            <h2 class="fw-bold"><?= $totalReferrals ?></h2>
                        </div>

                        <i class="fa-solid fa-share-nodes fa-2x text-primary"></i>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card shadow-sm border-start border-success border-5 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>
                            <h6 class="text-muted">Appointments</h6>
                            <h2 class="fw-bold"><?= $totalAppointments ?></h2>
                        </div>

                        <i class="fa-solid fa-calendar-check fa-2x text-success"></i>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card shadow-sm border-start border-info border-5 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>
                            <h6 class="text-muted">Approved</h6>
                            <h2 class="fw-bold"><?= $approvedAppointments ?></h2>
                        </div>

                        <i class="fa-solid fa-circle-check fa-2x text-info"></i>

                    </div>

                </div>

            </div>

        </div>


        <div class="col-lg-3 col-md-6 mb-4">

            <div class="card shadow-sm border-start border-warning border-5 h-100">

                <div class="card-body">

                    <div class="d-flex justify-content-between">

                        <div>
                            <h6 class="text-muted">Completed</h6>
                            <h2 class="fw-bold"><?= $completedAppointments ?></h2>
                        </div>

                        <i class="fa-solid fa-check-double fa-2x text-warning"></i>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <!-- ================= UPCOMING APPOINTMENT ================= -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-success text-white">

            <h5 class="mb-0">
                <i class="fa-solid fa-calendar-day"></i>
                Upcoming Appointment
            </h5>

        </div>

        <div class="card-body">

            <?php if ($upcomingAppointment): ?>

                <div class="row align-items-center">

                    <div class="col-md-2 text-center">

                        <i class="fa-solid fa-calendar-check fa-3x text-success"></i>

                    </div>

                    <div class="col-md-10">

                        <h5 class="fw-bold">
                            <?= date(
                                "d M Y",
                                strtotime($upcomingAppointment['appointment_date'])
                            ) ?>

                            at

                            <?= date(
                                "h:i A",
                                strtotime($upcomingAppointment['appointment_time'])
                            ) ?>
                        </h5>

                        <p class="mb-1">
                            <strong>Counsellor:</strong>
                            <?= htmlspecialchars(
                                $upcomingAppointment['counsellor_name']
                            ) ?>
                        </p>

                        <p class="mb-0">
                            <strong>Purpose:</strong>
                            <?= htmlspecialchars(
                                $upcomingAppointment['purpose'] ?? 'Not specified'
                            ) ?>
                        </p>

                    </div>

                </div>

            <?php else: ?>

                <div class="text-center text-muted py-4">

                    <i class="fa-solid fa-calendar-xmark fa-3x mb-3"></i>

                    <p class="mb-0">
                        No upcoming approved appointments.
                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>


    <!-- ================= QUICK ACTIONS ================= -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-primary text-white">

            <h5 class="mb-0">
                <i class="fa-solid fa-bolt"></i>
                Quick Actions
            </h5>

        </div>

        <div class="card-body">

            <div class="row g-3">

                <div class="col-md-3">

                    <a
                        href="appointments.php"
                        class="btn btn-primary w-100 py-3"
                    >
                        <i class="fa-solid fa-calendar-check d-block mb-2"></i>
                        My Appointments
                    </a>

                </div>

                <div class="col-md-3">

                    <a
                        href="history.php"
                        class="btn btn-success w-100 py-3"
                    >
                        <i class="fa-solid fa-clock-rotate-left d-block mb-2"></i>
                        My History
                    </a>

                </div>

                <div class="col-md-3">

                    <a
                        href="feedback.php"
                        class="btn btn-warning w-100 py-3"
                    >
                        <i class="fa-solid fa-comment-dots d-block mb-2"></i>
                        Feedback
                    </a>

                </div>

                <div class="col-md-3">

                    <a
                        href="resources.php"
                        class="btn btn-info w-100 py-3"
                    >
                        <i class="fa-solid fa-book-open d-block mb-2"></i>
                        Resources
                    </a>

                </div>

            </div>

        </div>

    </div>


    <!-- ================= RECENT REFERRALS ================= -->

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-info text-white">

            <h5 class="mb-0">
                <i class="fa-solid fa-share-nodes"></i>
                Recent Referrals
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Reason</th>
                            <th>Priority</th>
                            <th>Counsellor</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if (count($recentReferrals) > 0): ?>

                        <?php foreach ($recentReferrals as $row): ?>

                            <tr>

                                <td>
                                    <?= htmlspecialchars($row['reason']) ?>
                                </td>

                                <td>

                                    <?php if ($row['priority'] === 'High'): ?>

                                        <span class="badge bg-danger">
                                            High
                                        </span>

                                    <?php elseif ($row['priority'] === 'Medium'): ?>

                                        <span class="badge bg-warning text-dark">
                                            Medium
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-success">
                                            Low
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row['counsellor_name'] ?? 'Not assigned'
                                    ) ?>
                                </td>

                                <td>

                                    <?php if ($row['status'] === 'Pending'): ?>

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    <?php elseif ($row['status'] === 'Accepted'): ?>

                                        <span class="badge bg-primary">
                                            Accepted
                                        </span>

                                    <?php elseif ($row['status'] === 'Completed'): ?>

                                        <span class="badge bg-success">
                                            Completed
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Rejected
                                        </span>

                                    <?php endif; ?>

                                </td>

                                <td>
                                    <?= date(
                                        "d M Y",
                                        strtotime($row['referral_date'])
                                    ) ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="5"
                                class="text-center text-muted"
                            >
                                No referrals found.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>


    <!-- ================= RECENT APPOINTMENTS ================= -->

    <div class="card shadow-sm mb-4">

        <div class="card-header bg-secondary text-white">

            <h5 class="mb-0">
                <i class="fa-solid fa-calendar-check"></i>
                Recent Appointments
            </h5>

        </div>

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-hover table-bordered align-middle">

                    <thead class="table-light">

                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Counsellor</th>
                            <th>Purpose</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                    <?php if (count($recentAppointments) > 0): ?>

                        <?php foreach ($recentAppointments as $row): ?>

                            <tr>

                                <td>
                                    <?= date(
                                        "d M Y",
                                        strtotime($row['appointment_date'])
                                    ) ?>
                                </td>

                                <td>
                                    <?= date(
                                        "h:i A",
                                        strtotime($row['appointment_time'])
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row['counsellor_name']
                                    ) ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars(
                                        $row['purpose'] ?? ''
                                    ) ?>
                                </td>

                                <td>

                                    <?php if ($row['status'] === 'Pending'): ?>

                                        <span class="badge bg-warning text-dark">
                                            Pending
                                        </span>

                                    <?php elseif ($row['status'] === 'Approved'): ?>

                                        <span class="badge bg-primary">
                                            Approved
                                        </span>

                                    <?php elseif ($row['status'] === 'Completed'): ?>

                                        <span class="badge bg-success">
                                            Completed
                                        </span>

                                    <?php else: ?>

                                        <span class="badge bg-danger">
                                            Rejected
                                        </span>

                                    <?php endif; ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>

                            <td
                                colspan="5"
                                class="text-center text-muted"
                            >
                                No appointments found.
                            </td>

                        </tr>

                    <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>
