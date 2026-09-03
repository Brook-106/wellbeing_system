<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

// Dashboard Counts
$totalStudents = $conn->query("SELECT COUNT(*) FROM students")->fetchColumn();
$totalMentors = $conn->query("SELECT COUNT(*) FROM mentors")->fetchColumn();
$totalCounsellors = $conn->query("SELECT COUNT(*) FROM counsellors")->fetchColumn();
$totalReferrals = $conn->query("SELECT COUNT(*) FROM referrals")->fetchColumn();

// Latest 5 Referrals
$stmt = $conn->query("
SELECT
    su.fullname AS student_name,
    mu.fullname AS mentor_name,
    r.priority,
    r.status,
    r.referral_date
FROM referrals r
JOIN students s ON r.student_id = s.id
JOIN users su ON s.user_id = su.id
JOIN mentors m ON r.mentor_id = m.id
JOIN users mu ON m.user_id = mu.id
ORDER BY r.referral_date DESC
LIMIT 5
");
$recentReferrals = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Referral Status Counts
$pending = $conn->query("SELECT COUNT(*) FROM referrals WHERE status='Pending'")->fetchColumn();
$accepted = $conn->query("SELECT COUNT(*) FROM referrals WHERE status='Accepted'")->fetchColumn();
$rejected = $conn->query("SELECT COUNT(*) FROM referrals WHERE status='Rejected'")->fetchColumn();
$completed = $conn->query("SELECT COUNT(*) FROM referrals WHERE status='Completed'")->fetchColumn();

include "../includes/header.php";
include "../includes/admin_sidebar.php";
?>

<div class="card shadow border-0 mb-4">

    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center">

            <div>

                <h2 class="mb-1">
                    Welcome,
                    <?= htmlspecialchars($_SESSION['fullname']); ?>
                </h2>

                <p class="text-muted mb-0">
                    <?= ucfirst($_SESSION['role']); ?> Dashboard
                </p>

            </div>

            <div class="text-end">

                <h5><?= date("d M Y"); ?></h5>

                <small class="text-muted">
                    Student Wellbeing Management System
                </small>

            </div>

        </div>

    </div>

</div>

<!-- Quick Actions -->

<div class="card shadow-sm border-0 mb-4">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">
            <i class="fa-solid fa-bolt"></i>
            Quick Actions
        </h5>

    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-lg-3 col-md-6">

                <a href="add_student.php"
                   class="btn btn-primary w-100 p-3">

                    <i class="fa-solid fa-user-plus fa-2x mb-2"></i>

                    <br>

                    Add Student

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a href="add_mentor.php"
                   class="btn btn-success w-100 p-3">

                    <i class="fa-solid fa-chalkboard-user fa-2x mb-2"></i>

                    <br>

                    Add Mentor

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a href="add_counsellor.php"
                   class="btn btn-danger w-100 p-3">

                    <i class="fa-solid fa-user-doctor fa-2x mb-2"></i>

                    <br>

                    Add Counsellor

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a href="add_referral.php"
                   class="btn btn-warning w-100 p-3 text-dark">

                    <i class="fa-solid fa-share-nodes fa-2x mb-2"></i>

                    <br>

                    New Referral

                </a>

            </div>

        </div>

    </div>

</div>

<!-- Statistics Cards -->

<div class="row">

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Students
                        </small>

                        <h2><?= $totalStudents ?></h2>

                    </div>

                    <i class="fa-solid fa-user-graduate fa-2x text-primary"></i>

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Mentors
                        </small>

                        <h2><?= $totalMentors ?></h2>

                    </div>

                    <i class="fa-solid fa-chalkboard-teacher fa-2x text-success"></i>

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Counsellors
                        </small>

                        <h2><?= $totalCounsellors ?></h2>

                    </div>

                    <i class="fa-solid fa-user-doctor fa-2x text-danger"></i>

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card shadow-sm border-0">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center">

                    <div>

                        <small class="text-muted">
                            Referrals
                        </small>

                        <h2><?= $totalReferrals ?></h2>

                    </div>

                    <i class="fa-solid fa-share-nodes fa-2x text-warning"></i>

                </div>

            </div>

        </div>

    </div>

</div>
<!-- Referral Status Cards -->

<h3 class="mb-3">Referral Status</h3>

<div class="row">

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card border-warning shadow-sm">

            <div class="card-body text-center">

                <h2 class="text-warning"><?= $pending ?></h2>

                <p class="mb-0">Pending</p>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card border-primary shadow-sm">

            <div class="card-body text-center">

                <h2 class="text-primary"><?= $accepted ?></h2>

                <p class="mb-0">Accepted</p>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card border-danger shadow-sm">

            <div class="card-body text-center">

                <h2 class="text-danger"><?= $rejected ?></h2>

                <p class="mb-0">Rejected</p>

            </div>

        </div>

    </div>

    <div class="col-lg-3 col-md-6 mb-4">

        <div class="card border-success shadow-sm">

            <div class="card-body text-center">

                <h2 class="text-success"><?= $completed ?></h2>

                <p class="mb-0">Completed</p>

            </div>

        </div>

    </div>

</div>

<!-- Chart & Notifications -->

<div class="row mb-4">

    <!-- Chart -->

    <div class="col-lg-5 mb-4">

        <div class="card shadow-sm h-100">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">
                    <i class="fa-solid fa-chart-pie"></i>
                    Referral Status Overview
                </h5>

            </div>

            <div class="card-body d-flex justify-content-center align-items-center">

                <div style="width:250px;height:250px;">

                    <canvas id="statusChart"></canvas>

                </div>

            </div>

        </div>

    </div>

    <!-- Notifications -->

    <div class="col-lg-7 mb-4">

        <div class="card shadow-sm h-100">

            <div class="card-header bg-dark text-white">

                <h5 class="mb-0">
                    <i class="fa-solid fa-bell"></i>
                    Notifications
                </h5>

            </div>

            <div class="card-body">

                <div class="alert alert-warning d-flex justify-content-between">

                    <span>
                        <i class="fa-solid fa-clock"></i>
                        Pending Referrals
                    </span>

                    <strong><?= $pending ?></strong>

                </div>

                <div class="alert alert-primary d-flex justify-content-between">

                    <span>
                        <i class="fa-solid fa-circle-check"></i>
                        Accepted Referrals
                    </span>

                    <strong><?= $accepted ?></strong>

                </div>

                <div class="alert alert-danger d-flex justify-content-between">

                    <span>
                        <i class="fa-solid fa-circle-xmark"></i>
                        Rejected Referrals
                    </span>

                    <strong><?= $rejected ?></strong>

                </div>

                <div class="alert alert-success d-flex justify-content-between">

                    <span>
                        <i class="fa-solid fa-check-double"></i>
                        Completed Referrals
                    </span>

                    <strong><?= $completed ?></strong>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- System Information -->

<div class="card shadow-sm mb-4">

    <div class="card-header bg-secondary text-white">

        <h5 class="mb-0">
            <i class="fa-solid fa-server"></i>
            System Information
        </h5>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3 mb-3">

                <div class="border rounded p-3 text-center">

                    <i class="fa-solid fa-user fa-2x text-primary mb-2"></i>

                    <h6>Logged-in User</h6>

                    <strong><?= htmlspecialchars($_SESSION['fullname']); ?></strong>

                </div>

            </div>

            <div class="col-md-3 mb-3">

                <div class="border rounded p-3 text-center">

                    <i class="fa-solid fa-user-shield fa-2x text-success mb-2"></i>

                    <h6>Role</h6>

                    <strong><?= ucfirst($_SESSION['role']); ?></strong>

                </div>

            </div>

            <div class="col-md-3 mb-3">

                <div class="border rounded p-3 text-center">

                    <i class="fa-solid fa-database fa-2x text-warning mb-2"></i>

                    <h6>Database</h6>

                    <span class="badge bg-success">
                        Connected
                    </span>

                </div>

            </div>

            <div class="col-md-3 mb-3">

                <div class="border rounded p-3 text-center">

                    <i class="fa-solid fa-calendar-days fa-2x text-danger mb-2"></i>

                    <h6>Today</h6>

                    <strong><?= date("d M Y"); ?></strong>

                </div>

            </div>

        </div>

    </div>

</div>

<!-- Recent Referrals -->

<div class="card shadow-sm">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">Recent Referrals</h5>

    </div>

    <div class="card-body">

        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">

                <tr>

                    <th>Student</th>
                    <th>Mentor</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($recentReferrals)>0): ?>

                <?php foreach($recentReferrals as $row): ?>

                <tr>

                    <td><?= htmlspecialchars($row['student_name']) ?></td>

                    <td><?= htmlspecialchars($row['mentor_name']) ?></td>

                    <td>

                        <?php

                        if($row['priority']=="High"){

                            echo '<span class="badge bg-danger">High</span>';

                        }elseif($row['priority']=="Medium"){

                            echo '<span class="badge bg-warning text-dark">Medium</span>';

                        }else{

                            echo '<span class="badge bg-success">Low</span>';

                        }

                        ?>

                    </td>

                    <td>

                        <?php

                        if($row['status']=="Pending"){

                            echo '<span class="badge bg-warning text-dark">Pending</span>';

                        }elseif($row['status']=="Accepted"){

                            echo '<span class="badge bg-primary">Accepted</span>';

                        }elseif($row['status']=="Rejected"){

                            echo '<span class="badge bg-danger">Rejected</span>';

                        }else{

                            echo '<span class="badge bg-success">Completed</span>';

                        }

                        ?>

                    </td>

                    <td><?= date("d M Y", strtotime($row['referral_date'])) ?></td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="5" class="text-center text-danger">

                        No referrals found.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>
<script>

document.addEventListener("DOMContentLoaded", function () {

    const ctx = document.getElementById("statusChart");

    if (ctx) {

        new Chart(ctx, {

            type: "doughnut",

            data: {

                labels: [
                    "Pending",
                    "Accepted",
                    "Rejected",
                    "Completed"
                ],

                datasets: [{

                    data: [
                        <?= $pending ?>,
                        <?= $accepted ?>,
                        <?= $rejected ?>,
                        <?= $completed ?>
                    ],

                    backgroundColor: [
                        "#ffc107",
                        "#0d6efd",
                        "#dc3545",
                        "#198754"
                    ],

                    borderColor: "#ffffff",
                    borderWidth: 2,
                    hoverOffset: 10

                }]

            },

            options: {

                responsive: true,
                maintainAspectRatio: false,

                cutout: "65%",

                plugins: {

                    legend: {

                        position: "bottom",

                        labels: {

                            boxWidth: 15,
                            padding: 20,
                            font: {
                                size: 13
                            }

                        }

                    },

                    tooltip: {

                        enabled: true

                    }

                }

            }

        });

    }

});

</script>

<?php include "../includes/footer.php"; ?>