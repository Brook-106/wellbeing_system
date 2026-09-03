<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

/* ==========================================
   Logged-in Counsellor
========================================== */

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT id
FROM counsellors
WHERE user_id = ?
");

$stmt->execute([$userId]);

$counsellor = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$counsellor){

    die("Counsellor record not found.");

}

$counsellorId = $counsellor['id'];


/* ==========================================
   Dashboard Statistics
========================================== */

// Pending Referrals

$stmt = $conn->prepare("
SELECT COUNT(*)
FROM referrals
WHERE counsellor_id=?
AND status='Pending'
");

$stmt->execute([$counsellorId]);

$pending = $stmt->fetchColumn();


// Accepted Referrals

$stmt = $conn->prepare("
SELECT COUNT(*)
FROM referrals
WHERE counsellor_id=?
AND status='Accepted'
");

$stmt->execute([$counsellorId]);

$accepted = $stmt->fetchColumn();


// Completed Sessions

$stmt = $conn->prepare("
SELECT COUNT(*)
FROM referrals
WHERE counsellor_id=?
AND status='Completed'
");

$stmt->execute([$counsellorId]);

$completed = $stmt->fetchColumn();


// Rejected Referrals

$stmt = $conn->prepare("
SELECT COUNT(*)
FROM referrals
WHERE counsellor_id=?
AND status='Rejected'
");

$stmt->execute([$counsellorId]);

$rejected = $stmt->fetchColumn();


/* ==========================================
   Recent Referrals
========================================== */

$stmt = $conn->prepare("
SELECT

r.id,

u.fullname,

s.register_no,

r.priority,

r.status,

r.referral_date

FROM referrals r

JOIN students s
ON r.student_id=s.id

JOIN users u
ON s.user_id=u.id

WHERE r.counsellor_id=?

ORDER BY r.referral_date DESC

LIMIT 5
");

$stmt->execute([$counsellorId]);

$recentReferrals = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================================
   Referral Status Chart
========================================== */

$stmt = $conn->prepare("
SELECT

status,

COUNT(*) total

FROM referrals

WHERE counsellor_id=?

GROUP BY status
");

$stmt->execute([$counsellorId]);

$statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================================
   Monthly Referral Chart
========================================== */

$stmt = $conn->prepare("
SELECT

MONTH(referral_date) month,

COUNT(*) total

FROM referrals

WHERE counsellor_id=?

GROUP BY MONTH(referral_date)

ORDER BY MONTH(referral_date)
");

$stmt->execute([$counsellorId]);

$monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* ==========================================
   Layout
========================================== */

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";

?>

<!-- ================= Welcome ================= -->

<div class="card shadow-lg border-0 mb-4">

    <div class="card-body">

        <div class="row align-items-center">

            <div class="col-md-8">

                <h2 class="fw-bold">

                    👋 Welcome Back,

                    <?= htmlspecialchars($_SESSION['fullname']) ?>

                </h2>

                <h5 class="text-primary">

                    Counsellor Dashboard

                </h5>

                <p class="text-muted">

                    <?= date("l, d F Y") ?>

                </p>

            </div>

            <div class="col-md-4 text-center">

                <i class="fa-solid fa-user-doctor fa-5x text-primary"></i>

            </div>

        </div>

    </div>

</div>

<!-- ================= Statistics ================= -->

<div class="row">

    <div class="col-md-3 mb-4">

        <div class="card shadow text-center">

            <div class="card-body">

                <i class="fa-solid fa-clock fa-2x text-warning mb-2"></i>

                <h2><?= $pending ?></h2>

                <h6>Pending</h6>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-4">

        <div class="card shadow text-center">

            <div class="card-body">

                <i class="fa-solid fa-circle-check fa-2x text-primary mb-2"></i>

                <h2><?= $accepted ?></h2>

                <h6>Accepted</h6>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-4">

        <div class="card shadow text-center">

            <div class="card-body">

                <i class="fa-solid fa-check-double fa-2x text-success mb-2"></i>

                <h2><?= $completed ?></h2>

                <h6>Completed</h6>

            </div>

        </div>

    </div>

    <div class="col-md-3 mb-4">

        <div class="card shadow text-center">

            <div class="card-body">

                <i class="fa-solid fa-ban fa-2x text-danger mb-2"></i>

                <h2><?= $rejected ?></h2>

                <h6>Rejected</h6>

            </div>

        </div>

    </div>

</div>

<!-- ================= Quick Actions ================= -->

<div class="card shadow mb-4">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">

            <i class="fa-solid fa-bolt"></i>

            Quick Actions

        </h5>

    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-md-3">

                <a href="appointments.php" class="btn btn-primary w-100">

                    <i class="fa-solid fa-calendar-check d-block mb-2"></i>

                    Appointments

                </a>

            </div>

            <div class="col-md-3">

                <a href="notes.php" class="btn btn-success w-100">

                    <i class="fa-solid fa-note-sticky d-block mb-2"></i>

                    Notes

                </a>

            </div>

            <div class="col-md-3">

                <a href="followups.php" class="btn btn-warning w-100">

                    <i class="fa-solid fa-user-clock d-block mb-2"></i>

                    Follow-ups

                </a>

            </div>

            <div class="col-md-3">

                <a href="reports.php" class="btn btn-info w-100">

                    <i class="fa-solid fa-chart-line d-block mb-2"></i>

                    Reports

                </a>

            </div>

        </div>

    </div>

</div>

<!-- ================= Charts ================= -->

<div class="row mb-4">

    <div class="col-lg-6">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                Referral Status

            </div>

            <div class="card-body">

                <canvas id="statusChart" height="220"></canvas>

            </div>

        </div>

    </div>

    <div class="col-lg-6">

        <div class="card shadow">

            <div class="card-header bg-success text-white">

                Monthly Referrals

            </div>

            <div class="card-body">

                <canvas id="monthlyChart" height="220"></canvas>

            </div>

        </div>

    </div>

</div>

<!-- ================= Recent Referrals ================= -->

<div class="card shadow mb-4">

    <div class="card-header bg-info text-white">

        <h5 class="mb-0">
            <i class="fa-solid fa-list"></i>
            Recent Referrals
        </h5>

    </div>

    <div class="card-body">

        <table class="table table-hover">

            <thead class="table-primary">

                <tr>

                    <th>Student</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Date</th>

                </tr>

            </thead>

            <tbody>

            <?php if(count($recentReferrals)>0): ?>

                <?php foreach($recentReferrals as $row): ?>

                <tr>

                    <td><?= htmlspecialchars($row['fullname']) ?></td>

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

                        }elseif($row['status']=="Completed"){

                            echo '<span class="badge bg-success">Completed</span>';

                        }else{

                            echo '<span class="badge bg-danger">Rejected</span>';

                        }

                        ?>

                    </td>

                    <td>

                        <?= date("d M Y",strtotime($row['referral_date'])) ?>

                    </td>

                </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td colspan="4" class="text-center text-danger">

                        No referrals found.

                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<script>

/* ---------- Pie Chart ---------- */

const statusLabels = [

<?php
foreach($statusData as $row){
    echo "'".$row['status']."',";
}
?>

];

const statusValues = [

<?php
foreach($statusData as $row){
    echo $row['total'].",";
}
?>

];

new Chart(document.getElementById('statusChart'),{

    type:'doughnut',

    data:{

        labels:statusLabels,

        datasets:[{

            data:statusValues,

            backgroundColor:[
                '#ffc107',
                '#0d6efd',
                '#198754',
                '#dc3545'
            ]

        }]

    },

    options:{

        responsive:true,

        maintainAspectRatio:false,

        plugins:{

            legend:{
                position:'bottom'
            }

        }

    }

});


/* ---------- Monthly Bar Chart ---------- */

const months = [

<?php

$monthNames=[
1=>"Jan",
2=>"Feb",
3=>"Mar",
4=>"Apr",
5=>"May",
6=>"Jun",
7=>"Jul",
8=>"Aug",
9=>"Sep",
10=>"Oct",
11=>"Nov",
12=>"Dec"
];

foreach($monthlyData as $row){

    echo "'".$monthNames[$row['month']]."',";

}

?>

];

const totals = [

<?php

foreach($monthlyData as $row){

    echo $row['total'].",";

}

?>

];

new Chart(document.getElementById('monthlyChart'),{

    type:'bar',

    data:{

        labels:months,

        datasets:[{

            label:'Referrals',

            data:totals,

            backgroundColor:'#198754',

            borderRadius:8

        }]

    },

    options:{

        responsive:true,

        maintainAspectRatio:false,

        plugins:{

            legend:{
                display:false
            }

        },

        scales:{

            y:{
                beginAtZero:true
            }

        }

    }

});

</script>

<?php include "../includes/footer.php"; ?>