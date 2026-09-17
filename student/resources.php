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

$stmt->execute([$_SESSION['user_id']]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student profile not found.");
}

include "../includes/header.php";

if (file_exists("../includes/student_sidebar.php")) {
    include "../includes/student_sidebar.php";
}

?>

<style>

.resources-page {
    padding: 25px;
}

.page-title {
    font-weight: 700;
    color: #1f2937;
}

.page-subtitle {
    color: #6b7280;
}

.resource-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 24px;
    height: 100%;
    transition: all 0.2s ease;
}

.resource-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08);
}

.resource-icon {
    width: 54px;
    height: 54px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    margin-bottom: 18px;
}

.icon-blue {
    background: #dbeafe;
    color: #2563eb;
}

.icon-green {
    background: #dcfce7;
    color: #16a34a;
}

.icon-yellow {
    background: #fef3c7;
    color: #d97706;
}

.icon-purple {
    background: #ede9fe;
    color: #7c3aed;
}

.icon-red {
    background: #fee2e2;
    color: #dc2626;
}

.icon-cyan {
    background: #cffafe;
    color: #0891b2;
}

.resource-title {
    font-size: 17px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 9px;
}

.resource-text {
    color: #6b7280;
    font-size: 14px;
    line-height: 1.6;
}

.resource-list {
    padding-left: 18px;
    color: #6b7280;
    font-size: 14px;
}

.resource-list li {
    margin-bottom: 7px;
}

.resource-section {
    margin-bottom: 30px;
}

.tip-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 24px;
}

.tip-item {
    padding: 14px 0;
    border-bottom: 1px solid #e5e7eb;
}

.tip-item:last-child {
    border-bottom: 0;
    padding-bottom: 0;
}

.tip-item:first-child {
    padding-top: 0;
}

.tip-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    flex-shrink: 0;
}

.support-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 25px;
}

</style>

<div class="content">

    <div class="resources-page">

        <!-- Page Header -->
        <div class="mb-4">

            <h2 class="page-title mb-1">

                <i class="fa-solid fa-book-open me-2"></i>

                Wellbeing Resources

            </h2>

            <p class="page-subtitle mb-0">

                Helpful guidance and practical tips for maintaining your
                wellbeing while studying.

            </p>

        </div>


        <!-- Main Resources -->
        <div class="resource-section">

            <div class="row g-4">

                <!-- Stress Management -->
                <div class="col-md-6 col-xl-4">

                    <div class="resource-card">

                        <div class="resource-icon icon-blue">

                            <i class="fa-solid fa-brain"></i>

                        </div>

                        <div class="resource-title">

                            Managing Stress

                        </div>

                        <p class="resource-text">

                            Stress can affect concentration, sleep and daily
                            activities. Small changes to your routine can help
                            you manage everyday academic pressure.

                        </p>

                        <ul class="resource-list">

                            <li>Break large tasks into smaller steps.</li>

                            <li>Take short breaks while studying.</li>

                            <li>Keep a realistic daily schedule.</li>

                            <li>Talk to someone when stress becomes difficult
                            to manage.</li>

                        </ul>

                    </div>

                </div>


                <!-- Sleep -->
                <div class="col-md-6 col-xl-4">

                    <div class="resource-card">

                        <div class="resource-icon icon-purple">

                            <i class="fa-solid fa-bed"></i>

                        </div>

                        <div class="resource-title">

                            Healthy Sleep

                        </div>

                        <p class="resource-text">

                            Adequate rest supports concentration, learning and
                            general wellbeing.

                        </p>

                        <ul class="resource-list">

                            <li>Try to maintain a consistent sleep schedule.</li>

                            <li>Reduce screen use before sleeping.</li>

                            <li>Create a comfortable sleeping environment.</li>

                            <li>Avoid regularly sacrificing sleep for study.</li>

                        </ul>

                    </div>

                </div>


                <!-- Study Balance -->
                <div class="col-md-6 col-xl-4">

                    <div class="resource-card">

                        <div class="resource-icon icon-green">

                            <i class="fa-solid fa-graduation-cap"></i>

                        </div>

                        <div class="resource-title">

                            Study-Life Balance

                        </div>

                        <p class="resource-text">

                            A balanced routine can make it easier to manage
                            academic responsibilities while maintaining time
                            for yourself.

                        </p>

                        <ul class="resource-list">

                            <li>Plan study time and personal time.</li>

                            <li>Set achievable academic goals.</li>

                            <li>Make time for hobbies and relaxation.</li>

                            <li>Avoid studying continuously without breaks.</li>

                        </ul>

                    </div>

                </div>


                <!-- Breathing -->
                <div class="col-md-6 col-xl-4">

                    <div class="resource-card">

                        <div class="resource-icon icon-cyan">

                            <i class="fa-solid fa-wind"></i>

                        </div>

                        <div class="resource-title">

                            Simple Breathing Exercise

                        </div>

                        <p class="resource-text">

                            Slow, controlled breathing can be used as a simple
                            relaxation technique during stressful moments.

                        </p>

                        <ul class="resource-list">

                            <li>Sit comfortably.</li>

                            <li>Take a slow breath in.</li>

                            <li>Pause briefly.</li>

                            <li>Slowly breathe out.</li>

                            <li>Repeat several times at a comfortable pace.</li>

                        </ul>

                    </div>

                </div>


                <!-- Healthy Routine -->
                <div class="col-md-6 col-xl-4">

                    <div class="resource-card">

                        <div class="resource-icon icon-yellow">

                            <i class="fa-solid fa-heart-pulse"></i>

                        </div>

                        <div class="resource-title">

                            Healthy Daily Routine

                        </div>

                        <p class="resource-text">

                            Simple daily habits can support physical and
                            emotional wellbeing.

                        </p>

                        <ul class="resource-list">

                            <li>Eat regular meals.</li>

                            <li>Stay hydrated.</li>

                            <li>Include physical activity in your routine.</li>

                            <li>Get enough rest.</li>

                        </ul>

                    </div>

                </div>


                <!-- Asking for Help -->
                <div class="col-md-6 col-xl-4">

                    <div class="resource-card">

                        <div class="resource-icon icon-red">

                            <i class="fa-solid fa-hand-holding-heart"></i>

                        </div>

                        <div class="resource-title">

                            Reaching Out for Support

                        </div>

                        <p class="resource-text">

                            You do not have to handle every difficulty alone.
                            Reaching out to a trusted person or wellbeing
                            professional can be an important step.

                        </p>

                        <ul class="resource-list">

                            <li>Talk to your mentor.</li>

                            <li>Consider contacting a counsellor.</li>

                            <li>Use the appointment system when support is
                            needed.</li>

                            <li>Speak to someone you trust.</li>

                        </ul>

                    </div>

                </div>

            </div>

        </div>


        <!-- Quick Tips -->
        <div class="row g-4">

            <div class="col-lg-7">

                <div class="tip-card">

                    <h5 class="fw-bold mb-3">

                        <i class="fa-solid fa-lightbulb text-warning me-2"></i>

                        Quick Wellbeing Tips

                    </h5>


                    <div class="tip-item d-flex gap-3">

                        <div class="tip-icon">

                            <i class="fa-solid fa-list-check text-primary"></i>

                        </div>

                        <div>

                            <div class="fw-semibold">

                                Set small goals

                            </div>

                            <div class="small text-muted">

                                Focus on one manageable task at a time instead
                                of trying to complete everything at once.

                            </div>

                        </div>

                    </div>


                    <div class="tip-item d-flex gap-3">

                        <div class="tip-icon">

                            <i class="fa-solid fa-person-walking text-success"></i>

                        </div>

                        <div>

                            <div class="fw-semibold">

                                Take regular breaks

                            </div>

                            <div class="small text-muted">

                                Step away from your study area periodically and
                                give yourself time to reset.

                            </div>

                        </div>

                    </div>


                    <div class="tip-item d-flex gap-3">

                        <div class="tip-icon">

                            <i class="fa-solid fa-comments text-info"></i>

                        </div>

                        <div>

                            <div class="fw-semibold">

                                Stay connected

                            </div>

                            <div class="small text-muted">

                                Maintaining supportive relationships can help
                                you feel less isolated during difficult periods.

                            </div>

                        </div>

                    </div>


                    <div class="tip-item d-flex gap-3">

                        <div class="tip-icon">

                            <i class="fa-solid fa-calendar-check text-primary"></i>

                        </div>

                        <div>

                            <div class="fw-semibold">

                                Keep appointments

                            </div>

                            <div class="small text-muted">

                                If you have a counselling appointment, keep
                                track of the date and time through My
                                Appointments.

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Support -->
            <div class="col-lg-5">

                <div class="support-card h-100">

                    <h5 class="fw-bold mb-3">

                        <i class="fa-solid fa-life-ring text-primary me-2"></i>

                        Need Support?

                    </h5>

                    <p class="text-muted">

                        If you are finding it difficult to manage your
                        wellbeing, consider using the support options available
                        through the college wellbeing system.

                    </p>


                    <div class="d-grid gap-2 mt-4">

                        <a href="appointments.php"
                           class="btn btn-primary">

                            <i class="fa-solid fa-calendar-plus me-2"></i>

                            View My Appointments

                        </a>


                        <a href="assessment.php"
                           class="btn btn-outline-primary">

                            <i class="fa-solid fa-clipboard-check me-2"></i>

                            Take Wellbeing Assessment

                        </a>


                        <a href="feedback.php"
                           class="btn btn-outline-secondary">

                            <i class="fa-solid fa-comment-dots me-2"></i>

                            Give Feedback

                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include "../includes/footer.php";

?>
