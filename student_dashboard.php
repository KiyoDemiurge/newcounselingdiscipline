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

// Fetch student's records counts
$discipline_count = $conn->query("SELECT COUNT(*) AS total FROM discipline_records WHERE student_id = $student_id")->fetch_assoc()['total'];
$counseling_count = $conn->query("SELECT COUNT(*) AS total FROM counseling_records WHERE student_id = $student_id")->fetch_assoc()['total'];
$total_records = $discipline_count + $counseling_count;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* -------- GLOBAL STYLING -------- */
* { box-sizing: border-box; }
body { margin: 0; padding: 0; font-family: "Segoe UI", sans-serif; background: linear-gradient(135deg, #2980b9, #6dd5fa, #ffffff); min-height: 100vh; display: flex; transition: .3s; }
body.dark { background: #111; color: #eee; }

/* -------- SIDEBAR -------- */
.sidebar { width: 260px; height: 100vh; background: rgba(0,0,0,0.35); backdrop-filter: blur(12px); padding-top: 30px; position: fixed; box-shadow: 0 0 25px rgba(0,0,0,0.3); display: flex; flex-direction: column; justify-content: space-between; }
.sidebar h2 { color: white; text-align: center; margin-bottom: 30px; font-size: 24px; }
.sidebar a { display: block; padding: 14px 25px; color: white; font-size: 16px; text-decoration: none; border-left: 4px solid transparent; transition: .3s; }
.sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.15); border-left: 4px solid #fff; }
.sidebar a i { margin-right: 12px; font-size: 18px; }
#darkModeToggle { margin: 20px; padding: 12px; width: calc(100% - 40px); background: #444; color: white; border: none; border-radius: 12px; cursor: pointer; transition: 0.3s; }
#darkModeToggle:hover { background: #555; }

/* -------- MAIN CONTENT -------- */
.content { margin-left: 260px; padding: 50px 40px; width: calc(100% - 260px); display: flex; flex-direction: column; gap: 30px; }
.page-title { color: white; font-size: 34px; font-weight: bold; }

/* -------- CARDS -------- */
.dashboard-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 25px; }
.card-box { background: rgba(255,255,255,0.55); border-radius: 20px; padding: 40px 35px; backdrop-filter: blur(12px); box-shadow: 0 15px 30px rgba(0,0,0,0.15); transition: transform 0.3s ease, box-shadow 0.3s ease; text-align: center; }
body.dark .card-box { background: rgba(40,40,40,0.75); color: #fff; }
.card-box:hover { transform: translateY(-5px); box-shadow: 0 25px 40px rgba(0,0,0,0.25); }
.card-box i { font-size: 60px; margin-bottom: 20px; }
.card-title { font-size: 22px; font-weight: 600; margin-top: 10px; }
.card-value { font-size: 18px; margin-top: 15px; line-height: 1.6; color: #222; }
body.dark .card-value { color: #eee; }
.btn { display: inline-block; padding: 10px 15px; background: #111; color: white; border-radius: 6px; text-decoration: none; margin-top: 15px; }
.btn:hover { background: #333; }

/* -------- RESPONSIVE -------- */
@media (max-width: 768px) {
    .content { padding: 30px 20px; margin-left: 0; width: 100%; }
    .sidebar { width: 100%; height: auto; position: relative; display: flex; flex-direction: row; overflow-x: auto; padding: 10px 0; }
    .sidebar a { flex: 1; text-align: center; border-left: none; }
    .dashboard-cards { grid-template-columns: 1fr; }
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div>
        <h2>Student Panel</h2>
        <a href="student_dashboard.php" class="active"><i class="fa fa-home"></i> Dashboard</a>
        <a href="my_profile.php"><i class="fa fa-user"></i> My Profile</a>
        <a href="student_report.php"><i class="fa fa-book"></i> My Records</a>
    </div>
    <div>
        <a href="logout.php" id="logoutLink"><i class="fa fa-sign-out"></i> Logout</a>
        <button id="darkModeToggle"><i class="fa fa-moon"></i> Dark Mode</button>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="content">
    <div class="page-title">Welcome, <?= htmlspecialchars($user['name']) ?> 👋</div>

    <div class="dashboard-cards">
        <!-- PROFILE INFO CARD -->
        <div class="card-box">
            <i class="fa fa-id-card" style="color:#3498db;"></i>
            <div class="card-title">My Information</div>
            <div class="card-value">
                Name: <?= htmlspecialchars($user['name']) ?><br>
                Email: <?= htmlspecialchars($user['email']) ?><br>
                Phone: <?= htmlspecialchars($user['phone'] ?? 'N/A') ?><br>
                Address: <?= htmlspecialchars($user['address'] ?? 'N/A') ?><br>
                Role: Student
            </div>
        </div>

        <!-- DISCIPLINE CARD -->
        <div class="card-box">
            <i class="fa fa-exclamation-triangle" style="color:#e74c3c;"></i>
            <div class="card-title">My Discipline Records</div>
            <div class="card-value"><?= $discipline_count ?></div>
            <a href="student_report.php" class="btn">View My Records</a>
        </div>

        <!-- COUNSELING CARD -->
        <div class="card-box">
            <i class="fa fa-comments" style="color:#27ae60;"></i>
            <div class="card-title">My Counseling Records</div>
            <div class="card-value"><?= $counseling_count ?></div>
            <a href="student_report.php" class="btn">View My Records</a>
        </div>

        <!-- TOTAL RECORDS CARD -->
        <div class="card-box">
            <i class="fa fa-list" style="color:#3498db;"></i>
            <div class="card-title">Total Records</div>
            <div class="card-value"><?= $total_records ?></div>
            <a href="student_report.php" class="btn">View All</a>
        </div>
    </div>
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
