<?php

session_start();

require_once "../includes/db.php";

/*
|--------------------------------------------------------------------------
| Student Authentication
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Get Student Information
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        s.id AS student_id,
        s.register_no,
        s.phone,
        s.gender,
        s.dob,
        s.address,
        u.fullname,
        u.email
    FROM students s
    INNER JOIN users u ON s.user_id = u.id
    WHERE s.user_id = ?
    LIMIT 1
");

$stmt->execute([$userId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student profile not found.");
}

$studentId = $student['student_id'];

/*
|--------------------------------------------------------------------------
| Appointment History
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.purpose,
        a.status,
        u.fullname AS counsellor_name
    FROM appointments a
    LEFT JOIN counsellors c ON a.counsellor_id = c.id
    LEFT JOIN users u ON c.user_id = u.id
    WHERE a.student_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

$stmt->execute([$studentId]);
$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Referral History
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        r.id,
        r.reason,
        r.priority,
        r.status,
        r.referral_date,
        u.fullname AS mentor_name
    FROM referrals r
    LEFT JOIN mentors m ON r.mentor_id = m.id
    LEFT JOIN users u ON m.user_id = u.id
    WHERE r.student_id = ?
    ORDER BY r.referral_date DESC
");

$stmt->execute([$studentId]);
$referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Counts
|--------------------------------------------------------------------------
*/
$totalAppointments = count($appointments);
$totalReferrals = count($referrals);

$completedAppointments = 0;
$pendingAppointments = 0;

foreach ($appointments as $appointment) {
    if ($appointment['status'] === 'Completed') {
        $completedAppointments++;
    }

    if ($appointment['status'] === 'Pending') {
        $pendingAppointments++;
    }
}

/*
|--------------------------------------------------------------------------
| Page Header
|--------------------------------------------------------------------------
*/
include "../includes/header.php";

if (file_exists("../includes/student_sidebar.php")) {
    include "../includes/student_sidebar.php";
}

?>

<div class="container-fluid py-4">

    <!-- Page Heading -->
    <div class="mb-4">
        <h2 class="fw-bold">
            <i class="fa-solid fa-clock-rotate-left me-2"></i>
            My History
        </h2>

        <p class="text-muted mb-0">
            View your appointments and referrals history.
        </p>
    </div>

    <!-- Student Information -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">

            <div class="row">

                <div class="col-md-6 mb-3">
                    <small class="text-muted">Student Name</small>
                    <h5 class="mb-0">
                        <?= htmlspecialchars($student['fullname']); ?>
                    </h5>
                </div>

                <div class="col-md-6 mb-3">
                    <small class="text-muted">Register Number</small>
                    <h5 class="mb-0">
                        <?= htmlspecialchars($student['register_no'] ?? 'Not available'); ?>
                    </h5>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Email</small>
                    <p class="mb-0">
                        <?= htmlspecialchars($student['email']); ?>
                    </p>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Phone</small>
                    <p class="mb-0">
                        <?= htmlspecialchars($student['phone'] ?? 'Not available'); ?>
                    </p>
                </div>

            </div>

        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">
                                Total Appointments
                            </small>

                            <h2 class="fw-bold mb-0">
                                <?= $totalAppointments; ?>
                            </h2>
                        </div>

                        <div class="fs-1 text-primary">
                            <i class="fa-solid fa-calendar-check"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">
                                Completed
                            </small>

                            <h2 class="fw-bold mb-0">
                                <?= $completedAppointments; ?>
                            </h2>
                        </div>

                        <div class="fs-1 text-success">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">

                        <div>
                            <small class="text-muted">
                                Referrals
                            </small>

                            <h2 class="fw-bold mb-0">
                                <?= $totalReferrals; ?>
                            </h2>
                        </div>

                        <div class="fs-1 text-warning">
                            <i class="fa-solid fa-share-nodes"></i>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Appointment History -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fa-solid fa-calendar-days me-2"></i>
                Appointment History
            </h5>
        </div>

        <div class="card-body">

            <?php if (empty($appointments)): ?>

                <div class="text-center py-5">

                    <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>

                    <h5>No appointment history</h5>

                    <p class="text-muted">
                        You don't have any appointments yet.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

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

                            <?php foreach ($appointments as $index => $appointment): ?>

                                <?php
                                $status = $appointment['status'];

                                $statusClass = 'secondary';

                                if ($status === 'Pending') {
                                    $statusClass = 'warning';
                                } elseif ($status === 'Approved') {
                                    $statusClass = 'primary';
                                } elseif ($status === 'Completed') {
                                    $statusClass = 'success';
                                } elseif ($status === 'Rejected') {
                                    $statusClass = 'danger';
                                }
                                ?>

                                <tr>

                                    <td>
                                        <?= $index + 1; ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            "d M Y",
                                            strtotime($appointment['appointment_date'])
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            "h:i A",
                                            strtotime($appointment['appointment_time'])
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $appointment['counsellor_name'] ?? 'Not assigned'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $appointment['purpose'] ?? '—'
                                        ); ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-<?= $statusClass; ?>">
                                            <?= htmlspecialchars($status); ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

    <!-- Referral History -->
    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">

            <h5 class="mb-0 fw-bold">
                <i class="fa-solid fa-share-nodes me-2"></i>
                Referral History
            </h5>

        </div>

        <div class="card-body">

            <?php if (empty($referrals)): ?>

                <div class="text-center py-5">

                    <i class="fa-solid fa-folder-open fa-3x text-muted mb-3"></i>

                    <h5>No referral history</h5>

                    <p class="text-muted">
                        No referrals have been recorded for you.
                    </p>

                </div>

            <?php else: ?>

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Mentor</th>
                                <th>Reason</th>
                                <th>Priority</th>
                                <th>Status</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($referrals as $index => $referral): ?>

                                <?php
                                $priority = $referral['priority'];

                                $priorityClass = 'secondary';

                                if ($priority === 'Low') {
                                    $priorityClass = 'success';
                                } elseif ($priority === 'Medium') {
                                    $priorityClass = 'warning';
                                } elseif ($priority === 'High') {
                                    $priorityClass = 'danger';
                                }

                                $referralStatus = $referral['status'];

                                $referralStatusClass = 'secondary';

                                if ($referralStatus === 'Pending') {
                                    $referralStatusClass = 'warning';
                                } elseif ($referralStatus === 'Accepted') {
                                    $referralStatusClass = 'primary';
                                } elseif ($referralStatus === 'Completed') {
                                    $referralStatusClass = 'success';
                                } elseif ($referralStatus === 'Rejected') {
                                    $referralStatusClass = 'danger';
                                }
                                ?>

                                <tr>

                                    <td>
                                        <?= $index + 1; ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            "d M Y",
                                            strtotime($referral['referral_date'])
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $referral['mentor_name'] ?? 'Not assigned'
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            $referral['reason']
                                        ); ?>
                                    </td>

                                    <td>
                                        <span class="badge bg-<?= $priorityClass; ?>">
                                            <?= htmlspecialchars($priority); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <span class="badge bg-<?= $referralStatusClass; ?>">
                                            <?= htmlspecialchars($referralStatus); ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php

include "../includes/footer.php";

?>
