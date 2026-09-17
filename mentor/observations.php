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

$successMessage = '';
$errorMessage = '';

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
| Add Observation
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_observation'])) {

    $studentId = (int)($_POST['student_id'] ?? 0);
    $observation = trim($_POST['observation'] ?? '');

    if ($studentId <= 0) {

        $errorMessage = "Please select a student.";

    } elseif ($observation === '') {

        $errorMessage = "Please enter an observation.";

    } else {

        /*
         * Make sure the selected student actually belongs
         * to this mentor.
         */
        $stmt = $conn->prepare("
            SELECT id
            FROM students
            WHERE id = ?
              AND mentor_id = ?
            LIMIT 1
        ");

        $stmt->execute([
            $studentId,
            $mentorId
        ]);

        $studentExists = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$studentExists) {

            $errorMessage = "You can only add observations for your assigned students.";

        } else {

            $stmt = $conn->prepare("
                INSERT INTO mentor_observations
                (
                    student_id,
                    mentor_id,
                    observation
                )
                VALUES (?, ?, ?)
            ");

            $stmt->execute([
                $studentId,
                $mentorId,
                $observation
            ]);

            $successMessage = "Observation added successfully.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Delete Observation
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_observation'])) {

    $observationId = (int)($_POST['observation_id'] ?? 0);

    if ($observationId > 0) {

        /*
         * Delete only observations belonging to this mentor.
         */
        $stmt = $conn->prepare("
            DELETE FROM mentor_observations
            WHERE id = ?
              AND mentor_id = ?
        ");

        $stmt->execute([
            $observationId,
            $mentorId
        ]);

        if ($stmt->rowCount() > 0) {

            $successMessage = "Observation deleted successfully.";

        } else {

            $errorMessage = "Observation could not be deleted.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Get Assigned Students
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        s.id,
        s.register_no,
        u.fullname
    FROM students s
    INNER JOIN users u
        ON s.user_id = u.id
    WHERE s.mentor_id = ?
    ORDER BY u.fullname ASC
");

$stmt->execute([$mentorId]);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Get Mentor Observations
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        o.id,
        o.observation,
        o.created_at,
        o.updated_at,

        s.register_no,

        u.fullname AS student_name

    FROM mentor_observations o

    INNER JOIN students s
        ON o.student_id = s.id

    INNER JOIN users u
        ON s.user_id = u.id

    WHERE o.mentor_id = ?

    ORDER BY o.created_at DESC
");

$stmt->execute([$mentorId]);

$observations = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalObservations = count($observations);

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

.observations-page {
    padding: 25px;
}

.page-title {
    font-weight: 700;
    color: #1f2937;
}

.page-subtitle {
    color: #6b7280;
}

.observation-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
}

.card-header-custom {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.form-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}

.observation-item {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
}

.observation-item:last-child {
    border-bottom: 0;
}

.student-name {
    font-weight: 700;
    color: #1f2937;
}

.register-no {
    font-size: 12px;
    color: #94a3b8;
}

.observation-text {
    color: #475569;
    font-size: 14px;
    line-height: 1.6;
    white-space: pre-line;
}

.observation-date {
    color: #94a3b8;
    font-size: 12px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state i {
    font-size: 45px;
    color: #94a3b8;
    margin-bottom: 15px;
}

.info-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
}

</style>

<div class="content">

    <div class="observations-page">

        <!-- Page Header -->
        <div class="mb-4">

            <h2 class="page-title mb-1">

                <i class="fa-solid fa-eye me-2"></i>

                Student Observations

            </h2>

            <p class="page-subtitle mb-0">

                Record and review observations about your assigned students.

            </p>

        </div>


        <!-- Messages -->
        <?php if ($successMessage !== ''): ?>

            <div class="alert alert-success border-0 shadow-sm">

                <i class="fa-solid fa-circle-check me-2"></i>

                <?php echo htmlspecialchars($successMessage); ?>

            </div>

        <?php endif; ?>


        <?php if ($errorMessage !== ''): ?>

            <div class="alert alert-danger border-0 shadow-sm">

                <i class="fa-solid fa-circle-exclamation me-2"></i>

                <?php echo htmlspecialchars($errorMessage); ?>

            </div>

        <?php endif; ?>


        <div class="row g-4">


            <!-- Add Observation -->
            <div class="col-lg-4">

                <div class="form-card">

                    <h5 class="fw-bold mb-2">

                        <i class="fa-solid fa-plus-circle text-primary me-2"></i>

                        Add Observation

                    </h5>

                    <p class="text-muted small mb-4">

                        Record a relevant observation about one of your
                        assigned students.

                    </p>


                    <?php if (count($students) > 0): ?>

                        <form method="POST">

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

                                        <option value="<?php
                                            echo (int)$student['id'];
                                        ?>">

                                            <?php
                                            echo htmlspecialchars(
                                                $student['fullname']
                                            );
                                            ?>

                                            <?php if (!empty($student['register_no'])): ?>

                                                -
                                                <?php
                                                echo htmlspecialchars(
                                                    $student['register_no']
                                                );
                                                ?>

                                            <?php endif; ?>

                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <!-- Observation -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Observation
                                </label>

                                <textarea
                                    name="observation"
                                    class="form-control"
                                    rows="7"
                                    maxlength="2000"
                                    placeholder="Enter your observation..."
                                    required
                                ></textarea>

                                <div class="form-text">
                                    Maximum 2000 characters.
                                </div>

                            </div>


                            <button
                                type="submit"
                                name="add_observation"
                                class="btn btn-primary w-100"
                            >

                                <i class="fa-solid fa-save me-2"></i>

                                Save Observation

                            </button>

                        </form>

                    <?php else: ?>

                        <div class="text-center text-muted py-4">

                            <i class="fa-solid fa-user-graduate fa-2x mb-3"></i>

                            <p class="mb-0">

                                No students are currently assigned to you.

                            </p>

                        </div>

                    <?php endif; ?>

                </div>


                <!-- Privacy Information -->
                <div class="info-card mt-4">

                    <h6 class="fw-bold mb-3">

                        <i class="fa-solid fa-shield-halved text-success me-2"></i>

                        Privacy

                    </h6>

                    <p class="small text-muted mb-0">

                        These observations are mentor records. They are
                        separate from confidential counselling notes and do not
                        provide access to counsellor notes.

                    </p>

                </div>

            </div>


            <!-- Observation List -->
            <div class="col-lg-8">

                <div class="observation-card">

                    <div class="card-header-custom">

                        <div class="d-flex justify-content-between align-items-center">

                            <div>

                                <h5 class="fw-bold mb-1">

                                    <i class="fa-solid fa-clipboard-list text-primary me-2"></i>

                                    My Observations

                                </h5>

                                <small class="text-muted">

                                    Observations recorded for your assigned
                                    students.

                                </small>

                            </div>

                            <span class="badge bg-primary">

                                <?php echo $totalObservations; ?>

                            </span>

                        </div>

                    </div>


                    <?php if ($totalObservations > 0): ?>

                        <?php foreach ($observations as $observation): ?>

                            <div class="observation-item">

                                <div class="d-flex justify-content-between
                                            align-items-start gap-3">

                                    <div>

                                        <div class="student-name">

                                            <i class="fa-solid fa-user-graduate text-primary me-2"></i>

                                            <?php
                                            echo htmlspecialchars(
                                                $observation['student_name']
                                            );
                                            ?>

                                        </div>

                                        <div class="register-no">

                                            <?php
                                            echo htmlspecialchars(
                                                $observation['register_no']
                                                    ?: 'No register number'
                                            );
                                            ?>

                                        </div>

                                    </div>


                                    <!-- Delete -->
                                    <form
                                        method="POST"
                                        onsubmit="return confirm('Delete this observation?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="observation_id"
                                            value="<?php
                                            echo (int)$observation['id'];
                                            ?>"
                                        >

                                        <button
                                            type="submit"
                                            name="delete_observation"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Delete observation"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </button>

                                    </form>

                                </div>


                                <div class="observation-text mt-3">

                                    <?php
                                    echo htmlspecialchars(
                                        $observation['observation']
                                    );
                                    ?>

                                </div>


                                <div class="observation-date mt-3">

                                    <i class="fa-regular fa-clock me-1"></i>

                                    Added

                                    <?php
                                    echo date(
                                        "d M Y, h:i A",
                                        strtotime(
                                            $observation['created_at']
                                        )
                                    );
                                    ?>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <div class="empty-state">

                            <i class="fa-regular fa-clipboard"></i>

                            <h5 class="fw-bold">
                                No Observations Yet
                            </h5>

                            <p class="text-muted mb-0">

                                Your recorded student observations will appear
                                here.

                            </p>

                        </div>

                    <?php endif; ?>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include "../includes/footer.php";

?>
