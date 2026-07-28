<?php
require_once "../includes/session.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body{
            background:#f5f7fb;
        }

        .sidebar{
            width:250px;
            height:100vh;
            position:fixed;
            background:#0d6efd;
            color:white;
            padding-top:20px;
        }

        .sidebar h3{
            text-align:center;
            margin-bottom:30px;
        }

        .sidebar a{
            display:block;
            color:white;
            text-decoration:none;
            padding:15px 25px;
        }

        .sidebar a:hover{
            background:#0b5ed7;
        }

        .content{
            margin-left:250px;
            padding:30px;
        }

        .card{
            border:none;
            border-radius:15px;
            box-shadow:0 5px 15px rgba(0,0,0,.1);
        }
    </style>
</head>

<body>

<div class="sidebar">

    <h3>Wellbeing</h3>

    <a href="#">🏠 Dashboard</a>
    <a href="students.php">👨‍🎓 Students</a>
    <a href="mentors.php">👨‍🏫 Mentors</a>
    <a href="counsellors.php">🩺 Counsellors</a>
    <a href="departments.php">🏢 Departments</a>
    <a href="reports.php">📊 Reports</a>
    <a href="settings.php">⚙ Settings</a>
    <a href="../logout.php" class="text-warning">🚪 Logout</a>

</div>

<div class="content">

    <div class="row">

        <div class="col-md-12">

            <div class="card p-4">

                <h2>Welcome, <?php echo $_SESSION['fullname']; ?></h2>

                <p class="text-muted">
                    Role :
                    <strong><?php echo ucfirst($_SESSION['role']); ?></strong>
                </p>

            </div>

        </div>

    </div>

    <br>

    <div class="row">

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <h3>120</h3>
                <p>Total Students</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <h3>12</h3>
                <p>Mentors</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <h3>4</h3>
                <p>Counsellors</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 text-center">
                <h3>18</h3>
                <p>Pending Referrals</p>
            </div>
        </div>

    </div>

</div>

</body>
</html>