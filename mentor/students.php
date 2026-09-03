<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

/* Logged-in Mentor */

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT id
FROM mentors
WHERE user_id = ?
");

$stmt->execute([$userId]);

$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mentor) {
    die("Mentor not found.");
}

$mentorId = $mentor['id'];

/* Fetch Assigned Students */

$stmt = $conn->prepare("
SELECT
    s.id,
    u.fullname,
    s.register_no,
    u.email,
    d.department_name,
    c.class_name
FROM students s
JOIN users u ON s.user_id = u.id
LEFT JOIN departments d ON s.department_id = d.id
LEFT JOIN classes c ON s.class_id = c.id
WHERE s.mentor_id = ?
ORDER BY u.fullname
");

$stmt->execute([$mentorId]);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<h2 class="mb-4">
    <i class="fa-solid fa-user-graduate"></i>
    My Students
</h2>

<div class="card shadow">

    <div class="card-body">

        <table class="table table-hover">

            <thead class="table-primary">

                <tr>

                    <th>Name</th>
                    <th>Register No</th>
                    <th>Email</th>
                    <th>Department</th>
                    <th>Class</th>
                    <th width="120">Action</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($students)>0): ?>

                <?php foreach($students as $student): ?>

                <tr>

                    <td><?= htmlspecialchars($student['fullname']) ?></td>

                    <td><?= htmlspecialchars($student['register_no']) ?></td>

                    <td><?= htmlspecialchars($student['email']) ?></td>

                    <td><?= htmlspecialchars($student['department_name']) ?></td>

                    <td><?= htmlspecialchars($student['class_name']) ?></td>

                    <td>

                        <a href="view_student.php?id=<?= $student['id'] ?>"
                           class="btn btn-primary btn-sm">

                            <i class="fa-solid fa-eye"></i>

                            View

                        </a>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="6" class="text-center text-danger">

                        No students assigned.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "../includes/footer.php"; ?>