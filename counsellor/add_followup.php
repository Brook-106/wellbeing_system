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

/* Fetch Completed Appointments */

$stmt = $conn->prepare("
SELECT

a.id,

u.fullname,

s.register_no,

a.appointment_date

FROM appointments a

JOIN students s
ON a.student_id = s.id

JOIN users u
ON s.user_id = u.id

WHERE a.counsellor_id = ?
AND a.status = 'Completed'

ORDER BY a.appointment_date DESC
");

$stmt->execute([$counsellorId]);

$appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Save Follow-up */

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $appointmentId = $_POST['appointment_id'];
    $date = $_POST['followup_date'];
    $time = $_POST['followup_time'];
    $remarks = trim($_POST['remarks']);

    if (
        empty($appointmentId) ||
        empty($date) ||
        empty($time) ||
        empty($remarks)
    ) {
        die("Please complete all fields.");
    }

    $stmt = $conn->prepare("
    INSERT INTO followups
    (
        appointment_id,
        followup_date,
        followup_time,
        remarks,
        status
    )
    VALUES
    (
        ?,?,?,?,
        'Pending'
    )
    ");

    $stmt->execute([
        $appointmentId,
        $date,
        $time,
        $remarks
    ]);

    header("Location: followups.php?success=1");
    exit();
}

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<div class="container mt-4">

    <div class="card shadow">

        <div class="card-header bg-success text-white">

            <h4>

                <i class="fa-solid fa-user-clock"></i>

                Schedule Follow-up

            </h4>

        </div>

        <div class="card-body">

            <form method="POST">

                <div class="mb-3">

                    <label class="form-label">

                        Completed Appointment

                    </label>

                    <select
                        name="appointment_id"
                        class="form-select"
                        required>

                        <option value="">

                            Select Appointment

                        </option>

                        <?php foreach($appointments as $row): ?>

                            <option value="<?= $row['id'] ?>">

                                <?= htmlspecialchars($row['fullname']) ?>

                                (<?= htmlspecialchars($row['register_no']) ?>)

                                -

                                <?= date("d M Y", strtotime($row['appointment_date'])) ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Follow-up Date

                    </label>

                    <input
                        type="text"
                        id="followup_date"
                        name="followup_date"
                        class="form-control"
                        placeholder="Select Follow-up Date"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Follow-up Time

                    </label>

                    <input
                        type="text"
                        id="followup_time"
                        name="followup_time"
                        class="form-control"
                        placeholder="Select Follow-up Time"
                        required>

                </div>

                <div class="mb-3">

                    <label class="form-label">

                        Remarks

                    </label>

                    <textarea
                        name="remarks"
                        class="form-control"
                        rows="5"
                        placeholder="Enter follow-up remarks..."
                        required></textarea>

                </div>

                <button
                    type="submit"
                    class="btn btn-success">

                    <i class="fa-solid fa-floppy-disk"></i>

                    Save Follow-up

                </button>

                <a
                    href="followups.php"
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

    flatpickr("#followup_date", {

        dateFormat: "Y-m-d",

        altInput: true,

        altFormat: "d F Y",

        minDate: "today"

    });

    flatpickr("#followup_time", {

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