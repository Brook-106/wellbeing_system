<?php

require_once "../includes/session.php";
require_once "../includes/db.php";

include "../includes/header.php";
include "../includes/mentor_sidebar.php";
?>

<div class="card shadow">

    <div class="card-header bg-primary text-white">

        <h3>
            <i class="fa-solid fa-gear"></i>
            Mentor Settings
        </h3>

    </div>

    <div class="card-body">

        <div class="list-group">

            <a href="profile.php" class="list-group-item list-group-item-action">

                <i class="fa-solid fa-user text-primary"></i>

                My Profile

            </a>

            <a href="change_password.php" class="list-group-item list-group-item-action">

                <i class="fa-solid fa-key text-warning"></i>

                Change Password

            </a>

            <a href="../logout.php" class="list-group-item list-group-item-action text-danger">

                <i class="fa-solid fa-right-from-bracket"></i>

                Logout

            </a>

        </div>

    </div>

</div>

<?php include "../includes/footer.php"; ?>