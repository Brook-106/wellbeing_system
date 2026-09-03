<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: referrals.php");
    exit();
}

$id = $_GET['id'];

// Get referral
$stmt = $conn->prepare("
SELECT *
FROM referrals
WHERE id = ?
");
$stmt->execute([$id]);
$referral = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$referral) {
    header("Location: referrals.php");
    exit();
}

// Students
$stmt = $conn->query("
SELECT s.id, u.fullname
FROM students s
JOIN users u ON s.user_id = u.id
ORDER BY u.fullname
");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mentors
$stmt = $conn->query("
SELECT m.id, u.fullname
FROM mentors m
JOIN users u ON m.user_id = u.id
ORDER BY u.fullname
");
$mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Counsellors
$stmt = $conn->query("
SELECT c.id, u.fullname
FROM counsellors c
JOIN users u ON c.user_id = u.id
ORDER BY u.fullname
");
$counsellors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">Edit Referral</h2>

<div class="card shadow">
    <div class="card-body">

        <form action="update_referral.php" method="POST">

            <input type="hidden" name="id" value="<?= $referral['id']; ?>">

            <!-- Student -->

            <div class="mb-3">

                <label class="form-label">Student</label>

                <select name="student_id" class="form-control" required>

                    <?php foreach($students as $student): ?>

                        <option
                            value="<?= $student['id']; ?>"
                            <?= $student['id'] == $referral['student_id'] ? 'selected' : ''; ?>>

                            <?= htmlspecialchars($student['fullname']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Mentor -->

            <div class="mb-3">

                <label class="form-label">Mentor</label>

                <select name="mentor_id" class="form-control" required>

                    <?php foreach($mentors as $mentor): ?>

                        <option
                            value="<?= $mentor['id']; ?>"
                            <?= $mentor['id'] == $referral['mentor_id'] ? 'selected' : ''; ?>>

                            <?= htmlspecialchars($mentor['fullname']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Counsellor -->

            <div class="mb-3">

                <label class="form-label">Counsellor</label>

                <select name="counsellor_id" class="form-control">

                    <option value="">Not Assigned</option>

                    <?php foreach($counsellors as $c): ?>

                        <option
                            value="<?= $c['id']; ?>"
                            <?= $c['id'] == $referral['counsellor_id'] ? 'selected' : ''; ?>>

                            <?= htmlspecialchars($c['fullname']); ?>

                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- Priority -->

            <div class="mb-3">

                <label class="form-label">Priority</label>

                <select name="priority" class="form-control">

                    <option value="Low" <?= $referral['priority'] == 'Low' ? 'selected' : ''; ?>>
                        Low
                    </option>

                    <option value="Medium" <?= $referral['priority'] == 'Medium' ? 'selected' : ''; ?>>
                        Medium
                    </option>

                    <option value="High" <?= $referral['priority'] == 'High' ? 'selected' : ''; ?>>
                        High
                    </option>

                </select>

            </div>

            <!-- Status -->

            <div class="mb-3">

                <label class="form-label">Status</label>

                <select name="status" class="form-control">

                    <option value="Pending" <?= $referral['status'] == 'Pending' ? 'selected' : ''; ?>>
                        Pending
                    </option>

                    <option value="Accepted" <?= $referral['status'] == 'Accepted' ? 'selected' : ''; ?>>
                        Accepted
                    </option>

                    <option value="Rejected" <?= $referral['status'] == 'Rejected' ? 'selected' : ''; ?>>
                        Rejected
                    </option>

                    <option value="Completed" <?= $referral['status'] == 'Completed' ? 'selected' : ''; ?>>
                        Completed
                    </option>

                </select>

            </div>

            <!-- Reason -->

            <div class="mb-3">

                <label class="form-label">Reason</label>

                <textarea
                    name="reason"
                    class="form-control"
                    rows="4"
                    required><?= htmlspecialchars($referral['reason']); ?></textarea>

            </div>

            <!-- Counsellor Notes -->

            <div class="mb-3">

                <label class="form-label">Counsellor Notes</label>

                <textarea
                    name="counsellor_notes"
                    class="form-control"
                    rows="5"
                    placeholder="Confidential notes (Visible only to Admin and Counsellor)"><?= htmlspecialchars($referral['counsellor_notes'] ?? ''); ?></textarea>

            </div>

            <button class="btn btn-success">
                Update Referral
            </button>

            <a href="referrals.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>
</div>

<?php include "../includes/footer.php"; ?>