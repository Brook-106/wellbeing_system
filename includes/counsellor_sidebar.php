<div class="sidebar">

    <h3>Wellbeing</h3>

    <!-- Profile -->
    <div class="text-center py-3 border-bottom">

        <i class="fa-solid fa-circle-user fa-4x text-white mb-2"></i>

        <h5 class="text-white mb-1">
            <?= htmlspecialchars($_SESSION['fullname']) ?>
        </h5>

        <small class="text-light">
            Counsellor
        </small>

    </div>

    <!-- Dashboard -->
    <a href="../counsellor/dashboard.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='dashboard.php' ? 'active':''; ?>">

        <i class="fa fa-home"></i>

        Dashboard

    </a>

    <li class="nav-item">

<a class="nav-link" href="../counsellor/referrals.php">

<i class="fa-solid fa-list-check"></i>

Referrals

</a>

</li>

    <!-- Appointments -->
    <a href="../counsellor/appointments.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='appointments.php' ? 'active':''; ?>">

        <i class="fa-solid fa-calendar-check"></i>

        Appointments

    </a>

    <!-- Notes -->
    <a href="../counsellor/notes.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='notes.php' ? 'active':''; ?>">

        <i class="fa-solid fa-note-sticky"></i>

        Notes

    </a>

    <!-- Follow Ups -->
    <a href="../counsellor/followups.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='followups.php' ? 'active':''; ?>">

        <i class="fa-solid fa-user-clock"></i>

        Follow-ups

    </a>

    <!-- Reports -->
    <a href="../counsellor/reports.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='reports.php' ? 'active':''; ?>">

        <i class="fa-solid fa-chart-line"></i>

        Reports

    </a>

    <!-- Profile -->
    <a href="../counsellor/profile.php"
       class="<?= basename($_SERVER['PHP_SELF'])=='profile.php' ? 'active':''; ?>">

        <i class="fa-solid fa-user"></i>

        Profile

    </a>

    <!-- Logout -->
    <a href="../logout.php">

        <i class="fa-solid fa-right-from-bracket"></i>

        Logout

    </a>

</div>

<div class="content">