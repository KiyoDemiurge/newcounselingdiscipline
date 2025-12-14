<?php
session_start();
include "db.php";

// Only counselors and admins
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['counselor','admin'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$success = "";
$error = "";

// Fetch students and counselors from logs
$students = $conn->query("SELECT id, name FROM logs WHERE role='user' ORDER BY name ASC");
$counselors = $conn->query("SELECT id, name FROM logs WHERE role='counselor' ORDER BY name ASC");

// Handle form submission
if (isset($_POST['add_counseling'])) {
    $student_id = intval($_POST['student_id']);
    $counselor_id = intval($_POST['counselor_id']);
    $session_date = $_POST['session_date'];
    $notes = trim($_POST['notes']);
    $follow_up = isset($_POST['follow_up']) ? 1 : 0;
    $status = 'pending';

    if (!$student_id || !$counselor_id || !$session_date) {
        $error = "Please fill all required fields.";
    } else {
        $stmt = $conn->prepare("INSERT INTO counseling_records (student_id, counselor_id, session_date, notes, follow_up_required, status, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        $stmt->bind_param("iissis", $student_id, $counselor_id, $session_date, $notes, $follow_up, $status);

        if ($stmt->execute()) {
            $success = "Counseling record added successfully!";
        } else {
            $error = "Failed to add record: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Counseling Record</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
body {
    margin:0; padding:0; font-family:"Segoe UI", sans-serif;
    display:flex; min-height:100vh;
    background: linear-gradient(135deg,#2980b9,#6dd5fa,#ffffff); transition:.3s;
}
body.dark { background:#111; color:#eee; }

.sidebar {
    width:260px; height:100vh; background:rgba(0,0,0,0.35); backdrop-filter:blur(12px);
    padding-top:30px; position:fixed; box-shadow:0 0 25px rgba(0,0,0,0.3);
}
.sidebar h2 { color:white; text-align:center; margin-bottom:25px; font-size:22px; }
.sidebar a { display:block; padding:14px 25px; color:white; font-size:16px; text-decoration:none; border-left:4px solid transparent; transition:.2s; }
.sidebar a:hover { background:rgba(255,255,255,0.15); border-left:4px solid #fff; }
.sidebar i { margin-right:10px; }
#darkModeToggle { margin:20px; padding:10px; width:calc(100% - 40px); background:#444; color:white; border:none; border-radius:8px; cursor:pointer; }

.content { margin-left:260px; padding:30px; width:calc(100% - 260px); }
.page-title { font-size:30px; font-weight:bold; color:#fff; margin-bottom:20px; }

.card-box {
    background: rgba(255,255,255,0.9);
    padding: 25px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
body.dark .card-box { background: rgba(40,40,40,0.75); color:#eee; }

input, select, textarea, button {
    width:100%; padding:10px; margin:8px 0; border-radius:6px; border:1px solid #aaa; font-size:14px;
}
button { background:#3498db; color:white; border:none; cursor:pointer; }
button:hover { background:#2980b9; }
.error { background:#e74c3c33; padding:10px; margin-bottom:10px; border-left:4px solid #e74c3c; border-radius:6px; }
.success { background:#2ecc7133; padding:10px; margin-bottom:10px; border-left:4px solid #27ae60; border-radius:6px; }
</style>
</head>
<body>

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

<div class="content">
    <div class="page-title">Add Counseling Record</div>

    <div class="card-box">
        <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <?php if($success): ?><div class="success"><?= $success ?></div><?php endif; ?>

        <form method="POST">
            <label>Student</label>
            <select name="student_id" required>
                <option value="">-- Select Student --</option>
                <?php while($s=$students->fetch_assoc()): ?>
                    <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Counselor</label>
            <select name="counselor_id" required>
                <option value="">-- Select Counselor --</option>
                <?php while($c=$counselors->fetch_assoc()): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endwhile; ?>
            </select>

            <label>Session Date</label>
            <input type="date" name="session_date" required>

            <label>Notes</label>
            <textarea name="notes" rows="4" placeholder="Enter counseling notes..."></textarea>

            <label><input type="checkbox" name="follow_up" value="1"> Follow-up required</label>

            <button type="submit" name="add_counseling">Add Record</button>
        </form>
    </div>
</div>

<script>
document.getElementById('logoutLink').addEventListener('click', function(e){
    if(!confirm("Logout now?")) e.preventDefault();
});
const darkToggle=document.getElementById("darkModeToggle");
if(localStorage.getItem("theme")==="dark") document.body.classList.add("dark");
darkToggle.onclick=function(){
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark")?"dark":"light");
};
</script>
</body>
</html>
