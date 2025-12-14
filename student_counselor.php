<?php
session_start();
include "db.php";

// ROLE CHECK
if (!isset($_SESSION['user']) || 
    ($_SESSION['user']['role'] != 'counselor' && $_SESSION['user']['role'] != 'admin')) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$success = "";
$error = "";

/* -----------------------
        DELETE STUDENT
----------------------- */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $conn->query("DELETE FROM logs WHERE id = $id AND role='user'");
    $success = "Student deleted successfully!";
}

/* -----------------------
        FETCH STUDENTS
----------------------- */
$students = $conn->query("SELECT * FROM logs WHERE role='user' ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Students</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* -------- GLOBAL -------- */
body{
    margin:0;
    padding:0;
    display:flex;
    min-height:100vh;
    font-family:"Segoe UI",sans-serif;
    background:linear-gradient(135deg,#2980b9,#6dd5fa,#ffffff);
    transition:.3s;
}
body.dark{
    background:#111;
    color:#eee;
}

/* -------- SIDEBAR -------- */
.sidebar{
    width:260px;
    height:100vh;
    background:rgba(0,0,0,.35);
    backdrop-filter:blur(12px);
    padding-top:30px;
    position:fixed;
    box-shadow:0 0 25px rgba(0,0,0,.3);
}
.sidebar h2{
    color:white;
    text-align:center;
    margin-bottom:25px;
}
.sidebar a{
    display:block;
    padding:14px 25px;
    color:white;
    text-decoration:none;
    border-left:4px solid transparent;
}
.sidebar a:hover{
    background:rgba(255,255,255,.15);
    border-left:4px solid #fff;
}
.sidebar i{margin-right:10px;}
#darkModeToggle{
    margin:20px;
    padding:10px;
    width:calc(100% - 40px);
    background:#444;
    color:white;
    border:none;
    border-radius:8px;
}

/* -------- CONTENT -------- */
.content{
    margin-left:260px;
    padding:40px;
    width:calc(100% - 260px);
}
.page-title{
    color:white;
    font-size:30px;
    font-weight:bold;
    margin-bottom:25px;
}

/* -------- CARD -------- */
.card{
    background:rgba(255,255,255,.6);
    backdrop-filter:blur(12px);
    padding:25px;
    border-radius:15px;
    box-shadow:0 10px 25px rgba(0,0,0,.15);
}
body.dark .card{
    background:rgba(40,40,40,.75);
}

/* -------- TABLE -------- */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:15px;
}
th,td{
    padding:15px;
    border-bottom:1px solid rgba(0,0,0,.1);
}
th{
    background:#2980b9;
    color:white;
}
body.dark th{
    background:#1f2f3a;
}
.action-btn{
    background:#c0392b;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:6px;
    cursor:pointer;
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
    <a href="add_counseling.php"><i class="fa fa-plus-circle"></i> Add Counseling</a>
    <a href="add_discipline.php"><i class="fa fa-plus"></i> Add Discipline Case</a>
    <a href="analytics_dashboard.php"><i class="fa fa-chart-line"></i> Analytics Dashboard</a>
    <a href="logout.php" id="logoutLink"><i class="fa fa-sign-out"></i> Logout</a>
    <button id="darkModeToggle"><i class="fa fa-moon"></i> Dark Mode</button>
</div>

<!-- CONTENT -->
<div class="content">
    <div class="page-title">Manage Students</div>

    <?php if($success): ?>
        <div class="card" style="background:#2ecc71;color:white"><?= $success ?></div>
    <?php endif; ?>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php while($s=$students->fetch_assoc()): ?>
                <tr>
                    <td><?= $s['id'] ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['email']) ?></td>
                    <td>
                        <button class="action-btn" onclick="deleteStudent(<?= $s['id'] ?>)">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
function deleteStudent(id){
    if(confirm("Delete this student?")){
        window.location = "?delete=" + id;
    }
}

/* DARK MODE */
if(localStorage.getItem("theme")==="dark"){
    document.body.classList.add("dark");
}
document.getElementById("darkModeToggle").onclick = function(){
    document.body.classList.toggle("dark");
    localStorage.setItem(
        "theme",
        document.body.classList.contains("dark") ? "dark" : "light"
    );
};
</script>

</body>
</html>
