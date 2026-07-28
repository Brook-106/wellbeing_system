<?php
session_start();

if (isset($_SESSION['user_id'])) {

    switch ($_SESSION['role']) {
        case 'admin':
            header("Location: admin/dashboard.php");
            break;
        case 'mentor':
            header("Location: mentor/dashboard.php");
            break;
        case 'counsellor':
            header("Location: counsellor/dashboard.php");
            break;
        case 'student':
            header("Location: student/dashboard.php");
            break;
    }

    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Wellbeing System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{

background:#0d6efd;

display:flex;

justify-content:center;

align-items:center;

height:100vh;

font-family:Arial;

}

.card{

width:420px;

border:none;

border-radius:15px;

box-shadow:0 10px 30px rgba(0,0,0,.3);

}

</style>

</head>

<body>

<div class="card p-4">

<h2 class="text-center mb-4">

Student Wellbeing System

</h2>

<form action="includes/auth.php" method="POST">

<div class="mb-3">

<label>Email</label>

<input type="email"

class="form-control"

name="email"

required>

</div>

<div class="mb-3">

<label>Password</label>

<input type="password"

class="form-control"

name="password"

required>

</div>

<button

class="btn btn-primary w-100"

type="submit"

name="login">

Login

</button>

</form>

</div>

</body>

</html>