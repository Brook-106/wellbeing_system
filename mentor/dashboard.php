<?php
require_once "../includes/session.php";
require_once "../includes/db.php";

/* =====================================================
   Logged-in Mentor
===================================================== */

$userId = $_SESSION['user_id'];

$stmt = $conn->prepare("
SELECT id
FROM mentors
WHERE user_id = ?
");

$stmt->execute([$userId]);

$mentor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$mentor) {
    die("Mentor record not found.");
}

$mentorId = $mentor['id'];


/* =====================================================
   Dashboard Statistics
===================================================== */

// Total Students
$stmt = $conn->prepare("
SELECT COUNT(*)
FROM students
WHERE mentor_id = ?
");

$stmt->execute([$mentorId]);
$totalStudents = $stmt->fetchColumn();


// Pending Referrals
$stmt = $conn->prepare("
SELECT COUNT(*)
FROM referrals
WHERE mentor_id = ?
AND status='Pending'
");

$stmt->execute([$mentorId]);
$pending = $stmt->fetchColumn();


// Accepted Referrals
$stmt = $conn->prepare("
SELECT COUNT(*)
FROM referrals
WHERE mentor_id = ?
AND status='Accepted'
");

$stmt->execute([$mentorId]);
$accepted = $stmt->fetchColumn();


// Completed Referrals
$stmt = $conn->prepare("
SELECT COUNT(*)
FROM referrals
WHERE mentor_id = ?
AND status='Completed'
");

$stmt->execute([$mentorId]);
$completed = $stmt->fetchColumn();


/* =====================================================
   Assigned Students
===================================================== */

$stmt = $conn->prepare("
SELECT
    s.id,
    u.fullname,
    s.register_no,
    d.department_name,
    c.class_name
FROM students s

JOIN users u
ON s.user_id = u.id

LEFT JOIN departments d
ON s.department_id = d.id

LEFT JOIN classes c
ON s.class_id = c.id

WHERE s.mentor_id = ?

ORDER BY u.fullname
");

$stmt->execute([$mentorId]);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =====================================================
   Recent Referrals
===================================================== */

$stmt = $conn->prepare("
SELECT
    u.fullname AS student_name,
    r.priority,
    r.status,
    r.referral_date

FROM referrals r

JOIN students s
ON r.student_id = s.id

JOIN users u
ON s.user_id = u.id

WHERE r.mentor_id = ?

ORDER BY r.referral_date DESC

LIMIT 5
");

$stmt->execute([$mentorId]);

$recentReferrals = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =====================================================
   Referral Status Chart
===================================================== */

$stmt = $conn->prepare("
SELECT
status,
COUNT(*) AS total

FROM referrals

WHERE mentor_id = ?

GROUP BY status
");

$stmt->execute([$mentorId]);

$statusData = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =====================================================
   Monthly Referral Chart
===================================================== */

$stmt = $conn->prepare("
SELECT
MONTH(referral_date) AS month,
COUNT(*) AS total

FROM referrals

WHERE mentor_id = ?

GROUP BY MONTH(referral_date)

ORDER BY MONTH(referral_date)
");

$stmt->execute([$mentorId]);

$monthlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =====================================================
   Layout
===================================================== */

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<!-- ===================================================
     Welcome Card
=================================================== -->

<div class="card shadow-lg border-0 mb-4">

    <div class="card-body">

        <div class="row align-items-center">

            <div class="col-lg-8">

                <h2 class="fw-bold">

                    👋 Welcome Back,
                    <?= htmlspecialchars($_SESSION['fullname']) ?>

                </h2>

                <h5 class="text-primary">
                    Mentor Dashboard
                </h5>

                <p class="text-muted">

                    Manage your assigned students and referrals.

                </p>

                <p class="text-muted">

                    <?= date("l, d F Y"); ?>

                </p>

            </div>

            <div class="col-lg-4 text-center">

                <i class="fa-solid fa-chalkboard-user text-primary"
                   style="font-size:90px;"></i>

            </div>

        </div>

    </div>

</div>


<!-- ===================================================
     Statistics Cards
=================================================== -->

<div class="row">

<div class="col-md-3 mb-4">

<div class="card shadow text-center">

<div class="card-body">

<i class="fa-solid fa-user-graduate fa-2x text-primary mb-3"></i>

<h2><?= $totalStudents ?></h2>

<h6>My Students</h6>

</div>

</div>

</div>



<div class="col-md-3 mb-4">

<div class="card shadow text-center">

<div class="card-body">

<i class="fa-solid fa-clock fa-2x text-warning mb-3"></i>

<h2><?= $pending ?></h2>

<h6>Pending</h6>

</div>

</div>

</div>



<div class="col-md-3 mb-4">

<div class="card shadow text-center">

<div class="card-body">

<i class="fa-solid fa-circle-check fa-2x text-primary mb-3"></i>

<h2><?= $accepted ?></h2>

<h6>Accepted</h6>

</div>

</div>

</div>



<div class="col-md-3 mb-4">

<div class="card shadow text-center">

<div class="card-body">

<i class="fa-solid fa-check-double fa-2x text-success mb-3"></i>

<h2><?= $completed ?></h2>

<h6>Completed</h6>

</div>

</div>

</div>

</div>

<!-- ===================================================
     Quick Actions
=================================================== -->

<div class="card shadow-sm mb-4">

    <div class="card-header bg-primary text-white">

        <h5 class="mb-0">
            <i class="fa-solid fa-bolt"></i>
            Quick Actions
        </h5>

    </div>

    <div class="card-body">

        <div class="row g-3">

            <div class="col-lg-3 col-md-6">

                <a href="create_referral.php"
                   class="btn btn-warning w-100 py-3">

                    <i class="fa-solid fa-share-nodes fa-2x mb-2"></i>

                    <br>

                    Create Referral

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a href="students.php"
                   class="btn btn-primary w-100 py-3">

                    <i class="fa-solid fa-user-graduate fa-2x mb-2"></i>

                    <br>

                    My Students

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a href="referrals.php"
                   class="btn btn-success w-100 py-3">

                    <i class="fa-solid fa-list-check fa-2x mb-2"></i>

                    <br>

                    My Referrals

                </a>

            </div>

            <div class="col-lg-3 col-md-6">

                <a href="profile.php"
                   class="btn btn-secondary w-100 py-3">

                    <i class="fa-solid fa-user fa-2x mb-2"></i>

                    <br>

                    My Profile

                </a>

            </div>

        </div>

    </div>

</div>


<!-- ===================================================
     Charts
=================================================== -->

<div class="row mb-4">

<div class="col-lg-6">

<div class="card shadow h-100">

<div class="card-header bg-primary text-white">

<i class="fa-solid fa-chart-pie"></i>

Referral Status

</div>

<div class="card-body d-flex justify-content-center align-items-center">

<div style="width:280px;height:280px;">

<canvas id="statusChart"></canvas>

</div>

</div>

</div>

</div>


<div class="col-lg-6">

<div class="card shadow h-100">

<div class="card-header bg-success text-white">

<i class="fa-solid fa-chart-column"></i>

Monthly Referrals

</div>

<div class="card-body">

<div style="height:280px;">

<canvas id="monthlyChart"></canvas>

</div>

</div>

</div>

</div>

</div>



<!-- ===================================================
     Assigned Students
=================================================== -->

<div class="card shadow mb-4">

<div class="card-header bg-success text-white">

<h5 class="mb-0">

<i class="fa-solid fa-user-group"></i>

My Assigned Students

</h5>

</div>

<div class="card-body">

<table class="table table-hover table-bordered">

<thead class="table-light">

<tr>

<th>ID</th>

<th>Name</th>

<th>Register No</th>

<th>Department</th>

<th>Class</th>

<th width="120">

Action

</th>

</tr>

</thead>

<tbody>

<?php if(count($students)>0): ?>

<?php foreach($students as $student): ?>

<tr>

<td><?= $student['id']; ?></td>

<td><?= htmlspecialchars($student['fullname']); ?></td>

<td><?= htmlspecialchars($student['register_no']); ?></td>

<td><?= htmlspecialchars($student['department_name']); ?></td>

<td><?= htmlspecialchars($student['class_name']); ?></td>

<td>

<a href="view_student.php?id=<?= $student['id']; ?>"

class="btn btn-primary btn-sm">

<i class="fa fa-eye"></i>

View

</a>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="6"

class="text-center text-danger">

No students assigned.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>



<!-- ===================================================
     Recent Referrals
=================================================== -->

<div class="card shadow mb-4">

<div class="card-header bg-info text-white">

<h5 class="mb-0">

<i class="fa-solid fa-list-check"></i>

Recent Referrals

</h5>

</div>

<div class="card-body">

<table class="table table-hover table-bordered">

<thead class="table-light">

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

<td><?= htmlspecialchars($row['student_name']); ?></td>

<td>

<?php

switch($row['priority']){

case 'High':

echo '<span class="badge bg-danger">High</span>';

break;

case 'Medium':

echo '<span class="badge bg-warning text-dark">Medium</span>';

break;

default:

echo '<span class="badge bg-success">Low</span>';

}

?>

</td>

<td>

<?php

switch($row['status']){

case 'Pending':

echo '<span class="badge bg-warning text-dark">Pending</span>';

break;

case 'Accepted':

echo '<span class="badge bg-primary">Accepted</span>';

break;

case 'Rejected':

echo '<span class="badge bg-danger">Rejected</span>';

break;

default:

echo '<span class="badge bg-success">Completed</span>';

}

?>

</td>

<td>

<?= date("d M Y",strtotime($row['referral_date'])); ?>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="4"

class="text-center text-danger">

No referrals found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

<script>

// ===========================
// Referral Status Chart
// ===========================

const statusLabels = [
<?php foreach($statusData as $row): ?>
"<?= $row['status']; ?>",
<?php endforeach; ?>
];

const statusValues = [
<?php foreach($statusData as $row): ?>
<?= $row['total']; ?>,
<?php endforeach; ?>
];

new Chart(document.getElementById("statusChart"),{

type:"doughnut",

data:{

labels:statusLabels,

datasets:[{

data:statusValues,

backgroundColor:[

"#FFC107",   // Pending

"#0D6EFD",   // Accepted

"#198754",   // Completed

"#DC3545"    // Rejected

],

borderColor:"#ffffff",

borderWidth:3,

hoverOffset:15

}]

},

options:{

responsive:true,

maintainAspectRatio:false,

cutout:"65%",

plugins:{

legend:{

position:"bottom",

labels:{

padding:20,

font:{

size:14

}

}

}

}

}

});



// ===========================
// Monthly Referral Chart
// ===========================

const months=[

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

const totals=[

<?php

foreach($monthlyData as $row){

echo $row['total'].",";

}

?>

];

new Chart(document.getElementById("monthlyChart"),{

type:"bar",

data:{

labels:months,

datasets:[{

label:"Referrals",

data:totals,

backgroundColor:"#198754",

borderRadius:10,

borderSkipped:false

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

beginAtZero:true,

ticks:{

stepSize:1

}

},

x:{

grid:{

display:false

}

}

}

}

});

</script>

<?php include "../includes/footer.php"; ?>