<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

$status = $_GET['status'] ?? '';
$priority = $_GET['priority'] ?? '';

$sql = "
SELECT
    r.id,
    su.fullname AS student_name,
    mu.fullname AS mentor_name,
    cu.fullname AS counsellor_name,
    r.priority,
    r.status,
    r.referral_date
FROM referrals r
JOIN students s ON r.student_id = s.id
JOIN users su ON s.user_id = su.id
JOIN mentors m ON r.mentor_id = m.id
JOIN users mu ON m.user_id = mu.id
LEFT JOIN counsellors c ON r.counsellor_id = c.id
LEFT JOIN users cu ON c.user_id = cu.id
WHERE 1=1
";

$params = [];

if($status != ""){
    $sql .= " AND r.status = ?";
    $params[] = $status;
}

if($priority != ""){
    $sql .= " AND r.priority = ?";
    $params[] = $priority;
}

$sql .= " ORDER BY r.referral_date DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);

$reports = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<h2 class="mb-4">Reports</h2>

<div class="card shadow">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-4">

                    <select name="status" class="form-control">

                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Accepted">Accepted</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Completed">Completed</option>

                    </select>

                </div>

                <div class="col-md-4">

                    <select name="priority" class="form-control">

                        <option value="">All Priority</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>

                    </select>

                </div>

                <div class="col-md-4">

                    <button class="btn btn-primary w-100">

                        Filter

                    </button>

                </div>

            </div>

        </form>

        <!-- Export Buttons -->

        <div class="mt-3">

            <a href="print_report.php" class="btn btn-secondary">
                🖨 Print
            </a>

            <a href="export_pdf.php" class="btn btn-danger">
                📄 Export PDF
            </a>

            <a href="export_excel.php" class="btn btn-success">
                📊 Export Excel
            </a>

        </div>

    </div>

</div>

<div class="card shadow mt-4">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">Referral Report</h5>

    </div>

    <div class="card-body">

        <table class="table table-bordered table-hover">

            <thead class="table-dark">

                <tr>

                    <th>Student</th>
                    <th>Mentor</th>
                    <th>Counsellor</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($reports) > 0): ?>

                <?php foreach($reports as $row): ?>

                <tr>

                    <td><?= htmlspecialchars($row['student_name']); ?></td>

                    <td><?= htmlspecialchars($row['mentor_name']); ?></td>

                    <td><?= htmlspecialchars($row['counsellor_name'] ?? 'Not Assigned'); ?></td>

                    <td><?= htmlspecialchars($row['priority']); ?></td>

                    <td><?= htmlspecialchars($row['status']); ?></td>

                    <td><?= date('d M Y', strtotime($row['referral_date'])); ?></td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="6" class="text-center text-danger">

                        No records found.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php include "../includes/footer.php"; ?>