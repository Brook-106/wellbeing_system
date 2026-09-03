<?php
require_once "../includes/session.php";

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">
    <i class="fa-solid fa-gear"></i>
    Settings
</h2>
<?php if(isset($_GET['password'])): ?>

<div class="alert alert-success alert-dismissible fade show">

    <strong>Success!</strong> Password changed successfully.

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>

<?php endif; ?>
<div class="row">

    <div class="col-md-6 col-lg-3 mb-4">

        <div class="card shadow h-100">

            <div class="card-body text-center">

                <i class="fa-solid fa-sliders fa-3x text-primary mb-3"></i>

                <h5>General Settings</h5>

                <p class="text-muted">
                    System name, college and email.
                </p>

                <a href="general_settings.php" class="btn btn-primary w-100">
                    Open
                </a>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-lg-3 mb-4">

        <div class="card shadow h-100">

            <div class="card-body text-center">

                <i class="fa-solid fa-lock fa-3x text-warning mb-3"></i>

                <h5>Password</h5>

                <p class="text-muted">
                    Change administrator password.
                </p>

                <a href="change_password.php" class="btn btn-warning w-100">
                    Open
                </a>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-lg-3 mb-4">

        <div class="card shadow h-100">

            <div class="card-body text-center">

                <i class="fa-solid fa-user fa-3x text-success mb-3"></i>

                <h5>My Profile</h5>

                <p class="text-muted">
                    Update your profile information.
                </p>

                <a href="profile.php" class="btn btn-success w-100">
                    Open
                </a>

            </div>

        </div>

    </div>

    <div class="col-md-6 col-lg-3 mb-4">

        <div class="card shadow h-100">

            <div class="card-body text-center">

                <i class="fa-solid fa-database fa-3x text-danger mb-3"></i>

                <h5>Backup</h5>

                <p class="text-muted">
                    Backup and restore database.
                </p>

                <a href="backup.php" class="btn btn-danger w-100">
                    Open
                </a>

            </div>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>