<?php
session_start();
include "db.php";

// Only allow counselor/admin
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','counselor'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_GET['id'] ?? 0;

// Fetch student
$student = $conn->query("SELECT * FROM logs WHERE role='user'")->fetch_assoc();
if (!$student) {
    die("Student not found.");
}

// Fetch discipline summary
$discipline_q = $conn->query("SELECT COUNT(*) AS total, MAX(incident_date) AS last_date 
                              FROM discipline_records 
                              WHERE student_id='$student_id'");
$discipline = $discipline_q->fetch_assoc();

// Fetch counseling summary
$counsel_q = $conn->query("SELECT COUNT(*) AS total, MAX(session_date) AS last_session 
                           FROM counseling_records 
                           WHERE student_id='$student_id'");
$counsel = $counsel_q->fetch_assoc();

// Behavior timeline
$timeline_q = $conn->query("
    SELECT 'discipline' AS type, description AS details, incident_date as event_date 
    FROM discipline_records WHERE student_id='$student_id'
    UNION
    SELECT 'counseling', notes, session_date as event_date
    FROM counseling_records WHERE student_id='$student_id'
    ORDER BY event_date DESC
");
$students = $conn->query("SELECT * FROM students WHERE id='?'")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<title>Student Profile</title>

<!-- EXACT SAME DESIGN AS student_counselor.php -->
<style>
body {
    margin: 0;
    font-family: Arial;
    background: #ecf0f5;
}

/* SIDEBAR */


/* CONTENT */
.content {
    margin-left: 270px;
    padding: 25px;
}

/* CARDS (same style as dashboard) */
.card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    margin-bottom: 25px;
}

.profile-header {
    display: flex;
    gap: 20px;
    align-items: center;
}

.profile-photo {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    background: #ddd;
    background-size: cover;
    background-position: center;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

.timeline-item {
    padding: 12px;
    background: #f0f4ff;
    border-left: 4px solid #1a237e;
    border-radius: 6px;
    margin-bottom: 12px;
}
</style>
</head>

<body>


<!-- MAIN CONTENT -->
<div class="content">

    <!-- Back / Manage button -->
    <div style="text-align:right; margin-bottom:15px;">
        <a href="student_counselor.php?id=<?= $student_id ?>" 
           style="background:#1a237e; color:white; padding:10px 15px; border-radius:8px; text-decoration:none; font-weight:bold;">
           Manage Accounts
        </a>
    </div>

    <!-- DISCIPLINE SUMMARY -->
    <div class="card">
        <h3>⚖ Discipline Summary</h3>
        <p>Total Cases: <b><?= $discipline['total'] ?></b></p>
        <p>Last Case: <b><?= $discipline['last_date'] ?: 'None' ?></b></p>
    </div>

    <!-- COUNSELING SUMMARY -->
    <div class="card">
        <h3>🧠 Counseling Summary</h3>
        <p>Total Sessions: <b><?= $counsel['total'] ?></b></p>
        <p>Last Session: <b><?= $counsel['last_session'] ?: 'None' ?></b></p>
    </div>

    <!-- TIMELINE -->
    <div class="card">
        <h3>📅 Behavior Timeline</h3>

        <?php while($t = $timeline_q->fetch_assoc()): ?>
            <div class="timeline-item">
                <b><?= ucfirst($t['type']) ?></b> • <?= $t['event_date'] ?><br>
                <?= $t['details'] ?>
            </div>
        <?php endwhile; ?>
    </div>

</div>

</body>
</html>
