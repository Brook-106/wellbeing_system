<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../includes/session.php";
include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">
    <i class="fa-solid fa-lock"></i> Change Password
</h2>

<div class="card shadow">

    <div class="card-body">

        <?php if(isset($_GET['success'])): ?>

            <div class="alert alert-success">
                Password changed successfully.
            </div>

        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>

            <div class="alert alert-danger">
                <?= htmlspecialchars($_GET['error']); ?>
            </div>

        <?php endif; ?>

        <form action="update_password.php" method="POST">

            <div class="mb-3">

                <label class="form-label">
                    Current Password
                </label>

                <input
                    type="password"
                    name="current_password"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    New Password
                </label>

                <input
                    type="password"
                    name="new_password"
                    class="form-control"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Confirm Password
                </label>

                <input
                    type="password"
                    name="confirm_password"
                    class="form-control"
                    required>

            </div>

            <button class="btn btn-success">

                <i class="fa-solid fa-key"></i>
                Change Password

            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>