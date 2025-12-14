<?php
session_start();
include "db.php";

// ALLOW ONLY COUNSELOR & ADMIN
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','counselor'])) {
    header("Location: login.php");
    exit;
}

$user = $_SESSION['user'];
$message = "";

// ---------------------- FORM SUBMIT ----------------------
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id        = (int)($_POST["student_id"] ?? 0);
    $incident_date     = $_POST["incident_date"] ?? '';
    $incident_type     = trim($_POST["incident_type"] ?? '');
    $incident_location = trim($_POST["incident_location"] ?? ''); // NEW
    $description       = trim($_POST["description"] ?? '');
    $action_taken      = trim($_POST["action_taken"] ?? '');
    $counselor_id      = (int)$user['id'];
    $status            = "pending";

    if (!$student_id || !$incident_date || !$incident_type || !$incident_location) {
        $message = "⚠️ Student, Incident Date, Incident Type, and Location are required.";
    } else {

        $check = $conn->prepare("SELECT id FROM logs WHERE id = ? AND role='user'");
        $check->bind_param("i", $student_id);
        $check->execute();
        $check->store_result();

        if ($check->num_rows === 0) {
            $message = "❌ Selected student does not exist.";
        } else {
            $stmt = $conn->prepare("
                INSERT INTO discipline_records
                (student_id, counselor_id, incident_date, incident_type, incident_location, description, action_taken, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iissssss",
                $student_id,
                $counselor_id,
                $incident_date,
                $incident_type,
                $incident_location,
                $description,
                $action_taken,
                $status
            );

            if ($stmt->execute()) {
                $message = "✅ Discipline record saved successfully!";
            } else {
                $message = "❌ Database error: " . $stmt->error;
            }

            $stmt->close();
        }
        $check->close();
    }
}
?>




<!DOCTYPE html>
<html>
<head>
    <title>Add Discipline Record</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
/* ------------------------- GLOBAL STYLING ------------------------- */
body { margin:0; padding:0; display:flex; min-height:100vh; font-family:"Segoe UI", sans-serif; background: linear-gradient(135deg,#2980b9,#6dd5fa,#ffffff); transition:.3s; }
body.dark { background:#111 !important; color:#eee; }

/* ------------------------- SIDEBAR ------------------------- */
.sidebar { width:260px; height:100vh; background: rgba(0,0,0,0.35); backdrop-filter: blur(12px); padding-top:30px; position:fixed; box-shadow:0 0 25px rgba(0,0,0,0.3); }
.sidebar h2 { color:white; text-align:center; margin-bottom:25px; font-size:22px; }
.sidebar a { display:block; padding:14px 25px; color:white; font-size:16px; text-decoration:none; border-left:4px solid transparent; transition:.2s; }
.sidebar a:hover { background: rgba(255,255,255,0.15); border-left:4px solid #fff; }
.sidebar i { margin-right:10px; }
#darkModeToggle { margin:20px; padding:10px; width:calc(100% - 40px); background:#444; color:white; border:none; border-radius:8px; }

/* ------------------------- MAIN CONTENT ------------------------- */
.content { margin-left:260px; padding:40px; width:calc(100% - 260px); }
.page-title { color:white; font-size:30px; font-weight:bold; }
.form-card { margin-top:30px; background: rgba(255,255,255,0.60); padding:30px; border-radius:15px; backdrop-filter: blur(12px); box-shadow:0 10px 25px rgba(0,0,0,0.15); max-width:600px; }
body.dark .form-card { background: rgba(40,40,40,0.75); }
input, select, textarea { width:100%; padding:12px; margin-top:6px; border-radius:10px; border:none; outline:none; }
button { padding:10px 20px; border:none; border-radius:10px; cursor:pointer; }
.alert { padding:12px; margin-bottom:15px; border-radius:8px; font-weight:bold; }
.alert-success { background:#2ecc71; color:white; }
.alert-error { background:#c0392b; color:white; }
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
    <div class="page-title">Add Discipline Record</div>

    <?php if($message): ?>
        <div class="alert <?= strpos($message,'successfully')!==false ? 'alert-success' : 'alert-error' ?>">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <form method="POST">

    <label>Student</label>
    <select name="student_id" required>
        <option value="">-- Select Student --</option>
        <?php
        $students = $conn->query("SELECT id,name FROM logs WHERE role='user' ORDER BY name ASC");
        while($row=$students->fetch_assoc()):
        ?>
            <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
        <?php endwhile; ?>
    </select>

    <label style="margin-top:15px;">Incident Date</label>
    <input type="date" name="incident_date" required value="<?= date('Y-m-d') ?>">

    <label style="margin-top:15px;">Incident Type</label>
    <input type="text" name="incident_type" required>

    <label style="margin-top:15px;">Location of Incident</label>
    <input type="text" name="incident_location" placeholder="Where the incident happened" required>

    <label style="margin-top:15px;">Description</label>
    <textarea name="description" rows="4"></textarea>

    <label style="margin-top:15px;">Action Taken</label>
    <textarea name="action_taken" rows="3"></textarea>

    <div style="margin-top:20px; display:flex; gap:10px;">
        <a href="counselor_dashboard.php" style="background:#bbb;padding:10px 20px;border-radius:10px;color:#000;text-decoration:none;">Back</a>
        <button type="submit" style="background:#e74c3c;color:white;">Save Record</button>
    </div>
</form>


</div>

<script>
// DARK MODE
if(localStorage.getItem("theme")==="dark") document.body.classList.add("dark");
document.getElementById("darkModeToggle").onclick=function(){
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark")?"dark":"light");
};

// LOGOUT CONFIRM
document.getElementById('logoutLink').addEventListener('click',e=>{
    if(!confirm("Logout now?")) e.preventDefault();
});
</script>

</body>
</html>
