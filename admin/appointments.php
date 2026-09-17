<?php
session_start();
require_once "../includes/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

/* Fetch appointments */
$stmt = $conn->query("
    SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.purpose,
        a.status,
        su.fullname AS student_name,
        su.email AS student_email,
        cu.fullname AS counsellor_name
    FROM appointments a
    INNER JOIN students s
        ON a.student_id = s.id
    INNER JOIN users su
        ON s.user_id = su.id
    INNER JOIN counsellors c
        ON a.counsellor_id = c.id
    INNER JOIN users cu
        ON c.user_id = cu.id
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Statistics */
$total = count($appointments);
$pending = 0;
$approved = 0;
$completed = 0;
$rejected = 0;

foreach ($appointments as $appointment) {
    switch ($appointment['status']) {
        case 'Pending':
            $pending++;
            break;

        case 'Approved':
            $approved++;
            break;

        case 'Completed':
            $completed++;
            break;

        case 'Rejected':
            $rejected++;
            break;
    }
}

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="mb-1">Appointments</h2>
            <p class="text-muted mb-0">
                Manage student counselling appointments.
            </p>
        </div>

        <a href="add_appointment.php" class="btn btn-primary">
            <i class="fa-solid fa-plus me-1"></i>
            Add Appointment
        </a>

    </div>


    <!-- STATISTICS -->
    <div class="row g-4 mb-4">

        <!-- Total -->
        <div class="col-xl col-md-6">
            <div class="card stat-card h-100">
                <i class="fa-solid fa-calendar-check text-primary"></i>

                <h2><?= $total; ?></h2>

                <h6>Total Appointments</h6>
            </div>
        </div>


        <!-- Pending -->
        <div class="col-xl col-md-6">
            <div class="card stat-card h-100">
                <i class="fa-solid fa-clock text-warning"></i>

                <h2><?= $pending; ?></h2>

                <h6>Pending</h6>
            </div>
        </div>


        <!-- Approved -->
        <div class="col-xl col-md-6">
            <div class="card stat-card h-100">
                <i class="fa-solid fa-circle-check text-success"></i>

                <h2><?= $approved; ?></h2>

                <h6>Approved</h6>
            </div>
        </div>


        <!-- Completed -->
        <div class="col-xl col-md-6">
            <div class="card stat-card h-100">
                <i class="fa-solid fa-check-double text-success"></i>

                <h2><?= $completed; ?></h2>

                <h6>Completed</h6>
            </div>
        </div>


        <!-- Rejected -->
        <div class="col-xl col-md-6">
            <div class="card stat-card h-100">
                <i class="fa-solid fa-circle-xmark text-danger"></i>

                <h2><?= $rejected; ?></h2>

                <h6>Rejected</h6>
            </div>
        </div>

    </div>


    <!-- APPOINTMENTS TABLE -->
    <div class="card">

        <div class="card-header bg-white d-flex justify-content-between align-items-center">

            <div>
                <h5 class="mb-1">
                    <i class="fa-solid fa-calendar-days text-primary me-2"></i>
                    All Appointments
                </h5>

                <small class="text-muted">
                    View and manage all counselling appointments.
                </small>
            </div>

            <span class="badge bg-primary">
                <?= $total; ?> Appointment<?= $total == 1 ? '' : 's'; ?>
            </span>

        </div>


        <?php if (empty($appointments)): ?>

            <div class="text-center py-5">

                <i class="fa-solid fa-calendar-xmark fa-3x text-muted mb-3"></i>

                <h5>No appointments found</h5>

                <p class="text-muted">
                    There are currently no appointments in the system.
                </p>

                <a href="add_appointment.php" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i>
                    Add Appointment
                </a>

            </div>

        <?php else: ?>

            <div class="table-responsive">

                <table class="table table-hover align-middle mb-0">

                    <thead>

                        <tr>
                            <th>#</th>
                            <th>Student</th>
                            <th>Counsellor</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Purpose</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>

                    </thead>


                    <tbody>

                    <?php foreach ($appointments as $appointment): ?>

                        <?php
                        switch ($appointment['status']) {

                            case 'Approved':
                                $badgeClass = 'bg-success';
                                break;

                            case 'Completed':
                                $badgeClass = 'bg-primary';
                                break;

                            case 'Rejected':
                                $badgeClass = 'bg-danger';
                                break;

                            default:
                                $badgeClass = 'bg-warning text-dark';
                                break;
                        }
                        ?>

                        <tr>

                            <!-- ID -->
                            <td>
                                <strong>
                                    #<?= (int)$appointment['id']; ?>
                                </strong>
                            </td>


                            <!-- STUDENT -->
                            <td>

                                <strong>
                                    <?= htmlspecialchars($appointment['student_name']); ?>
                                </strong>

                                <br>

                                <small class="text-muted">
                                    <?= htmlspecialchars($appointment['student_email']); ?>
                                </small>

                            </td>


                            <!-- COUNSELLOR -->
                            <td>
                                <?= htmlspecialchars($appointment['counsellor_name']); ?>
                            </td>


                            <!-- DATE -->
                            <td>
                                <i class="fa-regular fa-calendar text-muted me-1"></i>

                                <?= date(
                                    "d M Y",
                                    strtotime($appointment['appointment_date'])
                                ); ?>
                            </td>


                            <!-- TIME -->
                            <td>
                                <i class="fa-regular fa-clock text-muted me-1"></i>

                                <?= date(
                                    "h:i A",
                                    strtotime($appointment['appointment_time'])
                                ); ?>
                            </td>


                            <!-- PURPOSE -->
                            <td style="min-width: 180px; max-width: 280px;">

                                <?php
                                $purpose = trim($appointment['purpose'] ?? '');
                                ?>

                                <?php if ($purpose === ''): ?>

                                    <span class="text-muted">
                                        Not specified
                                    </span>

                                <?php elseif (strlen($purpose) > 70): ?>

                                    <?= htmlspecialchars(substr($purpose, 0, 70)); ?>...

                                <?php else: ?>

                                    <?= htmlspecialchars($purpose); ?>

                                <?php endif; ?>

                            </td>


                            <!-- STATUS -->
                            <td>

                                <span class="badge <?= $badgeClass; ?>">
                                    <?= htmlspecialchars($appointment['status']); ?>
                                </span>

                            </td>


                            <!-- ACTIONS -->
                            <td class="text-center">

                                <div class="d-flex justify-content-center gap-2">

                                    <a
                                        href="edit_appointment.php?id=<?= (int)$appointment['id']; ?>"
                                        class="btn btn-warning btn-sm"
                                        title="Edit Appointment"
                                    >
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
<form
    action="delete_appointment.php"
    method="POST"
    class="d-inline"
    onsubmit="return confirm('Are you sure you want to delete this appointment?');"
>
    <input
        type="hidden"
        name="id"
        value="<?= (int)$appointment['id']; ?>"
    >

    <button
        type="submit"
        class="btn btn-danger btn-sm"
        title="Delete Appointment"
    >
        <i class="fa-solid fa-trash"></i>
    </button>
</form>

                                </div>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>


<?php include "../includes/footer.php"; ?>
