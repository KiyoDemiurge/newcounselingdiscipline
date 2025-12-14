<?php
session_start();
include "db.php";

// ROLE CHECK
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] != 'counselor' && $_SESSION['user']['role'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// GET ANALYTICS DATA
$month_discipline = $conn->query("SELECT MONTH(incident_date) AS m, COUNT(*) AS total FROM discipline_records GROUP BY MONTH(incident_date)");
$month_counseling = $conn->query("SELECT MONTH(session_date) AS m, COUNT(*) AS total FROM counseling_records GROUP BY MONTH(session_date)");

$offense_type = $conn->query("SELECT incident_type, COUNT(*) AS total FROM discipline_records GROUP BY incident_type");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Analytics Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            padding: 0;
            display: flex;
            min-height: 100vh;
            font-family: "Segoe UI", sans-serif;
            background: linear-gradient(135deg, #2980b9, #6dd5fa, #ffffff);
            transition: .3s;
        }

        body.dark { background: #111; color: #eee; }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            height: 100vh;
            background: rgba(0,0,0,0.35);
            backdrop-filter: blur(12px);
            padding-top: 30px;
            position: fixed;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
        }
        .sidebar h2 { color: white; text-align: center; margin-bottom: 25px; }
        .sidebar a {
            display: block;
            padding: 14px 25px;
            color: white;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: .2s;
        }
        .sidebar a:hover {
            background: rgba(255,255,255,0.15);
            border-left: 4px solid #fff;
        }

        #darkModeToggle {
            margin: 20px;
            padding: 10px;
            width: calc(100% - 40px);
            background: #444;
            color: white;
            border: none;
            border-radius: 8px;
        }

        /* CONTENT */
        .content {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
        }

        .page-title {
            font-size: 30px;
            color: white;
            font-weight: bold;
            margin-bottom: 20px;
        }

        /* CHART CARDS */
        .chart-box {
            background: rgba(255,255,255,0.55);
            padding: 25px;
            border-radius: 18px;
            backdrop-filter: blur(12px);
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
            transition: .2s;
        }

        body.dark .chart-box { background: rgba(40,40,40,0.75); }
        .chart-box:hover { transform: scale(1.02); }

        canvas { width: 100%; height: 350px !important; }
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

    <div class="page-title">📊 Advanced Analytics Dashboard</div>

    <!-- DISCIPLINE CASES PER MONTH -->
    <div class="chart-box">
        <h3>Discipline Cases per Month</h3>
        <canvas id="disciplineChart"></canvas>
    </div>

    <!-- COUNSELING SESSIONS PER MONTH -->
    <div class="chart-box">
        <h3>Counseling Sessions per Month</h3>
        <canvas id="counselingChart"></canvas>
    </div>

    <!-- OFFENSE TYPES PIE CHART -->
    <div class="chart-box">
        <h3>Offense Types Distribution</h3>
        <canvas id="offenseChart"></canvas>
    </div>

</div>

<script>
// DARK MODE
if (localStorage.getItem("theme") === "dark") document.body.classList.add("dark");

document.getElementById("darkModeToggle").onclick = function() {
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
};

// DISCIPLINE CHART (BAR)
new Chart(document.getElementById("disciplineChart").getContext("2d"), {
    type: "bar",
    data: {
        labels: [<?php while($r = $month_discipline->fetch_assoc()) echo "'" . $r['m'] . "',"; ?>],
        datasets: [{
            label: "Cases",
            data: [<?php
                $month_discipline->data_seek(0);
                while($r=$month_discipline->fetch_assoc()) echo $r['total'] . ",";
            ?>],
            backgroundColor: "rgba(231, 76, 60, 0.7)"
        }]
    }
});

// COUNSELING CHART (LINE)
new Chart(document.getElementById("counselingChart").getContext("2d"), {
    type: "line",
    data: {
        labels: [<?php while($c = $month_counseling->fetch_assoc()) echo "'" . $c['m'] . "',"; ?>],
        datasets: [{
            label: "Sessions",
            data: [<?php
                $month_counseling->data_seek(0);
                while($c=$month_counseling->fetch_assoc()) echo $c['total'] . ",";
            ?>],
            borderWidth: 3
        }]
    }
});

// OFFENSE PIE CHART
new Chart(document.getElementById("offenseChart").getContext("2d"), {
    type: "pie",
    data: {
        labels: [<?php while($o = $offense_type->fetch_assoc()) echo "'" . $o['incident_type'] . "',"; ?>],
        datasets: [{
            data: [
                <?php
                $offense_type->data_seek(0);
                while($o=$offense_type->fetch_assoc()) echo $o['total'] . ",";
                ?>
            ]
        }]
    }
});
</script>

</body>
</html>
