<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['referral_id'])) {
    die("Referral ID is missing.");
}

$referralId = $_GET['referral_id'];

/* Get Referral Details */

$stmt = $conn->prepare("
SELECT
    r.student_id,
    r.counsellor_id,
    u.fullname,
    s.register_no
FROM referrals r
JOIN students s
ON r.student_id = s.id
JOIN users u
ON s.user_id = u.id
WHERE r.id = ?
");

$stmt->execute([$referralId]);

$referral = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$referral) {
    die("Referral not found.");
}

/* Save Appointment */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $appointmentDate = $_POST['appointment_date'];
    $appointmentTime = $_POST['appointment_time'];
    $purpose = trim($_POST['purpose']);

    if (
        empty($appointmentDate) ||
        empty($appointmentTime) ||
        empty($purpose)
    ) {
        die("Please fill all fields.");
    }

    $stmt = $conn->prepare("
    INSERT INTO appointments
    (
        student_id,
        counsellor_id,
        appointment_date,
        appointment_time,
        purpose,
        status
    )
    VALUES
    (
        ?, ?, ?, ?, ?, 'Pending'
    )
    ");

    $stmt->execute([
        $referral['student_id'],
        $referral['counsellor_id'],
        $appointmentDate,
        $appointmentTime,
        $purpose
    ]);

    header("Location: appointments.php?success=1");
    exit();
}

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h4>

                <i class="fa-solid fa-calendar-plus"></i>

                Schedule Appointment

            </h4>

        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">

                        Student

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($referral['fullname']) . " (" . htmlspecialchars($referral['register_no']) . ")" ?>"
                        readonly>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Appointment Date

                    </label>

                    <input
                        type="text"
                        id="appointment_date"
                        name="appointment_date"
                        class="form-control"
                        placeholder="Select Appointment Date"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Appointment Time

                    </label>

                    <input
                        type="text"
                        id="appointment_time"
                        name="appointment_time"
                        class="form-control"
                        placeholder="Select Appointment Time"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Purpose

                    </label>

                    <textarea
                        name="purpose"
                        class="form-control"
                        rows="5"
                        placeholder="Reason for counselling session..."
                        required></textarea>

                </div>

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="fa-solid fa-calendar-check"></i>

                    Schedule Appointment

                </button>

                <a
                    href="referrals.php"
                    class="btn btn-secondary">

                    Cancel

                </a>

            </form>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>

<script>

document.addEventListener("DOMContentLoaded", function () {

    flatpickr("#appointment_date", {

        dateFormat: "Y-m-d",

        altInput: true,

        altFormat: "d F Y",

        minDate: "today"

    });

    flatpickr("#appointment_time", {

        enableTime: true,

        noCalendar: true,

        dateFormat: "H:i:S",

        altInput: true,

        altFormat: "h:i K",

        time_24hr: false,

        minuteIncrement: 5

    });

});

</script>