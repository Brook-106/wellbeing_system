<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Fetch counsellors
$sql = "
SELECT
    c.id,
    u.fullname,
    u.email,
    c.phone,
    c.specialization
FROM counsellors c
JOIN users u
ON c.user_id = u.id
ORDER BY c.id DESC
";

$stmt = $conn->query($sql);
$counsellors = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<div class="d-flex justify-content-between align-items-center mb-4">

    <h2>Counsellors</h2>

    <a href="add_counsellor.php" class="btn btn-primary">
        + Add Counsellor
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
                    <th>Specialization</th>
                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                <?php if (count($counsellors) > 0): ?>

                    <?php foreach ($counsellors as $c): ?>

                        <tr>

                            <td><?= $c['id']; ?></td>

                            <td><?= htmlspecialchars($c['fullname']); ?></td>

                            <td><?= htmlspecialchars($c['email']); ?></td>

                            <td><?= htmlspecialchars($c['phone']); ?></td>

                            <td>
                                <?= !empty($c['specialization']) ? htmlspecialchars($c['specialization']) : '<span class="text-muted">Not Assigned</span>'; ?>
                            </td>

                            <td>

                                <a href="edit_counsellor.php?id=<?= $c['id']; ?>" class="btn btn-warning btn-sm">
                                    Edit
                                </a>

                                <a href="delete_counsellor.php?id=<?= $c['id']; ?>"
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete this counsellor?');">
                                    Delete
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>

                        <td colspan="6" class="text-center text-danger">
                            No counsellors found
                        </td>

                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "../includes/footer.php"; ?>