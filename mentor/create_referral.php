<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

$userId = $_SESSION['user_id'];

/* Get Mentor ID */

$stmt = $conn->prepare("
SELECT id
FROM mentors
WHERE user_id = ?
");

$stmt->execute([$userId]);

$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

$mentorId = $mentor['id'];

/* Fetch Assigned Students */

$stmt = $conn->prepare("
SELECT
    s.id,
    u.fullname,
    s.register_no
FROM students s
JOIN users u
ON s.user_id = u.id
WHERE s.mentor_id = ?
ORDER BY u.fullname
");

$stmt->execute([$mentorId]);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Fetch Counsellors */

$stmt = $conn->prepare("
SELECT
    c.id,
    u.fullname
FROM counsellors c
JOIN users u
ON c.user_id = u.id
ORDER BY u.fullname
");

$stmt->execute();

$counsellors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<div class="card shadow-sm">

    <div class="card-header bg-primary text-white">

        <h4>
            <i class="fa-solid fa-share-nodes"></i>
            Create Referral
        </h4>

    </div>

    <div class="card-body">

        <form action="save_referral.php" method="POST">

            <div class="mb-3">

                <label class="form-label">Student</label>

                <select name="student_id" class="form-select" required>

                    <option value="">Select Student</option>

                    <?php foreach($students as $student): ?>

                        <option value="<?= $student['id'] ?>">

                            <?= htmlspecialchars($student['fullname']) ?>
                            (<?= htmlspecialchars($student['register_no']) ?>)

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">Priority</label>

                <select name="priority" class="form-select">

                    <option>Low</option>
                    <option selected>Medium</option>
                    <option>High</option>

                </select>

            </div>

            <!-- NEW COUNSELLOR DROPDOWN -->

            <div class="mb-3">

                <label class="form-label">Counsellor</label>

                <select name="counsellor_id" class="form-select" required>

                    <option value="">Select Counsellor</option>

                    <?php foreach($counsellors as $c): ?>

                        <option value="<?= $c['id'] ?>">

                            <?= htmlspecialchars($c['fullname']) ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">Reason</label>

                <textarea
                    name="reason"
                    class="form-control"
                    rows="5"
                    required
                ></textarea>

            </div>

            <button class="btn btn-primary">

                <i class="fa-solid fa-paper-plane"></i>

                Submit Referral

            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>