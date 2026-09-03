<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Wellbeing Management System</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

<style>

:root{

--primary:#2563eb;
--primary-dark:#1d4ed8;
--success:#16a34a;
--warning:#f59e0b;
--danger:#dc2626;
--info:#0ea5e9;
--bg:#f4f7fb;
--card-radius:18px;

}

*{

margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;

}

body{

background:var(--bg);
overflow-x:hidden;

}

/* ======================================
SIDEBAR
====================================== */

.sidebar{

position:fixed;
left:0;
top:0;

width:260px;
height:100vh;

background:linear-gradient(180deg,#2563eb,#1e3a8a);

overflow-y:auto;

box-shadow:0 0 25px rgba(0,0,0,.15);

z-index:1000;

}

.sidebar h3{

color:white;

text-align:center;

padding:25px 15px;

font-weight:700;

border-bottom:1px solid rgba(255,255,255,.2);

margin-bottom:15px;

}

.sidebar a{

display:flex;

align-items:center;

gap:12px;

color:white;

text-decoration:none;

padding:14px 18px;

margin:8px 12px;

border-radius:12px;

transition:.3s;

font-size:15px;

}

.sidebar a i{

width:22px;

text-align:center;

}

.sidebar a:hover{

background:rgba(255,255,255,.18);

transform:translateX(5px);

}

.sidebar a.active{

background:white;

color:var(--primary);

font-weight:600;

box-shadow:0 5px 15px rgba(0,0,0,.15);

}

.sidebar a.active i{

color:var(--primary);

}

/* ======================================
CONTENT
====================================== */

.content{

margin-left:260px;

padding:30px;

min-height:100vh;

}

/* ======================================
HEADINGS
====================================== */

h1,h2,h3,h4,h5,h6{

font-weight:600;

}

/* ======================================
CARDS
====================================== */

.card{

border:none;

border-radius:var(--card-radius);

box-shadow:0 8px 25px rgba(0,0,0,.08);

transition:.3s;

overflow:hidden;

}

.card:hover{

transform:translateY(-4px);

box-shadow:0 15px 30px rgba(0,0,0,.12);

}

.card-header{

border:none;

font-weight:600;

padding:15px 20px;

}

/* ======================================
STAT CARDS
====================================== */

.stat-card{

text-align:center;

padding:25px;

}

.stat-card i{

font-size:40px;

margin-bottom:15px;

}

.stat-card h2{

font-size:34px;

font-weight:700;

margin-bottom:5px;

}

.stat-card h6{

font-size:15px;

color:#555;

}

/* ======================================
TABLES
====================================== */

.table{

margin-bottom:0;

}

.table thead{

background:var(--primary);

color:white;

}

.table thead th{

border:none;

}

.table tbody tr{

transition:.2s;

}

.table tbody tr:hover{

background:#eef5ff;

}

/* ======================================
BUTTONS
====================================== */

.btn{

border-radius:12px;

transition:.25s;

}

.btn:hover{

transform:translateY(-3px);

}

.btn.w-100{

padding:18px;

font-weight:500;

}

.btn.w-100 i{

font-size:28px;

margin-bottom:10px;

}

/* ======================================
FORMS
====================================== */

.form-control,
.form-select{

border-radius:10px;

}

.form-control:focus,
.form-select:focus{

border-color:#2563eb;

box-shadow:0 0 0 .2rem rgba(37,99,235,.2);

}

/* ======================================
BADGES
====================================== */

.badge{

padding:8px 12px;

font-size:12px;

border-radius:8px;

}

/* ======================================
CHARTS
====================================== */

canvas{

max-width:100%;

}

/* ======================================
SCROLLBAR
====================================== */

::-webkit-scrollbar{

width:8px;

}

::-webkit-scrollbar-thumb{

background:#2563eb;

border-radius:10px;

}

::-webkit-scrollbar-track{

background:#f1f5f9;

}

/* ======================================
RESPONSIVE
====================================== */

@media(max-width:992px){

.sidebar{

width:80px;

}

.sidebar h3{

display:none;

}

.sidebar a{

justify-content:center;

padding:15px;

}

.sidebar a span{

display:none;

}

.sidebar a i{

margin:0;

font-size:18px;

}

.content{

margin-left:80px;

padding:20px;

}

}

@media(max-width:576px){

.content{

padding:15px;

}

.card{

margin-bottom:20px;

}

.table{

font-size:14px;

}

}

</style>

</head>

<body>