<?php

session_start();

require_once "../includes/db.php";

/*
|--------------------------------------------------------------------------
| Mentor Access Protection
|--------------------------------------------------------------------------
*/
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'mentor') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Get Mentor ID
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT id
    FROM mentors
    WHERE user_id = ?
    LIMIT 1
");

$stmt->execute([$userId]);

$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mentor) {
    die("Mentor profile not found.");
}

$mentorId = $mentor['id'];

/*
|--------------------------------------------------------------------------
| Get Mentor's Student Appointments
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.purpose,
        a.status,
        a.created_at,

        s.id AS student_id,
        s.register_no,

        su.fullname AS student_name,

        c.id AS counsellor_id,
        cu.fullname AS counsellor_name

    FROM appointments a

    INNER JOIN students s
        ON a.student_id = s.id

    INNER JOIN users su
        ON s.user_id = su.id

    LEFT JOIN counsellors c
        ON a.counsellor_id = c.id

    LEFT JOIN users cu
        ON c.user_id = cu.id

    WHERE s.mentor_id = ?

    ORDER BY
        a.appointment_date DESC,
        a.appointment_time DESC
");

$stmt->execute([$mentorId]);

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Appointment Counts
|--------------------------------------------------------------------------
*/
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

/*
|--------------------------------------------------------------------------
| Header + Sidebar
|--------------------------------------------------------------------------
*/
include "../includes/header.php";

if (file_exists("../includes/mentor_sidebar.php")) {
    include "../includes/mentor_sidebar.php";
}

?>

<style>

.appointments-page {
    padding: 25px;
}

.page-title {
    font-weight: 700;
    color: #1f2937;
}

.page-subtitle {
    color: #6b7280;
}

.summary-card {
    border: 0;
    border-radius: 14px;
    padding: 20px;
    color: white;
}

.summary-number {
    font-size: 28px;
    font-weight: 700;
}

.summary-label {
    font-size: 13px;
    opacity: 0.9;
}

.appointments-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
}

.card-header-custom {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    background: #ffffff;
}

.appointments-table {
    margin-bottom: 0;
}

.appointments-table th {
    font-size: 12px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.03em;
    white-space: nowrap;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.appointments-table td {
    vertical-align: middle;
    font-size: 14px;
    color: #334155;
}

.student-name {
    font-weight: 600;
    color: #1f2937;
}

.register-no {
    font-size: 12px;
    color: #94a3b8;
}

.status-badge {
    display: inline-block;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 600;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-approved {
    background: #dcfce7;
    color: #166534;
}

.status-completed {
    background: #dbeafe;
    color: #1d4ed8;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.empty-state {
    text-align: center;
    padding: 70px 20px;
}

.empty-state i {
    font-size: 48px;
    color: #94a3b8;
    margin-bottom: 15px;
}

</style>

<div class="content">

    <div class="appointments-page">

        <!-- Page Header -->
        <div class="mb-4">

            <h2 class="page-title mb-1">

                <i class="fa-solid fa-calendar-check me-2"></i>

                Student Appointments

            </h2>

            <p class="page-subtitle mb-0">

                View counselling appointments for your assigned students.

            </p>

        </div>


        <!-- Summary Cards -->
        <div class="row g-3 mb-4">

            <div class="col-sm-6 col-xl-3">

                <div class="summary-card bg-primary">

                    <div class="summary-number">
                        <?php echo $totalAppointments; ?>
                    </div>

                    <div class="summary-label">
                        Total Appointments
                    </div>

                </div>

            </div>


            <div class="col-sm-6 col-xl-3">

                <div class="summary-card bg-warning text-dark">

                    <div class="summary-number">
                        <?php echo $pendingAppointments; ?>
                    </div>

                    <div class="summary-label">
                        Pending
                    </div>

                </div>

            </div>


            <div class="col-sm-6 col-xl-3">

                <div class="summary-card bg-success">

                    <div class="summary-number">
                        <?php echo $approvedAppointments; ?>
                    </div>

                    <div class="summary-label">
                        Approved
                    </div>

                </div>

            </div>


            <div class="col-sm-6 col-xl-3">

                <div class="summary-card bg-info">

                    <div class="summary-number">
                        <?php echo $completedAppointments; ?>
                    </div>

                    <div class="summary-label">
                        Completed
                    </div>

                </div>

            </div>

        </div>


        <!-- Appointments Table -->
        <div class="appointments-card">

            <div class="card-header-custom">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <h5 class="fw-bold mb-1">

                            <i class="fa-solid fa-calendar-days text-primary me-2"></i>

                            Appointment History

                        </h5>

                        <small class="text-muted">

                            Counselling appointments associated with your
                            assigned students.

                        </small>

                    </div>

                    <span class="badge bg-light text-dark">

                        <?php echo $totalAppointments; ?> records

                    </span>

                </div>

            </div>


            <?php if ($totalAppointments > 0): ?>

                <div class="table-responsive">

                    <table class="table appointments-table">

                        <thead>

                            <tr>

                                <th>Student</th>

                                <th>Date</th>

                                <th>Time</th>

                                <th>Counsellor</th>

                                <th>Purpose</th>

                                <th>Status</th>

                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($appointments as $appointment): ?>

                                <tr>

                                    <!-- Student -->
                                    <td>

                                        <div class="student-name">

                                            <?php
                                            echo htmlspecialchars(
                                                $appointment['student_name']
                                            );
                                            ?>

                                        </div>

                                        <div class="register-no">

                                            <?php
                                            echo htmlspecialchars(
                                                $appointment['register_no']
                                                    ?: 'No register number'
                                            );
                                            ?>

                                        </div>

                                    </td>


                                    <!-- Date -->
                                    <td>

                                        <i class="fa-regular fa-calendar me-1 text-muted"></i>

                                        <?php
                                        echo date(
                                            "d M Y",
                                            strtotime(
                                                $appointment['appointment_date']
                                            )
                                        );
                                        ?>

                                    </td>


                                    <!-- Time -->
                                    <td>

                                        <i class="fa-regular fa-clock me-1 text-muted"></i>

                                        <?php
                                        echo date(
                                            "h:i A",
                                            strtotime(
                                                $appointment['appointment_time']
                                            )
                                        );
                                        ?>

                                    </td>


                                    <!-- Counsellor -->
                                    <td>

                                        <?php if (!empty($appointment['counsellor_name'])): ?>

                                            <i class="fa-solid fa-user-doctor me-1 text-primary"></i>

                                            <?php
                                            echo htmlspecialchars(
                                                $appointment['counsellor_name']
                                            );
                                            ?>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Not assigned
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- Purpose -->
                                    <td>

                                        <?php if (!empty($appointment['purpose'])): ?>

                                            <?php
                                            $purpose = $appointment['purpose'];

                                            if (strlen($purpose) > 45) {
                                                $purpose = substr($purpose, 0, 45) . '...';
                                            }

                                            echo htmlspecialchars($purpose);
                                            ?>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                Not specified
                                            </span>

                                        <?php endif; ?>

                                    </td>


                                    <!-- Status -->
                                    <td>

                                        <?php

                                        $statusClass = 'status-pending';

                                        switch ($appointment['status']) {

                                            case 'Approved':
                                                $statusClass = 'status-approved';
                                                break;

                                            case 'Completed':
                                                $statusClass = 'status-completed';
                                                break;

                                            case 'Rejected':
                                                $statusClass = 'status-rejected';
                                                break;
                                        }

                                        ?>

                                        <span class="status-badge <?php
                                            echo $statusClass;
                                        ?>">

                                            <?php
                                            echo htmlspecialchars(
                                                $appointment['status']
                                            );
                                            ?>

                                        </span>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php else: ?>

                <div class="empty-state">

                    <i class="fa-regular fa-calendar-xmark"></i>

                    <h5 class="fw-bold">
                        No Appointments
                    </h5>

                    <p class="text-muted mb-0">

                        None of your assigned students have counselling
                        appointments yet.

                    </p>

                </div>

            <?php endif; ?>

        </div>

    </div>

</div>

<?php

include "../includes/footer.php";

?>
