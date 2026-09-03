<div class="sidebar">

    <h3>Wellbeing</h3>

    <div class="text-center text-white mb-4">

        <i class="fa-solid fa-circle-user fa-3x mb-2"></i>

        <h6 class="mb-1">
            <?= htmlspecialchars($_SESSION['fullname']); ?>
        </h6>

        <small>Mentor</small>

    </div>

    <a href="../mentor/dashboard.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active':''; ?>">
        <i class="fa fa-home"></i>
        Dashboard
    </a>

    <a href="../mentor/students.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='students.php' ? 'active':''; ?>">
        <i class="fa fa-user-graduate"></i>
        My Students
    </a>

    <a href="../mentor/create_referral.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='create_referral.php' ? 'active':''; ?>">
        <i class="fa fa-share-nodes"></i>
        Create Referral
    </a>

    <a href="../mentor/referrals.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='referrals.php' ? 'active':''; ?>">
        <i class="fa fa-list-check"></i>
        My Referrals
    </a>

    <a href="../mentor/profile.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='profile.php' ? 'active':''; ?>">
        <i class="fa fa-user"></i>
        Profile
    </a>

    <a href="../mentor/settings.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='settings.php' ? 'active':''; ?>">
        <i class="fa fa-gear"></i>
        Settings
    </a>

    <div style="margin-top:auto;padding:15px;">

        <a href="../logout.php" class="btn btn-danger w-100">

            <i class="fa fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</div>

<div class="content">