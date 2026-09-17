<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: appointments.php");
    exit();
}

$id = (int) $_GET['id'];


/* Fetch appointment */
$stmt = $conn->prepare("
    SELECT
        id,
        student_id,
        counsellor_id,
        appointment_date,
        appointment_time,
        purpose,
        status
    FROM appointments
    WHERE id = ?
    LIMIT 1
");

$stmt->execute([$id]);

$appointment = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$appointment) {
    header("Location: appointments.php");
    exit();
}


/* Fetch students */
$stmt = $conn->query("
    SELECT
        s.id,
        u.fullname,
        s.register_no
    FROM students s
    JOIN users u ON s.user_id = u.id
    ORDER BY u.fullname
");

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* Fetch counsellors */
$stmt = $conn->query("
    SELECT
        c.id,
        u.fullname
    FROM counsellors c
    JOIN users u ON c.user_id = u.id
    ORDER BY u.fullname
");

$counsellors = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>Edit Appointment</title>

<link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
>

<link
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
    rel="stylesheet"
>

</head>

<body class="bg-light">

<div class="container mt-5 mb-5">

    <div class="card shadow">

        <div class="card-header bg-primary text-white">

            <h3 class="mb-0">

                <i class="fa-solid fa-calendar-pen me-2"></i>

                Edit Appointment

            </h3>

        </div>


        <div class="card-body">

            <form
                action="update_appointment.php"
                method="POST"
            >

                <input
                    type="hidden"
                    name="id"
                    value="<?= (int)$appointment['id']; ?>"
                >


                <!-- Student -->

                <div class="mb-3">

                    <label class="form-label">
                        Student
                    </label>

                    <select
                        name="student_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Student
                        </option>

                        <?php foreach ($students as $student): ?>

                            <option
                                value="<?= (int)$student['id']; ?>"
                                <?= (
                                    (int)$student['id'] ===
                                    (int)$appointment['student_id']
                                ) ? 'selected' : ''; ?>
                            >

                                <?= htmlspecialchars($student['fullname']); ?>

                                <?php if (!empty($student['register_no'])): ?>

                                    - <?= htmlspecialchars($student['register_no']); ?>

                                <?php endif; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Counsellor -->

                <div class="mb-3">

                    <label class="form-label">
                        Counsellor
                    </label>

                    <select
                        name="counsellor_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            Select Counsellor
                        </option>

                        <?php foreach ($counsellors as $counsellor): ?>

                            <option
                                value="<?= (int)$counsellor['id']; ?>"
                                <?= (
                                    (int)$counsellor['id'] ===
                                    (int)$appointment['counsellor_id']
                                ) ? 'selected' : ''; ?>
                            >

                                <?= htmlspecialchars($counsellor['fullname']); ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Date + Time -->

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Appointment Date
                        </label>

                        <input
                            type="date"
                            name="appointment_date"
                            class="form-control"
                            value="<?= htmlspecialchars($appointment['appointment_date']); ?>"
                            required
                        >

                    </div>


                    <div class="col-md-6 mb-3">

                        <label class="form-label">
                            Appointment Time
                        </label>

                        <input
                            type="time"
                            name="appointment_time"
                            class="form-control"
                            value="<?= htmlspecialchars(substr($appointment['appointment_time'], 0, 5)); ?>"
                            required
                        >

                    </div>

                </div>


                <!-- Purpose -->

                <div class="mb-3">

                    <label class="form-label">
                        Purpose
                    </label>

                    <textarea
                        name="purpose"
                        class="form-control"
                        rows="4"
                        maxlength="1000"
                        placeholder="Enter the purpose of the counselling appointment..."
                    ><?= htmlspecialchars($appointment['purpose'] ?? ''); ?></textarea>

                </div>


                <!-- Status -->

                <div class="mb-3">

                    <label class="form-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                        required
                    >

                        <?php
                        $statuses = [
                            'Pending',
                            'Approved',
                            'Rejected',
                            'Completed'
                        ];
                        ?>

                        <?php foreach ($statuses as $status): ?>

                            <option
                                value="<?= $status; ?>"
                                <?= $appointment['status'] === $status ? 'selected' : ''; ?>
                            >

                                <?= $status; ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Buttons -->

                <button
                    type="submit"
                    class="btn btn-success"
                >

                    <i class="fa-solid fa-floppy-disk me-1"></i>

                    Update Appointment

                </button>


                <a
                    href="appointments.php"
                    class="btn btn-secondary"
                >

                    <i class="fa-solid fa-arrow-left me-1"></i>

                    Back

                </a>

            </form>

        </div>

    </div>

</div>

</body>

</html>
