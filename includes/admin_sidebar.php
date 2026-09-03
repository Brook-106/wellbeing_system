<?php
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<div class="sidebar">

    <h3>Wellbeing</h3>

    <a href="../admin/dashboard.php"
       class="<?= ($currentPage == 'dashboard.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-house"></i>
        Dashboard
    </a>

    <a href="../admin/students.php"
       class="<?= ($currentPage == 'students.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user-graduate"></i>
        Students
    </a>

    <a href="../admin/mentors.php"
       class="<?= ($currentPage == 'mentors.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-chalkboard-user"></i>
        Mentors
    </a>

    <a href="../admin/counsellors.php"
       class="<?= ($currentPage == 'counsellors.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user-doctor"></i>
        Counsellors
    </a>

    <a href="../admin/referrals.php"
       class="<?= ($currentPage == 'referrals.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-share-nodes"></i>
        Referrals
    </a>

    <a href="../admin/departments.php"
       class="<?= ($currentPage == 'departments.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-building"></i>
        Departments
    </a>

    <a href="../admin/reports.php"
       class="<?= ($currentPage == 'reports.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-chart-column"></i>
        Reports
    </a>

    <a href="../admin/profile.php"
       class="<?= ($currentPage == 'profile.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-user"></i>
        Profile
    </a>

    <a href="../admin/settings.php"
       class="<?= ($currentPage == 'settings.php') ? 'active' : ''; ?>">
        <i class="fa-solid fa-gear"></i>
        Settings
    </a>

    <a href="../logout.php">
        <i class="fa-solid fa-right-from-bracket"></i>
        Logout
    </a>

</div>

<div class="content">