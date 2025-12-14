<?php
session_start();
include "db.php";

// ROLE CHECK
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['counselor', 'admin'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// GET RECORD
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die('Invalid ID');
$id = (int)$_GET['id'];

// ✅ FETCH RECORD JOINING logs (STUDENT & COUNSELOR)
$stmt = $conn->prepare("
    SELECT dr.*, s.name AS student_name, s.id AS stud_code
    FROM discipline_records dr
    JOIN logs s ON dr.student_id = s.id
    WHERE dr.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$rec = $stmt->get_result()->fetch_assoc();

if (!$rec) die('Record not found');

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $incident_date     = $_POST['incident_date'] ?? '';
    $incident_type     = trim($_POST['incident_type'] ?? '');
    $incident_location = trim($_POST['incident_location'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $action_taken      = trim($_POST['action_taken'] ?? '');

    $update = $conn->prepare("
        UPDATE discipline_records
        SET incident_date=?, incident_type=?, incident_location=?, description=?, action_taken=?
        WHERE id=?
    ");
    $update->bind_param(
        'sssssi',
        $incident_date,
        $incident_type,
        $incident_location,
        $description,
        $action_taken,
        $id
    );

    if ($update->execute()) {
        header('Location: list_discipline_counseling.php?msg=updated');
        exit;
    } else {
        $msg = '❌ Update failed';
    }
}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Edit Discipline Record</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
*{box-sizing:border-box;}
body{
    margin:0;
    font-family:Inter,Segoe UI,system-ui,sans-serif;
    background:linear-gradient(135deg,#2980b9,#6dd5fa,#ffffff);
    min-height:100vh;
    display:flex;
    transition:.3s;
}
body.dark{ background:#111; color:#eee; }

.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    top:0;
    left:0;
    background:rgba(0,0,0,0.35);
    backdrop-filter:blur(12px);
    padding-top:30px;
    box-shadow:0 0 25px rgba(0,0,0,0.3);
}
.sidebar h2{ color:white; text-align:center; margin-bottom:25px; font-size:22px; }
.sidebar a{ display:block; padding:14px 25px; color:white; text-decoration:none; border-left:4px solid transparent; transition:.2s; }
.sidebar a:hover{ background:rgba(255,255,255,0.15); border-left:4px solid #fff; }
.sidebar i{ margin-right:10px; }
#darkModeToggle{ margin:20px; padding:10px; width:calc(100% - 40px); background:#444; color:white; border:none; border-radius:8px; cursor:pointer; }

.content{ margin-left:260px; padding:36px; flex:1; }
.card{ background:#fff; border-radius:12px; padding:22px; box-shadow:0 10px 30px rgba(2,6,23,0.12); max-width:900px; }
.input{ display:block; width:100%; padding:12px; border-radius:10px; border:1px solid #e3e6ee; margin-top:6px; }
.actions{ display:flex; gap:10px; margin-top:16px; }
.btn-primary{ background:#2d9cdb; color:#fff; padding:10px 16px; border-radius:10px; border:none; }
.btn{ padding:10px 16px; border-radius:10px; background:#eee; text-decoration:none; color:#333; }

body.dark .card{ background:rgba(40,40,40,0.9); color:#ddd; }
body.dark .input{ background:#3c3c3c; color:#eee; border:1px solid #555; }
body.dark .btn-primary{ background:#2ecc71; color:#fff; }
</style>
</head>
<body>

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

<div class="content">
  <div class="card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
      <div>
        <h2 style="margin:0">Edit Discipline Record</h2>
        <div style="color:#666;font-size:13px">
            Student: <?=htmlspecialchars($rec['student_name']).' ('.htmlspecialchars($rec['stud_code']).')'?>
        </div>
      </div>
      <a href="list_discipline_counseling.php" class="btn">Back</a>
    </div>

    <?php if($msg): ?>
        <div style="padding:10px;background:#fdd;border-radius:8px;margin-bottom:10px">
            <?=htmlspecialchars($msg)?>
        </div>
    <?php endif; ?>

    <form method="post">
        <label>Incident Date</label>
        <input class="input" type="date" name="incident_date" value="<?=htmlspecialchars($rec['incident_date'])?>" required>

        <label style="margin-top:12px">Incident Type</label>
        <input class="input" type="text" name="incident_type" value="<?=htmlspecialchars($rec['incident_type'])?>" required>

        <label style="margin-top:12px">Incident Location</label>
        <input class="input" type="text" name="incident_location" value="<?=htmlspecialchars($rec['incident_location'])?>" required>

        <label style="margin-top:12px">Description</label>
        <textarea class="input" name="description" rows="5"><?=htmlspecialchars($rec['description'])?></textarea>

        <label style="margin-top:12px">Action Taken</label>
        <textarea class="input" name="action_taken" rows="4"><?=htmlspecialchars($rec['action_taken'])?></textarea>

        <div class="actions">
            <button class="btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
            <a class="btn" href="delete_discipline.php?id=<?=$id?>" onclick="return confirm('Delete this record?')">Delete</a>
        </div>
    </form>
  </div>
</div>

<script>
document.getElementById('logoutLink').addEventListener('click', e=>{
    if(!confirm("Logout now?")) e.preventDefault();
});

const darkToggle = document.getElementById("darkModeToggle");
if(localStorage.getItem("theme")==="dark") document.body.classList.add("dark");
darkToggle.onclick = function(){
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark")?"dark":"light");
}
</script>

</body>
</html>
