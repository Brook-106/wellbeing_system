<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$stmt = $conn->query("
SELECT *
FROM departments
ORDER BY department_name
");

$departments = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">Departments</h2>

<div class="mb-3">
    <a href="add_department.php" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Add Department
    </a>
</div>

<div class="card shadow">

    <div class="card-body">

        <table class="table table-bordered table-hover">

            <thead class="table-dark">

                <tr>

                    <th>ID</th>
                    <th>Department</th>
                    <th>Code</th>
                    <th>HOD</th>
                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($departments)>0): ?>

                <?php foreach($departments as $row): ?>

                <tr>

                    <td><?= $row['id']; ?></td>

                    <td><?= htmlspecialchars($row['department_name']); ?></td>

                    <td><?= htmlspecialchars($row['department_code']); ?></td>

                    <td><?= htmlspecialchars($row['hod_name']); ?></td>

                    <td>

                        <a
                        href="edit_department.php?id=<?= $row['id']; ?>"
                        class="btn btn-warning btn-sm">

                        Edit

                        </a>

                        <a
                        href="delete_department.php?id=<?= $row['id']; ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return confirm('Delete this department?')">

                        Delete

                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="5" class="text-center text-danger">

                        No departments found.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "../includes/footer.php"; ?>