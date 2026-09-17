<?php

session_start();

require_once "../includes/db.php";

/*
|--------------------------------------------------------------------------
| Student Access Protection
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
| Notifications Array
|--------------------------------------------------------------------------
*/
$notifications = [];

/*
|--------------------------------------------------------------------------
| Appointment Notifications
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        a.id,
        a.appointment_date,
        a.appointment_time,
        a.purpose,
        a.status,
        u.fullname AS counsellor_name,
        a.created_at
    FROM appointments a
    INNER JOIN counsellors c
        ON a.counsellor_id = c.id
    INNER JOIN users u
        ON c.user_id = u.id
    WHERE a.student_id = ?
    ORDER BY a.appointment_date DESC, a.appointment_time DESC
");

$stmt->execute([$studentId]);

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($appointments as $appointment) {

    $status = $appointment['status'];

    switch ($status) {

        case 'Approved':
            $title = "Appointment Approved";
            $message = "Your counselling appointment with " .
                       htmlspecialchars($appointment['counsellor_name']) .
                       " has been approved.";
            $icon = "fa-circle-check";
            $type = "success";
            break;

        case 'Rejected':
            $title = "Appointment Rejected";
            $message = "Your counselling appointment with " .
                       htmlspecialchars($appointment['counsellor_name']) .
                       " was rejected.";
            $icon = "fa-circle-xmark";
            $type = "danger";
            break;

        case 'Completed':
            $title = "Appointment Completed";
            $message = "Your counselling appointment with " .
                       htmlspecialchars($appointment['counsellor_name']) .
                       " has been completed.";
            $icon = "fa-calendar-check";
            $type = "primary";
            break;

        default:
            $title = "Appointment Pending";
            $message = "Your counselling appointment with " .
                       htmlspecialchars($appointment['counsellor_name']) .
                       " is currently pending.";
            $icon = "fa-clock";
            $type = "warning";
            break;
    }

    $appointmentDate = date(
        "d M Y",
        strtotime($appointment['appointment_date'])
    );

    $appointmentTime = date(
        "h:i A",
        strtotime($appointment['appointment_time'])
    );

    $notifications[] = [
        'title' => $title,
        'message' => $message,
        'date' => $appointmentDate,
        'time' => $appointmentTime,
        'created_at' => $appointment['created_at'],
        'icon' => $icon,
        'type' => $type,
        'category' => 'Appointment'
    ];
}

/*
|--------------------------------------------------------------------------
| Referral Notifications
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
    INNER JOIN mentors m
        ON r.mentor_id = m.id
    INNER JOIN users u
        ON m.user_id = u.id
    WHERE r.student_id = ?
    ORDER BY r.referral_date DESC
");

$stmt->execute([$studentId]);

$referrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($referrals as $referral) {

    $status = $referral['status'];

    switch ($status) {

        case 'Accepted':
            $title = "Referral Accepted";
            $message = "Your wellbeing referral from " .
                       htmlspecialchars($referral['mentor_name']) .
                       " has been accepted.";
            $icon = "fa-circle-check";
            $type = "success";
            break;

        case 'Rejected':
            $title = "Referral Rejected";
            $message = "Your wellbeing referral from " .
                       htmlspecialchars($referral['mentor_name']) .
                       " was rejected.";
            $icon = "fa-circle-xmark";
            $type = "danger";
            break;

        case 'Completed':
            $title = "Referral Completed";
            $message = "Your wellbeing referral has been marked as completed.";
            $icon = "fa-flag-checkered";
            $type = "primary";
            break;

        default:
            $title = "New Wellbeing Referral";
            $message = "A wellbeing referral has been created for you by " .
                       htmlspecialchars($referral['mentor_name']) . ".";
            $icon = "fa-hand-holding-heart";
            $type = "warning";
            break;
    }

    $referralDate = date(
        "d M Y",
        strtotime($referral['referral_date'])
    );

    $referralTime = date(
        "h:i A",
        strtotime($referral['referral_date'])
    );

    $notifications[] = [
        'title' => $title,
        'message' => $message,
        'date' => $referralDate,
        'time' => $referralTime,
        'created_at' => $referral['referral_date'],
        'icon' => $icon,
        'type' => $type,
        'category' => 'Referral'
    ];
}

/*
|--------------------------------------------------------------------------
| Upcoming Appointment Notifications
|--------------------------------------------------------------------------
*/
$today = date('Y-m-d');

foreach ($appointments as $appointment) {

    if (
        $appointment['appointment_date'] >= $today &&
        $appointment['status'] !== 'Rejected' &&
        $appointment['status'] !== 'Completed'
    ) {

        $appointmentDate = date(
            "d M Y",
            strtotime($appointment['appointment_date'])
        );

        $appointmentTime = date(
            "h:i A",
            strtotime($appointment['appointment_time'])
        );

        $notifications[] = [
            'title' => "Upcoming Appointment",
            'message' => "You have an upcoming counselling appointment with " .
                         htmlspecialchars($appointment['counsellor_name']) .
                         ".",
            'date' => $appointmentDate,
            'time' => $appointmentTime,
            'created_at' => $appointment['appointment_date'] . ' ' .
                           $appointment['appointment_time'],
            'icon' => "fa-calendar-days",
            'type' => "info",
            'category' => 'Reminder'
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Sort Notifications
|--------------------------------------------------------------------------
*/
usort($notifications, function ($a, $b) {

    return strtotime($b['created_at']) <=> strtotime($a['created_at']);

});

/*
|--------------------------------------------------------------------------
| Notification Counts
|--------------------------------------------------------------------------
*/
$totalNotifications = count($notifications);

$appointmentNotifications = 0;
$referralNotifications = 0;
$reminderNotifications = 0;

foreach ($notifications as $notification) {

    if ($notification['category'] === 'Appointment') {
        $appointmentNotifications++;
    }

    if ($notification['category'] === 'Referral') {
        $referralNotifications++;
    }

    if ($notification['category'] === 'Reminder') {
        $reminderNotifications++;
    }
}

/*
|--------------------------------------------------------------------------
| Page Header
|--------------------------------------------------------------------------
*/
include "../includes/header.php";

/*
|--------------------------------------------------------------------------
| Student Sidebar
|--------------------------------------------------------------------------
*/
if (file_exists("../includes/student_sidebar.php")) {
    include "../includes/student_sidebar.php";
}

?>

<style>

.notification-page {
    padding: 25px;
}

.page-title {
    font-weight: 700;
    color: #1f2937;
}

.page-subtitle {
    color: #6b7280;
}

.notification-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 18px;
    margin-bottom: 15px;
    transition: 0.2s ease;
}

.notification-card:hover {
    box-shadow: 0 6px 18px rgba(0,0,0,0.07);
    transform: translateY(-1px);
}

.notification-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 20px;
}

.icon-success {
    background: #dcfce7;
    color: #15803d;
}

.icon-danger {
    background: #fee2e2;
    color: #dc2626;
}

.icon-warning {
    background: #fef3c7;
    color: #d97706;
}

.icon-primary {
    background: #dbeafe;
    color: #2563eb;
}

.icon-info {
    background: #cffafe;
    color: #0891b2;
}

.notification-title {
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 4px;
}

.notification-message {
    color: #6b7280;
    font-size: 14px;
    margin-bottom: 7px;
}

.notification-meta {
    font-size: 12px;
    color: #9ca3af;
}

.category-badge {
    font-size: 11px;
    padding: 5px 9px;
    border-radius: 20px;
    background: #f3f4f6;
    color: #4b5563;
}

.empty-notifications {
    text-align: center;
    padding: 70px 20px;
    background: #ffffff;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
}

.empty-notifications i {
    font-size: 48px;
    color: #9ca3af;
    margin-bottom: 15px;
}

.summary-card {
    border: 0;
    border-radius: 14px;
    color: white;
    padding: 18px;
}

.summary-number {
    font-size: 28px;
    font-weight: 700;
}

.summary-label {
    font-size: 13px;
    opacity: 0.9;
}

</style>

<div class="content">

    <div class="notification-page">

        <!-- Page Header -->
        <div class="mb-4">

            <h2 class="page-title mb-1">
                <i class="fa-solid fa-bell me-2"></i>
                Notifications
            </h2>

            <p class="page-subtitle mb-0">
                Stay updated with your appointments and wellbeing referrals.
            </p>

        </div>

        <!-- Summary Cards -->
        <div class="row g-3 mb-4">

            <div class="col-md-4">

                <div class="summary-card bg-primary">

                    <div class="summary-number">
                        <?php echo $totalNotifications; ?>
                    </div>

                    <div class="summary-label">
                        Total Notifications
                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="summary-card bg-success">

                    <div class="summary-number">
                        <?php echo $appointmentNotifications; ?>
                    </div>

                    <div class="summary-label">
                        Appointment Updates
                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="summary-card bg-warning text-dark">

                    <div class="summary-number">
                        <?php echo $referralNotifications; ?>
                    </div>

                    <div class="summary-label">
                        Referral Updates
                    </div>

                </div>

            </div>

        </div>

        <!-- Notifications -->
        <?php if ($totalNotifications > 0): ?>

            <div class="row">

                <div class="col-lg-9">

                    <?php foreach ($notifications as $notification): ?>

                        <div class="notification-card">

                            <div class="d-flex align-items-start gap-3">

                                <!-- Icon -->
                                <div class="notification-icon icon-<?php
                                    echo htmlspecialchars($notification['type']);
                                ?>">

                                    <i class="fa-solid <?php
                                        echo htmlspecialchars($notification['icon']);
                                    ?>"></i>

                                </div>

                                <!-- Content -->
                                <div class="flex-grow-1">

                                    <div class="d-flex justify-content-between align-items-start gap-2">

                                        <div>

                                            <div class="notification-title">

                                                <?php
                                                echo htmlspecialchars(
                                                    $notification['title']
                                                );
                                                ?>

                                            </div>

                                        </div>

                                        <span class="category-badge">

                                            <?php
                                            echo htmlspecialchars(
                                                $notification['category']
                                            );
                                            ?>

                                        </span>

                                    </div>

                                    <div class="notification-message">

                                        <?php
                                        echo $notification['message'];
                                        ?>

                                    </div>

                                    <div class="notification-meta">

                                        <i class="fa-regular fa-calendar me-1"></i>

                                        <?php
                                        echo htmlspecialchars(
                                            $notification['date']
                                        );
                                        ?>

                                        <span class="mx-2">•</span>

                                        <i class="fa-regular fa-clock me-1"></i>

                                        <?php
                                        echo htmlspecialchars(
                                            $notification['time']
                                        );
                                        ?>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

                <!-- Information Card -->
                <div class="col-lg-3">

                    <div class="card border-0 shadow-sm">

                        <div class="card-body">

                            <h6 class="fw-bold mb-3">

                                <i class="fa-solid fa-circle-info me-2 text-primary"></i>

                                Notification Information

                            </h6>

                            <p class="small text-muted mb-3">

                                Notifications are generated from your current
                                appointments and wellbeing referrals.

                            </p>

                            <hr>

                            <div class="small text-muted">

                                <div class="mb-2">

                                    <i class="fa-solid fa-calendar-check text-primary me-2"></i>

                                    Appointment updates

                                </div>

                                <div class="mb-2">

                                    <i class="fa-solid fa-hand-holding-heart text-success me-2"></i>

                                    Referral updates

                                </div>

                                <div>

                                    <i class="fa-solid fa-calendar-days text-info me-2"></i>

                                    Upcoming appointment reminders

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        <?php else: ?>

            <div class="empty-notifications">

                <i class="fa-regular fa-bell-slash"></i>

                <h5 class="fw-bold">
                    No Notifications
                </h5>

                <p class="text-muted mb-0">
                    You don't have any appointment or referral updates yet.
                </p>

            </div>

        <?php endif; ?>

    </div>

</div>

<?php

include "../includes/footer.php";

?>
