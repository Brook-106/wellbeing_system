<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

if (!isset($_GET['id'])) {
    header("Location: referrals.php");
    exit();
}

$referralId = $_GET['id'];

$stmt = $conn->prepare("
SELECT *
FROM referrals
WHERE id = ?
");

$stmt->execute([$referralId]);

$referral = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$referral) {
    die("Referral not found.");
}

if ($referral['status'] != "Pending") {
    die("Only pending referrals can be edited.");
}

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<div class="card shadow">

    <div class="card-header bg-warning">

        <h3>

            <i class="fa-solid fa-pen"></i>

            Edit Referral

        </h3>

    </div>

    <div class="card-body">

        <form action="update_referral.php" method="POST">

            <input
                type="hidden"
                name="id"
                value="<?= $referral['id'] ?>">

            <div class="mb-3">

                <label class="form-label">

                    Priority

                </label>

                <select
                    name="priority"
                    class="form-select">

                    <option value="Low"
                    <?= $referral['priority']=="Low"?"selected":"" ?>>

                    Low

                    </option>

                    <option value="Medium"
                    <?= $referral['priority']=="Medium"?"selected":"" ?>>

                    Medium

                    </option>

                    <option value="High"
                    <?= $referral['priority']=="High"?"selected":"" ?>>

                    High

                    </option>

                </select>

            </div>

            <div class="mb-3">

                <label class="form-label">

                    Reason

                </label>

                <textarea
                    name="reason"
                    rows="6"
                    class="form-control"
                    required><?= htmlspecialchars($referral['reason']) ?></textarea>

            </div>

            <button class="btn btn-warning">

                <i class="fa-solid fa-floppy-disk"></i>

                Update Referral

            </button>

            <a href="referrals.php"
               class="btn btn-secondary">

                Cancel

            </a>

        </form>

    </div>

</div>

<?php include "../includes/footer.php"; ?>