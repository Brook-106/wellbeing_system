<?php

session_start();

require_once "../includes/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$userId = $_SESSION['user_id'];

/*
|--------------------------------------------------------------------------
| Get Student
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        s.id AS student_id,
        u.fullname
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

$message = "";
$messageType = "";

/*
|--------------------------------------------------------------------------
| Submit Assessment
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $q1 = isset($_POST['q1']) ? (int) $_POST['q1'] : 0;
    $q2 = isset($_POST['q2']) ? (int) $_POST['q2'] : 0;
    $q3 = isset($_POST['q3']) ? (int) $_POST['q3'] : 0;
    $q4 = isset($_POST['q4']) ? (int) $_POST['q4'] : 0;
    $q5 = isset($_POST['q5']) ? (int) $_POST['q5'] : 0;

    $answers = [$q1, $q2, $q3, $q4, $q5];

    $valid = true;

    foreach ($answers as $answer) {
        if ($answer < 1 || $answer > 5) {
            $valid = false;
            break;
        }
    }

    if (!$valid) {

        $message = "Please answer all questions.";
        $messageType = "danger";

    } else {

        $totalScore = array_sum($answers);

        if ($totalScore <= 9) {
            $result = "Low Concern";
        } elseif ($totalScore <= 15) {
            $result = "Moderate Concern";
        } else {
            $result = "High Concern";
        }

        $stmt = $conn->prepare("
            INSERT INTO assessments
            (
                student_id,
                q1,
                q2,
                q3,
                q4,
                q5,
                total_score,
                result
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $studentId,
            $q1,
            $q2,
            $q3,
            $q4,
            $q5,
            $totalScore,
            $result
        ]);

        $message = "Assessment submitted successfully.";
        $messageType = "success";
    }
}

/*
|--------------------------------------------------------------------------
| Previous Assessments
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        total_score,
        result,
        submitted_at
    FROM assessments
    WHERE student_id = ?
    ORDER BY submitted_at DESC
");

$stmt->execute([$studentId]);
$assessments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";

if (file_exists("../includes/student_sidebar.php")) {
    include "../includes/student_sidebar.php";
}

?>

<div class="container-fluid py-4">

    <div class="mb-4">
        <h2 class="fw-bold">
            <i class="fa-solid fa-clipboard-check me-2"></i>
            Wellbeing Assessment
        </h2>

        <p class="text-muted mb-0">
            Complete this short self-assessment to reflect on your current wellbeing.
        </p>
    </div>

    <?php if ($message): ?>

        <div class="alert alert-<?= $messageType; ?> alert-dismissible fade show">
            <?= htmlspecialchars($message); ?>

            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert">
            </button>
        </div>

    <?php endif; ?>

    <div class="card shadow-sm border-0 mb-4">

        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold">
                <i class="fa-solid fa-list-check me-2"></i>
                Self-Assessment
            </h5>
        </div>

        <div class="card-body">

            <div class="alert alert-info">
                <strong>How to answer:</strong>
                Choose the option that best describes how you have been feeling recently.
            </div>

            <form method="POST">

                <?php
                $questions = [
                    1 => "I feel comfortable managing my daily responsibilities.",
                    2 => "I feel positive about my studies and academic progress.",
                    3 => "I feel that I have someone I can talk to when I need support.",
                    4 => "I am able to manage stress when difficult situations occur.",
                    5 => "I feel satisfied with my overall wellbeing."
                ];
                ?>

                <?php foreach ($questions as $number => $question): ?>

                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            <?= $number; ?>.
                            <?= htmlspecialchars($question); ?>
                        </label>

                        <select
                            name="q<?= $number; ?>"
                            class="form-select"
                            required>

                            <option value="">Select an answer</option>

                            <option value="1">
                                1 - Never
                            </option>

                            <option value="2">
                                2 - Rarely
                            </option>

                            <option value="3">
                                3 - Sometimes
                            </option>

                            <option value="4">
                                4 - Often
                            </option>

                            <option value="5">
                                5 - Always
                            </option>

                        </select>

                    </div>

                <?php endforeach; ?>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="fa-solid fa-paper-plane me-2"></i>
                    Submit Assessment

                </button>

            </form>

        </div>

    </div>

    <?php if (!empty($assessments)): ?>

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-3">

                <h5 class="mb-0 fw-bold">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i>
                    Previous Assessments
                </h5>

            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-hover align-middle">

                        <thead class="table-light">

                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Result</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($assessments as $index => $assessment): ?>

                                <?php

                                $resultClass = "secondary";

                                if ($assessment['result'] === "Low Concern") {
                                    $resultClass = "success";
                                } elseif ($assessment['result'] === "Moderate Concern") {
                                    $resultClass = "warning text-dark";
                                } elseif ($assessment['result'] === "High Concern") {
                                    $resultClass = "danger";
                                }

                                ?>

                                <tr>

                                    <td>
                                        <?= $index + 1; ?>
                                    </td>

                                    <td>
                                        <?= date(
                                            "d M Y h:i A",
                                            strtotime($assessment['submitted_at'])
                                        ); ?>
                                    </td>

                                    <td>
                                        <?= (int) $assessment['total_score']; ?>/25
                                    </td>

                                    <td>
                                        <span class="badge bg-<?= $resultClass; ?>">
                                            <?= htmlspecialchars($assessment['result']); ?>
                                        </span>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    <?php endif; ?>

</div>

<?php include "../includes/footer.php"; ?>
