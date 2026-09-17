<div class="sidebar">

    <h3>Wellbeing</h3>

    <!-- Student Profile -->
    <div class="text-center text-white mb-4">

        <i class="fa-solid fa-circle-user fa-3x mb-2"></i>

        <h6 class="mb-1">
            <?= htmlspecialchars($_SESSION['fullname']); ?>
        </h6>

        <small>Student</small>

    </div>

    <!-- Dashboard -->
    <a href="../student/dashboard.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">

        <i class="fa fa-home"></i>
        Dashboard

    </a>

    <!-- My Appointments -->
    <a href="../student/appointments.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'appointments.php' ? 'active' : ''; ?>">

        <i class="fa-solid fa-calendar-check"></i>
        My Appointments

    </a>

    <!-- My History -->
    <a href="../student/history.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'history.php' ? 'active' : ''; ?>">

        <i class="fa-solid fa-clock-rotate-left"></i>
        My History

    </a>

    <!-- Assessment -->
    <a href="../student/assessment.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'assessment.php' ? 'active' : ''; ?>">

        <i class="fa-solid fa-clipboard-check"></i>
        Assessment

    </a>

    <!-- Feedback -->
    <a href="../student/feedback.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'feedback.php' ? 'active' : ''; ?>">

        <i class="fa-solid fa-comment-dots"></i>
        Feedback

    </a>

    <!-- Notifications -->
    <a href="../student/notifications.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'notifications.php' ? 'active' : ''; ?>">

        <i class="fa-solid fa-bell"></i>
        Notifications

    </a>

    <!-- Resources -->
    <a href="../student/resources.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'resources.php' ? 'active' : ''; ?>">

        <i class="fa-solid fa-book"></i>
        Resources

    </a>

    <!-- Profile -->
    <a href="../student/profile.php"
       class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">

        <i class="fa-solid fa-user"></i>
        Profile

    </a>

    <!-- Logout -->
    <div style="margin-top:auto;padding:15px;">

        <a href="../logout.php" class="btn btn-danger w-100">

            <i class="fa-solid fa-right-from-bracket"></i>

            Logout

        </a>

    </div>

</div>

<div class="content">
