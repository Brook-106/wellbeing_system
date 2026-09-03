<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

$userId = $_SESSION['user_id'];

/* Get Mentor Details */

$stmt = $conn->prepare("
SELECT
    u.id,
    u.fullname,
    u.email,
    u.role,
    u.status,
    u.profile_image,
    m.phone,
    d.department_name
FROM users u
JOIN mentors m
ON u.id = m.user_id
LEFT JOIN departments d
ON m.department_id = d.id
WHERE u.id = ?
");

$stmt->execute([$userId]);

$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<div class="card shadow">

    <div class="card-header bg-primary text-white">

        <h3>

            <i class="fa-solid fa-user"></i>

            My Profile

        </h3>

    </div>

    <div class="card-body">

        <?php if(isset($_GET['success'])): ?>

        <div class="alert alert-success">

            Profile updated successfully.

        </div>

        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($_GET['error']) ?>

        </div>

        <?php endif; ?>

        <form action="update_profile.php" method="POST">

            <div class="row">

                <div class="col-md-6 text-center mb-4">

                    <?php if(!empty($mentor['profile_image'])): ?>

                        <img
                            src="../uploads/profile/<?= htmlspecialchars($mentor['profile_image']) ?>"
                            class="rounded-circle border"
                            width="150"
                            height="150">

                    <?php else: ?>

                        <i class="fa-solid fa-circle-user text-primary"
                           style="font-size:150px;"></i>

                    <?php endif; ?>

                </div>

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Full Name

                    </label>

                    <input
                        type="text"
                        name="fullname"
                        class="form-control"
                        value="<?= htmlspecialchars($mentor['fullname']) ?>"
                        required>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Email

                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($mentor['email']) ?>"
                        required>

                </div>

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Phone

                    </label>

                    <input
                        type="text"
                        name="phone"
                        class="form-control"
                        value="<?= htmlspecialchars($mentor['phone']) ?>">

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Department

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($mentor['department_name']) ?>"
                        readonly>

                </div>

            </div>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Role

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= ucfirst($mentor['role']) ?>"
                        readonly>

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">

                        Status

                    </label>

                    <input
                        type="text"
                        class="form-control"
                        value="<?= htmlspecialchars($mentor['status']) ?>"
                        readonly>

                </div>

            </div>

            <button class="btn btn-primary">

                <i class="fa-solid fa-floppy-disk"></i>

                Update Profile

            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>