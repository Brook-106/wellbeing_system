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

$successMessage = '';
$errorMessage = '';

/*
|--------------------------------------------------------------------------
| Update Profile
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {

    $fullname = trim($_POST['fullname'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $gender   = trim($_POST['gender'] ?? '');
    $dob      = trim($_POST['dob'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    if ($fullname === '') {

        $errorMessage = "Full name cannot be empty.";

    } else {

        $allowedGenders = ['Male', 'Female', 'Other', ''];

        if (!in_array($gender, $allowedGenders, true)) {

            $errorMessage = "Invalid gender selected.";

        } else {

            try {

                $conn->beginTransaction();

                /*
                 * Update users table
                 */
                $stmt = $conn->prepare("
                    UPDATE users
                    SET fullname = ?
                    WHERE id = ?
                ");

                $stmt->execute([
                    $fullname,
                    $userId
                ]);

                /*
                 * Update students table
                 */
                $stmt = $conn->prepare("
                    UPDATE students
                    SET
                        phone = ?,
                        gender = NULLIF(?, ''),
                        dob = NULLIF(?, ''),
                        address = ?
                    WHERE user_id = ?
                ");

                $stmt->execute([
                    $phone,
                    $gender,
                    $dob,
                    $address,
                    $userId
                ]);

                $conn->commit();

                /*
                 * Keep session name updated
                 */
                $_SESSION['fullname'] = $fullname;

                $successMessage = "Profile updated successfully.";

            } catch (PDOException $e) {

                if ($conn->inTransaction()) {
                    $conn->rollBack();
                }

                $errorMessage = "Unable to update profile. Please try again.";
            }
        }
    }
}

/*
|--------------------------------------------------------------------------
| Change Password
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {

    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword     = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($currentPassword === '' || $newPassword === '' || $confirmPassword === '') {

        $errorMessage = "Please fill in all password fields.";

    } elseif ($newPassword !== $confirmPassword) {

        $errorMessage = "New password and confirmation password do not match.";

    } elseif (strlen($newPassword) < 6) {

        $errorMessage = "New password must contain at least 6 characters.";

    } else {

        $stmt = $conn->prepare("
            SELECT password
            FROM users
            WHERE id = ?
            LIMIT 1
        ");

        $stmt->execute([$userId]);

        $userPassword = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$userPassword) {

            $errorMessage = "User account could not be found.";

        } elseif (!password_verify($currentPassword, $userPassword['password'])) {

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
| Get Current Student Profile
|--------------------------------------------------------------------------
*/
$stmt = $conn->prepare("
    SELECT
        s.id AS student_id,
        s.user_id,
        s.register_no,
        s.phone,
        s.gender,
        s.dob,
        s.address,

        u.fullname,
        u.email,
        u.profile_image,
        u.status,

        d.department_name,
        d.department_code,

        c.class_name,

        m.id AS mentor_id,
        mu.fullname AS mentor_name

    FROM students s

    INNER JOIN users u
        ON s.user_id = u.id

    LEFT JOIN departments d
        ON s.department_id = d.id

    LEFT JOIN classes c
        ON s.class_id = c.id

    LEFT JOIN mentors m
        ON s.mentor_id = m.id

    LEFT JOIN users mu
        ON m.user_id = mu.id

    WHERE s.user_id = ?

    LIMIT 1
");

$stmt->execute([$userId]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student profile not found.");
}

/*
|--------------------------------------------------------------------------
| Header + Sidebar
|--------------------------------------------------------------------------
*/
include "../includes/header.php";

if (file_exists("../includes/student_sidebar.php")) {
    include "../includes/student_sidebar.php";
}

?>

<style>

.profile-page {
    padding: 25px;
}

.page-title {
    font-weight: 700;
    color: #1f2937;
}

.page-subtitle {
    color: #6b7280;
}

.profile-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    overflow: hidden;
}

.profile-header {
    padding: 30px;
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
}

.profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: #e0e7ff;
    color: #4338ca;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: 700;
    flex-shrink: 0;
}

.profile-name {
    font-size: 22px;
    font-weight: 700;
    color: #1f2937;
}

.profile-email {
    color: #6b7280;
    font-size: 14px;
}

.status-badge {
    display: inline-block;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    background: #dcfce7;
    color: #15803d;
}

.info-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 22px;
    height: 100%;
}

.info-title {
    font-size: 16px;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 20px;
}

.info-item {
    padding: 12px 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: 0;
}

.info-label {
    color: #94a3b8;
    font-size: 12px;
    margin-bottom: 3px;
}

.info-value {
    color: #334155;
    font-size: 14px;
    font-weight: 500;
}

.form-card {
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 25px;
}

.form-label {
    font-weight: 600;
    font-size: 13px;
    color: #374151;
}

.readonly-field {
    background: #f8fafc !important;
}

.section-heading {
    font-size: 17px;
    font-weight: 700;
    color: #1f2937;
}

</style>

<div class="content">

    <div class="profile-page">

        <!-- Page Header -->
        <div class="mb-4">

            <h2 class="page-title mb-1">

                <i class="fa-solid fa-user me-2"></i>

                My Profile

            </h2>

            <p class="page-subtitle mb-0">

                View and manage your student profile information.

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


        <!-- Profile Header -->
        <div class="profile-card mb-4">

            <div class="profile-header">

                <div class="d-flex align-items-center gap-3">

                    <div class="profile-avatar">

                        <?php
                        echo strtoupper(
                            substr(
                                $student['fullname'],
                                0,
                                1
                            )
                        );
                        ?>

                    </div>

                    <div>

                        <div class="profile-name">

                            <?php
                            echo htmlspecialchars(
                                $student['fullname']
                            );
                            ?>

                        </div>

                        <div class="profile-email">

                            <?php
                            echo htmlspecialchars(
                                $student['email']
                            );
                            ?>

                        </div>

                        <span class="status-badge mt-2">

                            <i class="fa-solid fa-circle me-1"
                               style="font-size:7px;"></i>

                            <?php
                            echo htmlspecialchars(
                                $student['status']
                            );
                            ?>

                        </span>

                    </div>

                </div>

            </div>

        </div>


        <div class="row g-4">


            <!-- Student Information -->
            <div class="col-lg-5">

                <div class="info-card">

                    <div class="info-title">

                        <i class="fa-solid fa-id-card me-2 text-primary"></i>

                        Student Information

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            REGISTER NUMBER
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['register_no'] ?: 'Not provided'
                            );
                            ?>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            DEPARTMENT
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['department_name'] ?: 'Not assigned'
                            );
                            ?>

                            <?php if (!empty($student['department_code'])): ?>

                                <span class="text-muted">

                                    (<?php
                                    echo htmlspecialchars(
                                        $student['department_code']
                                    );
                                    ?>)

                                </span>

                            <?php endif; ?>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            CLASS
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['class_name'] ?: 'Not assigned'
                            );
                            ?>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            MENTOR
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['mentor_name'] ?: 'Not assigned'
                            );
                            ?>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            EMAIL
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['email']
                            );
                            ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- Edit Profile -->
            <div class="col-lg-7">

                <div class="form-card">

                    <div class="section-heading mb-4">

                        <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>

                        Edit Personal Information

                    </div>


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
                                value="<?php
                                echo htmlspecialchars(
                                    $student['fullname']
                                );
                                ?>"
                                maxlength="100"
                                required
                            >

                        </div>


                        <div class="row g-3">


                            <!-- Email -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    Email
                                </label>

                                <input
                                    type="email"
                                    class="form-control readonly-field"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $student['email']
                                    );
                                    ?>"
                                    readonly
                                >

                                <small class="text-muted">
                                    Email is used for login.
                                </small>

                            </div>


                            <!-- Phone -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    Phone
                                </label>

                                <input
                                    type="text"
                                    name="phone"
                                    class="form-control"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $student['phone'] ?? ''
                                    );
                                    ?>"
                                    maxlength="15"
                                >

                            </div>


                            <!-- Gender -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    Gender
                                </label>

                                <select
                                    name="gender"
                                    class="form-select"
                                >

                                    <option value="">
                                        Select Gender
                                    </option>

                                    <option value="Male"
                                        <?php
                                        echo $student['gender'] === 'Male'
                                            ? 'selected'
                                            : '';
                                        ?>>
                                        Male
                                    </option>

                                    <option value="Female"
                                        <?php
                                        echo $student['gender'] === 'Female'
                                            ? 'selected'
                                            : '';
                                        ?>>
                                        Female
                                    </option>

                                    <option value="Other"
                                        <?php
                                        echo $student['gender'] === 'Other'
                                            ? 'selected'
                                            : '';
                                        ?>>
                                        Other
                                    </option>

                                </select>

                            </div>


                            <!-- DOB -->
                            <div class="col-md-6">

                                <label class="form-label">
                                    Date of Birth
                                </label>

                                <input
                                    type="date"
                                    name="dob"
                                    class="form-control"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $student['dob'] ?? ''
                                    );
                                    ?>"
                                >

                            </div>


                            <!-- Address -->
                            <div class="col-12">

                                <label class="form-label">
                                    Address
                                </label>

                                <textarea
                                    name="address"
                                    class="form-control"
                                    rows="4"
                                    maxlength="1000"
                                ><?php
                                echo htmlspecialchars(
                                    $student['address'] ?? ''
                                );
                                ?></textarea>

                            </div>

                        </div>


                        <div class="text-end mt-4">

                            <button
                                type="submit"
                                name="update_profile"
                                class="btn btn-primary"
                            >

                                <i class="fa-solid fa-floppy-disk me-2"></i>

                                Save Changes

                            </button>

                        </div>

                    </form>

                </div>

            </div>


            <!-- Change Password -->
            <div class="col-lg-7">

                <div class="form-card">

                    <div class="section-heading mb-2">

                        <i class="fa-solid fa-lock me-2 text-primary"></i>

                        Change Password

                    </div>

                    <p class="text-muted small mb-4">

                        Use a strong password that you do not use for other
                        accounts.

                    </p>


                    <form method="POST">


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

                            <small class="text-muted">
                                Minimum 6 characters.
                            </small>

                        </div>


                        <div class="mb-3">

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


                        <div class="text-end mt-4">

                            <button
                                type="submit"
                                name="change_password"
                                class="btn btn-outline-primary"
                            >

                                <i class="fa-solid fa-key me-2"></i>

                                Change Password

                            </button>

                        </div>

                    </form>

                </div>

            </div>


            <!-- Account Information -->
            <div class="col-lg-5">

                <div class="info-card">

                    <div class="info-title">

                        <i class="fa-solid fa-shield-halved me-2 text-success"></i>

                        Account Information

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            ACCOUNT STATUS
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['status']
                            );
                            ?>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            ACCOUNT ROLE
                        </div>

                        <div class="info-value">
                            Student
                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            STUDENT ID
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['student_id']
                            );
                            ?>

                        </div>

                    </div>


                    <div class="info-item">

                        <div class="info-label">
                            LOGIN EMAIL
                        </div>

                        <div class="info-value">

                            <?php
                            echo htmlspecialchars(
                                $student['email']
                            );
                            ?>

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
