<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if(!isset($_GET['id'])){
    die("Follow-up ID missing.");
}

$id = $_GET['id'];

/* Fetch Follow-up */

$stmt = $conn->prepare("
SELECT *
FROM followups
WHERE id=?
");

$stmt->execute([$id]);

$followup = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$followup){
    die("Follow-up not found.");
}

/* Update */

if(isset($_POST['update'])){

    $date = $_POST['followup_date'];
    $time = $_POST['followup_time'];
    $remarks = trim($_POST['remarks']);

    $stmt = $conn->prepare("
    UPDATE followups
    SET

    followup_date=?,
    followup_time=?,
    remarks=?

    WHERE id=?
    ");

    $stmt->execute([
        $date,
        $time,
        $remarks,
        $id
    ]);

    header("Location: followups.php?updated=1");
    exit();
}

include "../includes/header.php";
include "../includes/counsellor_sidebar.php";
?>

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fa-solid fa-pen"></i>

Edit Follow-up

</h4>

</div>

<div class="card-body">

<form method="POST">

<div class="mb-3">

<label>Follow-up Date</label>

<input
type="text"
id="followup_date"
name="followup_date"
class="form-control"
value="<?= $followup['followup_date'] ?>"
required>

</div>

<div class="mb-3">

<label>Follow-up Time</label>

<input
type="text"
id="followup_time"
name="followup_time"
class="form-control"
value="<?= $followup['followup_time'] ?>"
required>

</div>

<div class="mb-3">

<label>Remarks</label>

<textarea
name="remarks"
class="form-control"
rows="5"
required><?= htmlspecialchars($followup['remarks']) ?></textarea>

</div>

<button
name="update"
class="btn btn-primary">

<i class="fa-solid fa-floppy-disk"></i>

Update

</button>

<a
href="followups.php"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</div>

<?php include "../includes/footer.php"; ?>

<script>

document.addEventListener("DOMContentLoaded",function(){

flatpickr("#followup_date",{

dateFormat:"Y-m-d",

altInput:true,

altFormat:"d F Y",

minDate:"today"

});

flatpickr("#followup_time",{

enableTime:true,

noCalendar:true,

dateFormat:"H:i:S",

altInput:true,

altFormat:"h:i K",

time_24hr:false,

minuteIncrement:5

});

});

</script>
