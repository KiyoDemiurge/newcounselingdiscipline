<?php
session_start();
include "db.php";

// ROLE CHECK → Only students allowed
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$student_id = $user['id'];

// Fetch student's records with incident_location
$discipline_records = $conn->query("SELECT * FROM discipline_records WHERE student_id = $student_id ORDER BY created_at DESC");
$counseling_records = $conn->query("SELECT * FROM counseling_records WHERE student_id = $student_id ORDER BY session_date DESC");

$total_records = $discipline_records->num_rows + $counseling_records->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Records</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
body {
    margin: 0;
    padding: 0;
    font-family: "Segoe UI", sans-serif;
    background: linear-gradient(135deg, #2980b9, #6dd5fa, #ffffff);
    display: flex;
    min-height: 100vh;
    transition: .3s;
}
body.dark { background: #111 !important; color: #eee; }

/* SIDEBAR */
.sidebar {
    width: 260px;
    height: 100vh;
    background: rgba(0,0,0,0.35);
    backdrop-filter: blur(12px);
    padding-top: 30px;
    position: fixed;
    box-shadow: 0 0 25px rgba(0,0,0,0.3);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.sidebar h2 { color: white; text-align: center; margin-bottom: 25px; font-size: 22px; }
.sidebar a { display: block; padding: 14px 25px; color: white; font-size: 16px; text-decoration: none; border-left: 4px solid transparent; transition: .2s; }
.sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.15); border-left: 4px solid #fff; }
.sidebar i { margin-right: 10px; }
#darkModeToggle { margin: 20px; padding: 10px; width: calc(100% - 40px); background: #444; color: white; border: none; border-radius: 8px; cursor: pointer; }

/* MAIN CONTENT */
.content {
    margin-left: 260px;
    padding: 40px;
    width: calc(100% - 260px);
}
.page-title { color: white; font-size: 30px; font-weight: bold; }

/* CARDS */
.dashboard-cards {
    margin-top: 30px;
    display: flex;
    flex-wrap: wrap;
    gap: 25px;
}
.card-box {
    flex: 1;
    min-width: 200px;
    background: rgba(255,255,255,0.55);
    padding: 25px;
    border-radius: 15px;
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    text-align: center;
    transition: .2s;
}
body.dark .card-box { background: rgba(40,40,40,0.75); }
.card-box:hover { transform: scale(1.04); }
.card-box i { font-size: 50px; margin-bottom: 15px; }
.card-title { font-size: 18px; margin-top: 10px; font-weight: 600; }
.card-value { font-size: 20px; margin-top: 12px; font-weight: bold; }

/* TABLE */
.records-table {
    width: 100%;
    margin-top: 25px;
    border-collapse: collapse;
    border-radius: 12px;
    overflow: hidden;
    background: rgba(255,255,255,0.55);
    backdrop-filter: blur(12px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
body.dark .records-table { background: rgba(40,40,40,0.75); color: #eee; }
.records-table th, .records-table td { padding: 12px 15px; text-align: left; }
.records-table th { background: rgba(0,0,0,0.1); font-weight: 600; }
.records-table tr:hover { background: rgba(255,255,255,0.15); }
body.dark .records-table tr:hover { background: rgba(255,255,255,0.05); }

/* BUTTON */
.btn { display: inline-block; padding: 8px 12px; background: #111; color: white; border-radius: 6px; text-decoration: none; margin-top: 10px; }
.btn:hover { background: #333; }

/* RESPONSIVE */
@media (max-width: 768px) {
    .content { padding: 30px 20px; margin-left: 0; width: 100%; }
    .sidebar { width: 100%; height: auto; position: relative; display: flex; flex-direction: row; overflow-x: auto; padding: 10px 0; }
    .sidebar a { flex: 1; text-align: center; border-left: none; }
    .dashboard-cards { flex-direction: column; }
    .records-table th, .records-table td { font-size: 14px; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div>
        <h2>Student Panel</h2>
        <a href="student_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="my_profile.php"><i class="fa fa-user"></i> My Profile</a>
        <a href="student_report.php" class="active"><i class="fa fa-book"></i> My Records</a>
    </div>
    <div>
        <a href="logout.php" id="logoutLink"><i class="fa fa-sign-out"></i> Logout</a>
        <button id="darkModeToggle"><i class="fa fa-moon"></i> Dark Mode</button>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    <div class="page-title">My Records 📚</div>

    <!-- SUMMARY CARDS -->
    <div class="dashboard-cards">
        <div class="card-box">
            <i class="fa fa-folder-open" style="color:#e67e22;"></i>
            <div class="card-title">Discipline Records</div>
            <div class="card-value"><?= $discipline_records->num_rows ?></div>
        </div>
        <div class="card-box">
            <i class="fa fa-comments" style="color:#27ae60;"></i>
            <div class="card-title">Counseling Records</div>
            <div class="card-value"><?= $counseling_records->num_rows ?></div>
        </div>
        <div class="card-box">
            <i class="fa fa-list" style="color:#3498db;"></i>
            <div class="card-title">Total Records</div>
            <div class="card-value"><?= $total_records ?></div>
        </div>
    </div>

    <!-- RECORDS TABLE -->
    <table class="records-table">
        <thead>
            <tr>
                <th>Type</th>
                <th>Date</th>
                <th>Location</th>
                <th>Reason / Notes</th>
                <th>Follow-Up</th>
            </tr>
        </thead>
        <tbody>
            <?php
            while($row = $discipline_records->fetch_assoc()) {
                $location = $row['incident_location'] ?: 'N/A';
                echo "<tr>
                        <td>Discipline</td>
                        <td>{$row['incident_date']}</td>
                        <td>{$location}</td>
                        <td>{$row['description']}</td>
                        <td>N/A</td>
                      </tr>";
            }
            while($row = $counseling_records->fetch_assoc()) {
                $followup = $row['follow_up_required'] ? 'Yes' : 'No';
                echo "<tr>
                        <td>Counseling</td>
                        <td>{$row['session_date']}</td>
                        <td>N/A</td>
                        <td>{$row['notes']}</td>
                        <td>{$followup}</td>
                      </tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<script>
// DARK MODE
if (localStorage.getItem("theme") === "dark") document.body.classList.add("dark");
document.getElementById("darkModeToggle").onclick = function() {
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
};

// LOGOUT CONFIRM
document.getElementById('logoutLink').addEventListener('click', function(e){
    if(!confirm("Logout now?")) e.preventDefault();
});
</script>

</body>
</html>
