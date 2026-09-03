<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Fetch mentors
$sql = "
SELECT
    m.id,
    u.fullname,
    u.email,
    m.phone,
    d.department_name
FROM mentors m
JOIN users u ON m.user_id = u.id
LEFT JOIN departments d ON m.department_id = d.id
ORDER BY m.id DESC
";

$stmt = $conn->query($sql);
$mentors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Common layout
include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Mentors</h2>

    <a href="add_mentor.php" class="btn btn-primary">
        + Add Mentor
    </a>

</div>

<div class="card shadow">

    <div class="card-body">

        <table class="table table-bordered table-hover">

            <thead class="table-dark">

                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>

            </thead>

            <tbody>

            <?php if(count($mentors) > 0): ?>

                <?php foreach($mentors as $mentor): ?>

                <tr>

                    <td><?= $mentor['id']; ?></td>

                    <td><?= htmlspecialchars($mentor['fullname']); ?></td>

                    <td><?= htmlspecialchars($mentor['email']); ?></td>

                    <td><?= htmlspecialchars($mentor['phone']); ?></td>

                    <td><?= htmlspecialchars($mentor['department_name']); ?></td>

                    <td>

                        <a href="edit_mentor.php?id=<?= $mentor['id']; ?>" class="btn btn-warning btn-sm">
                            Edit
                        </a>

                        <a href="delete_mentor.php?id=<?= $mentor['id']; ?>"
                           class="btn btn-danger btn-sm"
                           onclick="return confirm('Delete this mentor?');">
                            Delete
                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>
                    <td colspan="6" class="text-center text-danger">
                        No mentors found
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "../includes/footer.php"; ?>