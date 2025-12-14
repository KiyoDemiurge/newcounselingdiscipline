<?php
session_start();
include "db.php";  // Make sure this file includes your database connection setup

// ROLE CHECK
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'counselor' && $_SESSION['user']['role'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// DASHBOARD COUNTS
$students = $conn->query("SELECT COUNT(*) AS total FROM logs WHERE role='user'")->fetch_assoc()['total'];  // Ensure 'students' table is correct
$discipline = $conn->query("SELECT COUNT(*) AS total FROM discipline_records")->fetch_assoc()['total'];
$counseling = $conn->query("SELECT COUNT(*) AS total FROM counseling_records")->fetch_assoc()['total'];

?>
<!DOCTYPE html>
<html>
<head>
    <title>Counselor Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        /* ------------------------- GLOBAL STYLING ------------------------- */
        body {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #2980b9, #6dd5fa, #ffffff);
            transition: .3s;
        }

        body.dark {
            background: #111 !important;
            color: #eee;
        }

        /* ------------------------- SIDEBAR ------------------------- */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: rgba(0,0,0,0.35);
            backdrop-filter: blur(12px);
            padding-top: 30px;
            position: fixed;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
        }

        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 25px;
            font-size: 22px;
        }

        .sidebar a {
            display: block;
            padding: 14px 25px;
            color: white;
            font-size: 16px;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: .2s;
        }

        .sidebar a:hover {
            background: rgba(255,255,255,0.15);
            border-left: 4px solid #fff;
        }

        .sidebar i { margin-right: 10px; }

        #darkModeToggle {
            margin: 20px;
            padding: 10px;
            width: calc(100% - 40px);
            background: #444;
            color: white;
            border: none;
            border-radius: 8px;
        }

        /* ------------------------- MAIN CONTENT ------------------------- */
        .content {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
        }

        .page-title {
            color: white;
            font-size: 30px;
            font-weight: bold;
        }

        /* ------------------------- DASHBOARD CARDS ------------------------- */
        .dashboard-cards {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
        }

        .card-box {
            flex: 1;
            min-width: 260px;
            background: rgba(255,255,255,0.55);
            padding: 30px;
            border-radius: 15px;
            backdrop-filter: blur(12px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            text-align: center;
            transition: .2s;
        }

        body.dark .card-box {
            background: rgba(40,40,40,0.75);
        }

        .card-box:hover {
            transform: scale(1.04);
        }

        .card-box i {
            font-size: 55px;
            margin-bottom: 15px;
        }

        .card-title {
            font-size: 20px;
            margin-top: 10px;
            font-weight: 600;
        }

        .card-value {
            font-size: 36px;
            margin-top: 12px;
            font-weight: bold;
        }

    </style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Counselor Panel</h2>
    <a href="counselor_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="student_counselor.php"><i class="fa fa-users"></i> Manage Students</a>
    <a href="list_discipline_counseling.php"><i class="fa fa-book"></i> Counseling & Discipline Records</a>
    <a href="add_counseling.php" class="active"><i class="fa fa-plus-circle"></i> Add Counseling</a>
    <a href="add_discipline.php"><i class="fa fa-plus"></i> Add Discipline Case</a>
    <a href="analytics_dashboard.php"><i class="fa fa-chart-line"></i> Analytics Dashboard</a>
    <a href="logout.php" id="logoutLink"><i class="fa fa-sign-out"></i> Logout</a>
    <button id="darkModeToggle"><i class="fa fa-moon"></i> Dark Mode</button>
</div>
<!-- MAIN CONTENT -->
<div class="content">
    <div class="page-title">Welcome, <?= $user['name'] ?> 👋</div>

    <div class="dashboard-cards">

        <!-- STUDENTS -->
        <div class="card-box">
            <i class="fa fa-users" style="color:#3498db;"></i>
            <div class="card-title">Total Students</div>
            <div class="card-value"><?= $students ?></div>
        </div>

        <!-- DISCIPLINE -->
        <div class="card-box">
            <i class="fa fa-exclamation-triangle" style="color:#e74c3c;"></i>
            <div class="card-title">Discipline Records</div>
            <div class="card-value"><?= $discipline ?></div>
        </div>

        <!-- COUNSELING -->
        <div class="card-box">
            <i class="fa fa-comments" style="color:#2ecc71;"></i>
            <div class="card-title">Counseling Sessions</div>
            <div class="card-value"><?= $counseling ?></div>
        </div>

    </div>

</div>

<script>
// DARK MODE
document.getElementById('logoutLink').addEventListener('click', function(e){
    if(!confirm("Logout now?")) e.preventDefault();
});

if (localStorage.getItem("theme") === "dark") {
    document.body.classList.add("dark");
}

document.getElementById("darkModeToggle").onclick = function() {
    document.body.classList.toggle("dark");
    localStorage.setItem("theme",
        document.body.classList.contains("dark") ? "dark" : "light"
    );
};
</script>

</body>
</html>
