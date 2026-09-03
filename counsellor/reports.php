<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../includes/session.php";
require_once "../includes/db.php";

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) die("User session not found.");

$stmt = $conn->prepare("SELECT id FROM counsellors WHERE user_id = ?");
$stmt->execute([$userId]);
$counsellor = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$counsellor) die("Counsellor record not found.");
$counsellorId = (int)$counsellor['id'];

function countQuery(PDO $conn, string $sql, array $params): int {
    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

$totalReferrals = countQuery($conn, "SELECT COUNT(*) FROM referrals WHERE counsellor_id=?", [$counsellorId]);
$pendingReferrals = countQuery($conn, "SELECT COUNT(*) FROM referrals WHERE counsellor_id=? AND status='Pending'", [$counsellorId]);
$acceptedReferrals = countQuery($conn, "SELECT COUNT(*) FROM referrals WHERE counsellor_id=? AND status='Accepted'", [$counsellorId]);
$completedReferrals = countQuery($conn, "SELECT COUNT(*) FROM referrals WHERE counsellor_id=? AND status='Completed'", [$counsellorId]);
$rejectedReferrals = countQuery($conn, "SELECT COUNT(*) FROM referrals WHERE counsellor_id=? AND status='Rejected'", [$counsellorId]);
$totalAppointments = countQuery($conn, "SELECT COUNT(*) FROM appointments WHERE counsellor_id=?", [$counsellorId]);
$completedAppointments = countQuery($conn, "SELECT COUNT(*) FROM appointments WHERE counsellor_id=? AND status='Completed'", [$counsellorId]);
$pendingFollowups = countQuery($conn, "SELECT COUNT(*) FROM followups f JOIN appointments a ON f.appointment_id=a.id WHERE a.counsellor_id=? AND f.status='Pending'", [$counsellorId]);
$completedFollowups = countQuery($conn, "SELECT COUNT(*) FROM followups f JOIN appointments a ON f.appointment_id=a.id WHERE a.counsellor_id=? AND f.status='Completed'", [$counsellorId]);
$totalNotes = countQuery($conn, "SELECT COUNT(*) FROM counselling_notes WHERE counsellor_id=?", [$counsellorId]);

$stmt = $conn->prepare("SELECT status, COUNT(*) total FROM referrals WHERE counsellor_id=? GROUP BY status");
$stmt->execute([$counsellorId]);
$statusRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$statusLabels = array_column($statusRows, 'status');
$statusValues = array_map('intval', array_column($statusRows, 'total'));

$stmt = $conn->prepare("SELECT MONTH(appointment_date) month_number, COUNT(*) total FROM appointments WHERE counsellor_id=? AND YEAR(appointment_date)=YEAR(CURDATE()) GROUP BY MONTH(appointment_date) ORDER BY MONTH(appointment_date)");
$stmt->execute([$counsellorId]);
$monthlyRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
$monthNames = [1=>'Jan',2=>'Feb',3=>'Mar',4=>'Apr',5=>'May',6=>'Jun',7=>'Jul',8=>'Aug',9=>'Sep',10=>'Oct',11=>'Nov',12=>'Dec'];
$monthlyLabels=[]; $monthlyValues=[];
foreach($monthlyRows as $row){ $m=(int)$row['month_number']; if(isset($monthNames[$m])){ $monthlyLabels[]=$monthNames[$m]; $monthlyValues[]=(int)$row['total']; } }

$stmt = $conn->prepare("SELECT a.id,u.fullname,s.register_no,a.appointment_date,a.appointment_time,a.purpose,a.status FROM appointments a JOIN students s ON a.student_id=s.id JOIN users u ON s.user_id=u.id WHERE a.counsellor_id=? ORDER BY a.appointment_date DESC,a.appointment_time DESC LIMIT 10");
$stmt->execute([$counsellorId]);
$recentAppointments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->prepare("SELECT f.id,u.fullname,a.appointment_date,f.followup_date,f.status FROM followups f JOIN appointments a ON f.appointment_id=a.id JOIN students s ON a.student_id=s.id JOIN users u ON s.user_id=u.id WHERE a.counsellor_id=? ORDER BY f.followup_date DESC LIMIT 10");
$stmt->execute([$counsellorId]);
$recentFollowups = $stmt->fetchAll(PDO::FETCH_ASSOC);

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-1"><i class="fa-solid fa-chart-column text-primary"></i> Counsellor Reports</h2>
            <p class="text-muted mb-0">Overview of referrals, appointments, follow-ups and counselling notes.</p>
        </div>
        <span class="badge bg-primary fs-6"><?= date('d M Y') ?></span>
    </div>

    <div class="row">
        <?php
        $cards = [
            ['Total Referrals',$totalReferrals,'fa-share-nodes','primary'],
            ['Total Appointments',$totalAppointments,'fa-calendar-check','success'],
            ['Completed Sessions',$completedAppointments,'fa-circle-check','info'],
            ['Pending Follow-ups',$pendingFollowups,'fa-user-clock','warning']
        ];
        foreach($cards as $card):
        ?>
        <div class="col-lg-3 col-md-6 mb-4">
            <div class="card shadow-sm border-start border-<?= $card[3] ?> border-5 h-100">
                <div class="card-body"><div class="d-flex justify-content-between align-items-center">
                    <div><h6 class="text-muted"><?= $card[0] ?></h6><h2 class="fw-bold mb-0"><?= $card[1] ?></h2></div>
                    <i class="fa-solid <?= $card[2] ?> fa-2x text-<?= $card[3] ?>"></i>
                </div></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <?php
        $mini = [
            ['Pending Referrals',$pendingReferrals,'fa-clock','warning'],
            ['Accepted Referrals',$acceptedReferrals,'fa-circle-check','primary'],
            ['Completed Referrals',$completedReferrals,'fa-check-double','success'],
            ['Counselling Notes',$totalNotes,'fa-note-sticky','danger']
        ];
        foreach($mini as $item):
        ?>
        <div class="col-lg-3 col-md-6 mb-4"><div class="card shadow-sm h-100"><div class="card-body text-center">
            <i class="fa-solid <?= $item[2] ?> fa-2x text-<?= $item[3] ?> mb-2"></i>
            <h3><?= $item[1] ?></h3><h6><?= $item[0] ?></h6>
        </div></div></div>
        <?php endforeach; ?>
    </div>

    <div class="row mb-4">
        <div class="col-lg-6 mb-4"><div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white"><h5 class="mb-0"><i class="fa-solid fa-chart-pie"></i> Referral Status</h5></div>
            <div class="card-body">
                <?php if($statusRows): ?><div style="height:300px;position:relative"><canvas id="statusChart"></canvas></div>
                <?php else: ?><div class="text-center text-muted py-5">No referral data available.</div><?php endif; ?>
            </div>
        </div></div>
        <div class="col-lg-6 mb-4"><div class="card shadow-sm h-100">
            <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fa-solid fa-chart-column"></i> Monthly Appointments</h5></div>
            <div class="card-body">
                <?php if($monthlyRows): ?><div style="height:300px;position:relative"><canvas id="appointmentChart"></canvas></div>
                <?php else: ?><div class="text-center text-muted py-5">No appointment data available for <?= date('Y') ?>.</div><?php endif; ?>
            </div>
        </div></div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-info text-white"><h5 class="mb-0"><i class="fa-solid fa-calendar-check"></i> Recent Appointments</h5></div>
        <div class="card-body"><div class="table-responsive"><table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-light"><tr><th>ID</th><th>Student</th><th>Register No</th><th>Date</th><th>Time</th><th>Purpose</th><th>Status</th></tr></thead>
            <tbody>
            <?php if($recentAppointments): foreach($recentAppointments as $row): ?>
                <tr>
                    <td><?= (int)$row['id'] ?></td><td><?= htmlspecialchars($row['fullname']) ?></td><td><?= htmlspecialchars($row['register_no']) ?></td>
                    <td><?= date('d M Y',strtotime($row['appointment_date'])) ?></td><td><?= date('h:i A',strtotime($row['appointment_time'])) ?></td>
                    <td><?= htmlspecialchars($row['purpose'] ?? '') ?></td><td>
                    <?php if($row['status']==='Pending'): ?><span class="badge bg-warning text-dark">Pending</span>
                    <?php elseif($row['status']==='Approved'): ?><span class="badge bg-primary">Approved</span>
                    <?php elseif($row['status']==='Completed'): ?><span class="badge bg-success">Completed</span>
                    <?php else: ?><span class="badge bg-danger">Rejected</span><?php endif; ?></td>
                </tr>
            <?php endforeach; else: ?><tr><td colspan="7" class="text-center text-muted">No appointments found.</td></tr><?php endif; ?>
            </tbody>
        </table></div></div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning"><h5 class="mb-0"><i class="fa-solid fa-user-clock"></i> Recent Follow-ups</h5></div>
        <div class="card-body"><div class="table-responsive"><table class="table table-hover table-bordered align-middle mb-0">
            <thead class="table-light"><tr><th>ID</th><th>Student</th><th>Appointment Date</th><th>Follow-up Date</th><th>Status</th></tr></thead>
            <tbody>
            <?php if($recentFollowups): foreach($recentFollowups as $row): ?>
                <tr><td><?= (int)$row['id'] ?></td><td><?= htmlspecialchars($row['fullname']) ?></td>
                <td><?= date('d M Y',strtotime($row['appointment_date'])) ?></td><td><?= date('d M Y',strtotime($row['followup_date'])) ?></td>
                <td><?php if($row['status']==='Completed'): ?><span class="badge bg-success">Completed</span><?php else: ?><span class="badge bg-warning text-dark">Pending</span><?php endif; ?></td></tr>
            <?php endforeach; else: ?><tr><td colspan="5" class="text-center text-muted">No follow-ups found.</td></tr><?php endif; ?>
            </tbody>
        </table></div></div>
    </div>

    <div class="card shadow-sm mb-4"><div class="card-body text-center">
        <a href="print_report.php" target="_blank" class="btn btn-primary me-2"><i class="fa-solid fa-print"></i> Print Report</a>
        <button type="button" onclick="window.print()" class="btn btn-danger"><i class="fa-solid fa-file-pdf"></i> Print / Save PDF</button>
    </div></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const statusLabels = <?= json_encode($statusLabels) ?>;
const statusValues = <?= json_encode($statusValues) ?>;
const monthlyLabels = <?= json_encode($monthlyLabels) ?>;
const monthlyValues = <?= json_encode($monthlyValues) ?>;

const statusCanvas = document.getElementById('statusChart');
if(statusCanvas){
    new Chart(statusCanvas,{type:'doughnut',data:{labels:statusLabels,datasets:[{data:statusValues,backgroundColor:['#ffc107','#0d6efd','#198754','#dc3545'],borderWidth:2}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{position:'bottom'}}}});
}

const appointmentCanvas = document.getElementById('appointmentChart');
if(appointmentCanvas){
    new Chart(appointmentCanvas,{type:'bar',data:{labels:monthlyLabels,datasets:[{label:'Appointments',data:monthlyValues,backgroundColor:'#198754',borderRadius:8}]},options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true,ticks:{precision:0}}}}});
}
</script>

<style>
@media print{
    .sidebar,.navbar,.btn,button{display:none!important}
    body{background:#fff!important}
    .card{box-shadow:none!important;border:1px solid #ddd!important}
    .container-fluid{width:100%!important;margin:0!important;padding:10px!important}
}
</style>

<?php include "../includes/footer.php"; ?>
