<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$stmt = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">System Settings</h2>

<div class="card shadow">

    <div class="card-body">

        <form action="update_settings.php" method="POST">

            <input type="hidden" name="id" value="<?= $settings['id']; ?>">

            <div class="mb-3">

                <label class="form-label">
                    System Name
                </label>

                <input
                    type="text"
                    name="system_name"
                    class="form-control"
                    value="<?= htmlspecialchars($settings['system_name']); ?>"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    College Name
                </label>

                <input
                    type="text"
                    name="college_name"
                    class="form-control"
                    value="<?= htmlspecialchars($settings['college_name']); ?>"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Admin Email
                </label>

                <input
                    type="email"
                    name="admin_email"
                    class="form-control"
                    value="<?= htmlspecialchars($settings['admin_email']); ?>"
                    required>

            </div>

            <button class="btn btn-success">
                Save Settings
            </button>

        </form>

    </div>

</div>

<hr class="my-5">

<div class="card shadow">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">Change Password</h5>

    </div>

    <div class="card-body">

        <a href="change_password.php" class="btn btn-warning">

            Change Password

        </a>

    </div>

</div>

<?php include "../includes/footer.php"; ?>