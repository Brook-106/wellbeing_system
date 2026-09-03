<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: departments.php");
    exit();
}

$id = $_GET['id'];

$stmt = $conn->prepare("
SELECT *
FROM departments
WHERE id = ?
");

$stmt->execute([$id]);

$department = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$department) {
    header("Location: departments.php");
    exit();
}

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">Edit Department</h2>

<div class="card shadow">

    <div class="card-body">

        <form action="update_department.php" method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= $department['id']; ?>">

            <div class="mb-3">

                <label class="form-label">
                    Department Name
                </label>

                <input
                    type="text"
                    name="department_name"
                    class="form-control"
                    value="<?= htmlspecialchars($department['department_name']); ?>"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    Department Code
                </label>

                <input
                    type="text"
                    name="department_code"
                    class="form-control"
                    value="<?= htmlspecialchars($department['department_code']); ?>"
                    required>

            </div>

            <div class="mb-3">

                <label class="form-label">
                    HOD Name
                </label>

                <input
                    type="text"
                    name="hod_name"
                    class="form-control"
                    value="<?= htmlspecialchars($department['hod_name']); ?>">

            </div>

            <button class="btn btn-success">
                Update Department
            </button>

            <a href="departments.php" class="btn btn-secondary">
                Cancel
            </a>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>