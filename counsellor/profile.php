<?php

session_start();

require_once "../includes/db.php";

/*
|--------------------------------------------------------------------------
| Counsellor Access Protection
|--------------------------------------------------------------------------
*/
if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'counsellor'
) {
    header("Location: ../login.php");
    exit();
}

$userId = (int)$_SESSION['user_id'];

$successMessage = '';
$errorMessage = '';

/*
|--------------------------------------------------------------------------
| Get Counsellor Profile
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        c.id AS counsellor_id,
        c.department_id,
        c.specialization,
        c.phone,

        u.id AS user_id,
        u.fullname,
        u.email,
        u.status,
        u.created_at,

        d.department_name

    FROM counsellors c

    INNER JOIN users u
        ON c.user_id = u.id

    LEFT JOIN departments d
        ON c.department_id = d.id

    WHERE c.user_id = ?

    LIMIT 1
");

$stmt->execute([$userId]);

$profile = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$profile) {
    die("Counsellor profile not found.");
}

$counsellorId = (int)$profile['counsellor_id'];

/*
|--------------------------------------------------------------------------
| Update Profile
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['update_profile'])
) {

    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');

    if ($fullname === '') {

        $errorMessage = "Full name is required.";

    } elseif (strlen($fullname) > 100) {

        $errorMessage = "Full name must not exceed 100 characters.";

    } elseif (strlen($phone) > 20) {

        $errorMessage = "Phone number must not exceed 20 characters.";

    } elseif (strlen($specialization) > 100) {

        $errorMessage = "Specialization must not exceed 100 characters.";

    } else {

        try {

            $conn->beginTransaction();

            /*
             * Update user information.
             */
            $stmt = $conn->prepare("
                UPDATE users
                SET fullname = ?
                WHERE id = ?
                  AND role = 'counsellor'
            ");

            $stmt->execute([
                $fullname,
                $userId
            ]);

            /*
             * Update counsellor information.
             */
            $stmt = $conn->prepare("
                UPDATE counsellors
                SET
                    phone = ?,
                    specialization = ?
                WHERE id = ?
            ");

            $stmt->execute([
                $phone !== '' ? $phone : null,
                $specialization !== '' ? $specialization : null,
                $counsellorId
            ]);

            $conn->commit();

            /*
             * Keep sidebar name updated immediately.
             */
            $_SESSION['fullname'] = $fullname;

            $successMessage = "Profile updated successfully.";

            /*
             * Refresh displayed profile data.
             */
            $stmt = $conn->prepare("
                SELECT
                    c.id AS counsellor_id,
                    c.department_id,
                    c.specialization,
                    c.phone,

                    u.id AS user_id,
                    u.fullname,
                    u.email,
                    u.status,
                    u.created_at,

                    d.department_name

                FROM counsellors c

                INNER JOIN users u
                    ON c.user_id = u.id

                LEFT JOIN departments d
                    ON c.department_id = d.id

                WHERE c.user_id = ?

                LIMIT 1
            ");

            $stmt->execute([$userId]);

            $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {

            if ($conn->inTransaction()) {
                $conn->rollBack();
            }

            $errorMessage = "Unable to update profile. Please try again.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Change Password
|--------------------------------------------------------------------------
*/
if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['change_password'])
) {

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($currentPassword === '') {

        $errorMessage = "Please enter your current password.";

    } elseif ($newPassword === '') {

        $errorMessage = "Please enter a new password.";

    } elseif (strlen($newPassword) < 6) {

        $errorMessage = "New password must contain at least 6 characters.";

    } elseif ($newPassword !== $confirmPassword) {

        $errorMessage = "New passwords do not match.";

    } else {

        $stmt = $conn->prepare("
            SELECT password
            FROM users
            WHERE id = ?
              AND role = 'counsellor'
            LIMIT 1
        ");

        $stmt->execute([$userId]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {

            $errorMessage = "Account not found.";

        } elseif (!password_verify($currentPassword, $user['password'])) {

            $errorMessage = "Current password is incorrect.";

        } else {

            $hashedPassword = password_hash(
                $newPassword,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare("
                UPDATE users
                SET password = ?
                WHERE id = ?
                  AND role = 'counsellor'
            ");

            $stmt->execute([
                $hashedPassword,
                $userId
            ]);

            $successMessage = "Password changed successfully.";
        }
    }
}

/*
|--------------------------------------------------------------------------
| Header + Sidebar
|--------------------------------------------------------------------------
*/
include "../includes/header.php";

if (file_exists("../includes/counsellor_sidebar.php")) {
    include "../includes/counsellor_sidebar.php";
}

?>

<style>

.profile-page {
    padding: 25px;
}

.profile-header {
    background: #ffffff;
    border-radius: 18px;
    padding: 28px;
    border: 1px solid #e5e7eb;
    margin-bottom: 25px;
}

.profile-avatar {
    width: 85px;
    height: 85px;
    border-radius: 50%;
    background: #2563eb;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 35px;
}

.profile-name {
    font-weight: 700;
    color: #1f2937;
}

.profile-role {
    color: #6b7280;
}

.profile-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    overflow: hidden;
}

.profile-card-header {
    padding: 20px 24px;
    border-bottom: 1px solid #e5e7eb;
}

.profile-card-body {
    padding: 24px;
}

.section-title {
    font-weight: 700;
    color: #1f2937;
}

.form-label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}

.info-row {
    padding: 14px 0;
    border-bottom: 1px solid #eef2f7;
}

.info-row:last-child {
    border-bottom: 0;
}

.info-label {
    font-size: 12px;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.info-value {
    color: #334155;
    font-weight: 600;
    margin-top: 3px;
}

.readonly-box {
    background: #f8fafc;
}

</style>

<div class="content">

    <div class="profile-page">

        <!-- Header -->
        <div class="profile-header">

            <div class="d-flex align-items-center gap-4">

                <div class="profile-avatar">

                    <i class="fa-solid fa-user"></i>

                </div>

                <div>

                    <h2 class="profile-name mb-1">

                        <?= htmlspecialchars($profile['fullname']) ?>

                    </h2>

                    <div class="profile-role">

                        <i class="fa-solid fa-user-doctor me-1"></i>

                        Counsellor

                    </div>

                </div>

            </div>

        </div>


        <!-- Messages -->
        <?php if ($successMessage !== ''): ?>

            <div class="alert alert-success border-0 shadow-sm">

                <i class="fa-solid fa-circle-check me-2"></i>

                <?= htmlspecialchars($successMessage) ?>

            </div>

        <?php endif; ?>


        <?php if ($errorMessage !== ''): ?>

            <div class="alert alert-danger border-0 shadow-sm">

                <i class="fa-solid fa-circle-exclamation me-2"></i>

                <?= htmlspecialchars($errorMessage) ?>

            </div>

        <?php endif; ?>


        <div class="row g-4">


            <!-- Personal Information -->
            <div class="col-lg-7">

                <div class="profile-card">

                    <div class="profile-card-header">

                        <h5 class="section-title mb-1">

                            <i class="fa-solid fa-user-pen text-primary me-2"></i>

                            Personal Information

                        </h5>

                        <small class="text-muted">

                            Update your counsellor profile information.

                        </small>

                    </div>


                    <div class="profile-card-body">

                        <form method="POST">

                            <!-- Full Name -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Full Name
                                </label>

                                <input
                                    type="text"
                                    name="fullname"
                                    class="form-control"
                                    maxlength="100"
                                    value="<?= htmlspecialchars($profile['fullname']) ?>"
                                    required
                                >

                            </div>


                            <!-- Email -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    class="form-control readonly-box"
                                    value="<?= htmlspecialchars($profile['email']) ?>"
                                    readonly
                                >

                                <div class="form-text">
                                    Email is used for login and cannot be changed here.
                                </div>

                            </div>


                            <div class="row">

                                <!-- Phone -->
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Phone
                                    </label>

                                    <input
                                        type="text"
                                        name="phone"
                                        class="form-control"
                                        maxlength="20"
                                        value="<?= htmlspecialchars($profile['phone'] ?? '') ?>"
                                        placeholder="Enter phone number"
                                    >

                                </div>


                                <!-- Specialization -->
                                <div class="col-md-6 mb-3">

                                    <label class="form-label">
                                        Specialization
                                    </label>

                                    <input
                                        type="text"
                                        name="specialization"
                                        class="form-control"
                                        maxlength="100"
                                        value="<?= htmlspecialchars($profile['specialization'] ?? '') ?>"
                                        placeholder="e.g. Student Counselling"
                                    >

                                </div>

                            </div>


                            <!-- Department -->
                            <div class="mb-4">

                                <label class="form-label">
                                    Department
                                </label>

                                <input
                                    type="text"
                                    class="form-control readonly-box"
                                    value="<?= htmlspecialchars($profile['department_name'] ?? 'Not assigned') ?>"
                                    readonly
                                >

                                <div class="form-text">
                                    Department assignment is managed by the administrator.
                                </div>

                            </div>


                            <button
                                type="submit"
                                name="update_profile"
                                class="btn btn-primary"
                            >

                                <i class="fa-solid fa-save me-2"></i>

                                Save Changes

                            </button>

                        </form>

                    </div>

                </div>

            </div>


            <!-- Account Information -->
            <div class="col-lg-5">

                <div class="profile-card">

                    <div class="profile-card-header">

                        <h5 class="section-title mb-1">

                            <i class="fa-solid fa-shield-halved text-success me-2"></i>

                            Account Information

                        </h5>

                        <small class="text-muted">

                            Your account and access information.

                        </small>

                    </div>


                    <div class="profile-card-body">

                        <div class="info-row">

                            <div class="info-label">
                                Account Status
                            </div>

                            <div class="info-value">

                                <?php if ($profile['status'] === 'Active'): ?>

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                <?php else: ?>

                                    <span class="badge bg-secondary">
                                        <?= htmlspecialchars($profile['status']) ?>
                                    </span>

                                <?php endif; ?>

                            </div>

                        </div>


                        <div class="info-row">

                            <div class="info-label">
                                Account Role
                            </div>

                            <div class="info-value">
                                Counsellor
                            </div>

                        </div>


                        <div class="info-row">

                            <div class="info-label">
                                Counsellor ID
                            </div>

                            <div class="info-value">
                                <?= $counsellorId ?>
                            </div>

                        </div>


                        <div class="info-row">

                            <div class="info-label">
                                Login Email
                            </div>

                            <div class="info-value">
                                <?= htmlspecialchars($profile['email']) ?>
                            </div>

                        </div>


                        <div class="info-row">

                            <div class="info-label">
                                Member Since
                            </div>

                            <div class="info-value">

                                <?= date(
                                    "d M Y",
                                    strtotime($profile['created_at'])
                                ) ?>

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Change Password -->
            <div class="col-lg-7">

                <div class="profile-card">

                    <div class="profile-card-header">

                        <h5 class="section-title mb-1">

                            <i class="fa-solid fa-lock text-primary me-2"></i>

                            Change Password

                        </h5>

                        <small class="text-muted">

                            Use a strong password that you do not use elsewhere.

                        </small>

                    </div>


                    <div class="profile-card-body">

                        <form method="POST">

                            <!-- Current Password -->
                            <div class="mb-3">

                                <label class="form-label">
                                    Current Password
                                </label>

                                <input
                                    type="password"
                                    name="current_password"
                                    class="form-control"
                                    required
                                >

                            </div>


                            <!-- New Password -->
                            <div class="mb-3">

                                <label class="form-label">
                                    New Password
                                </label>

                                <input
                                    type="password"
                                    name="new_password"
                                    class="form-control"
                                    minlength="6"
                                    required
                                >

                                <div class="form-text">
                                    Minimum 6 characters.
                                </div>

                            </div>


                            <!-- Confirm -->
                            <div class="mb-4">

                                <label class="form-label">
                                    Confirm New Password
                                </label>

                                <input
                                    type="password"
                                    name="confirm_password"
                                    class="form-control"
                                    minlength="6"
                                    required
                                >

                            </div>


                            <button
                                type="submit"
                                name="change_password"
                                class="btn btn-outline-primary"
                            >

                                <i class="fa-solid fa-key me-2"></i>

                                Change Password

                            </button>

                        </form>

                    </div>

                </div>

            </div>


            <!-- Privacy -->
            <div class="col-lg-5">

                <div class="profile-card">

                    <div class="profile-card-header">

                        <h5 class="section-title mb-1">

                            <i class="fa-solid fa-user-shield text-success me-2"></i>

                            Privacy & Security

                        </h5>

                    </div>

                    <div class="profile-card-body">

                        <div class="d-flex gap-3">

                            <i class="fa-solid fa-shield-halved text-success fs-4"></i>

                            <div>

                                <h6 class="fw-bold mb-2">
                                    Confidential Records
                                </h6>

                                <p class="text-muted small mb-0">

                                    Counselling notes are restricted to authorised
                                    counsellor access. Your profile page does not
                                    expose confidential records to mentors or students.

                                </p>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php

include "../includes/footer.php";

?>
