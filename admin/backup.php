<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}

$backupFile = '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $backupDir = sys_get_temp_dir();
    $backupFile = $backupDir . '/wellbeing_system_backup_' . date('Y-m-d_H-i-s') . '.sql';

    $host = DB_HOST;
    $user = DB_USER;
    $pass = DB_PASS;
    $database = DB_NAME;

    $mysqldump = '/opt/lampp/bin/mysqldump';

    if (!file_exists($mysqldump)) {
        $error = "mysqldump was not found at $mysqldump.";
    } else {

        $command = escapeshellarg($mysqldump)
            . ' --host=' . escapeshellarg($host)
            . ' --user=' . escapeshellarg($user);

        if ($pass !== '') {
            $command .= ' --password=' . escapeshellarg($pass);
        }

        $command .= ' ' . escapeshellarg($database)
            . ' > ' . escapeshellarg($backupFile)
            . ' 2>&1';

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && file_exists($backupFile) && filesize($backupFile) > 0) {

            $downloadName = 'wellbeing_system_backup_' . date('Y-m-d_H-i-s') . '.sql';

            header('Content-Type: application/sql');
            header('Content-Disposition: attachment; filename="' . $downloadName . '"');
            header('Content-Length: ' . filesize($backupFile));
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($backupFile);

            unlink($backupFile);
            exit();

        } else {

            $error = "Database backup failed.";

            if (!empty($output)) {
                $error .= " " . implode(" ", $output);
            }

            if (file_exists($backupFile)) {
                unlink($backupFile);
            }
        }
    }
}

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">
                <i class="fa-solid fa-database me-2"></i>
                Database Backup
            </h2>
            <p class="text-muted mb-0">
                Create a backup of the Wellbeing Management System database.
            </p>
        </div>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i>
            <?= htmlspecialchars($error); ?>
            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="alert">
            </button>
        </div>
    <?php endif; ?>

    <div class="row g-4">

        <div class="col-lg-8">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <div class="mb-3">
                            <i class="fa-solid fa-database"
                               style="font-size:70px; color:#dc3545;">
                            </i>
                        </div>

                        <h4 class="fw-bold">
                            Backup Database
                        </h4>

                        <p class="text-muted">
                            This will create a complete SQL backup of the
                            <strong><?= htmlspecialchars(DB_NAME); ?></strong>
                            database.
                        </p>

                    </div>

                    <div class="alert alert-warning">

                        <i class="fa-solid fa-triangle-exclamation me-2"></i>

                        <strong>Important:</strong>

                        The backup may contain user accounts,
                        appointments, referrals and other system data.
                        Store the downloaded SQL file securely.

                    </div>

                    <form method="POST">

                        <div class="d-grid">

                            <button type="submit"
                                    class="btn btn-danger btn-lg"
                                    onclick="return confirm('Create a database backup now?');">

                                <i class="fa-solid fa-download me-2"></i>

                                Create & Download Backup

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>


        <div class="col-lg-4">

            <div class="card shadow-sm">

                <div class="card-body p-4">

                    <h5 class="fw-bold mb-3">
                        <i class="fa-solid fa-circle-info me-2"></i>
                        Backup Information
                    </h5>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Database
                        </small>

                        <strong>
                            <?= htmlspecialchars(DB_NAME); ?>
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Backup Format
                        </small>

                        <strong>
                            SQL (.sql)
                        </strong>
                    </div>

                    <div class="mb-3">
                        <small class="text-muted d-block">
                            Backup Tool
                        </small>

                        <strong>
                            MySQL mysqldump
                        </strong>
                    </div>

                    <hr>

                    <p class="text-muted small mb-0">
                        The backup contains the database structure and
                        stored data. It can later be restored using
                        MySQL tools.
                    </p>

                </div>

            </div>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>
