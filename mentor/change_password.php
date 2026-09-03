<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<div class="card shadow">

    <div class="card-header bg-warning">

        <h3>

            <i class="fa-solid fa-key"></i>

            Change Password

        </h3>

    </div>

    <div class="card-body">

        <?php if(isset($_GET['success'])): ?>

        <div class="alert alert-success">

            Password changed successfully.

        </div>

        <?php endif; ?>

        <?php if(isset($_GET['error'])): ?>

        <div class="alert alert-danger">

            <?= htmlspecialchars($_GET['error']) ?>

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

            <button class="btn btn-warning">

                <i class="fa-solid fa-floppy-disk"></i>

                Update Password

            </button>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>