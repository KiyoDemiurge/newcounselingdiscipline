<?php
session_start();
include "db.php";

// ROLE CHECK
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['counselor', 'admin'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// Get filter/search input
$search     = trim($_GET['search'] ?? "");
$filterDate = trim($_GET['date'] ?? "");
$type       = trim($_GET['type'] ?? "all");

// Escape
$search_esc = $conn->real_escape_string($search);
$date_esc   = $conn->real_escape_string($filterDate);

// ---------------------- DISCIPLINE RECORDS ----------------------
$discipline_sql = "
    SELECT 
        dr.id,
        s.id AS stud_id,
        s.name AS student_name,
        u.name AS counselor_name,
        dr.incident_date,
        dr.incident_type,
        dr.incident_location,  -- added location
        dr.description,
        dr.action_taken
    FROM discipline_records dr
    JOIN logs s ON dr.student_id = s.id
    JOIN logs u ON dr.counselor_id = u.id
    WHERE s.role = 'user'
";

if (($type === "all" || $type === "discipline") && $search_esc !== "") {
    $discipline_sql .= " AND (
        s.name LIKE '%{$search_esc}%'
        OR dr.incident_type LIKE '%{$search_esc}%'
        OR dr.description LIKE '%{$search_esc}%'
        OR dr.incident_location LIKE '%{$search_esc}%'
    )";
}

if (($type === "all" || $type === "discipline") && $date_esc !== "") {
    $discipline_sql .= " AND dr.incident_date = '{$date_esc}'";
}

$discipline_sql .= " ORDER BY dr.incident_date DESC";
$discipline_res = $conn->query($discipline_sql);

// ---------------------- COUNSELING RECORDS ----------------------
$counseling_sql = "
    SELECT 
        cr.id,
        s.id AS stud_id,
        s.name AS student_name,
        u.name AS counselor_name,
        cr.session_date,
        cr.notes,
        cr.follow_up_required,
        cr.status
    FROM counseling_records cr
    JOIN logs s ON cr.student_id = s.id
    JOIN logs u ON cr.counselor_id = u.id
    WHERE s.role = 'user'
";

if (($type === "all" || $type === "counseling") && $search_esc !== "") {
    $counseling_sql .= " AND (
        s.name LIKE '%{$search_esc}%'
        OR cr.notes LIKE '%{$search_esc}%'
    )";
}

if (($type === "all" || $type === "counseling") && $date_esc !== "") {
    $counseling_sql .= " AND cr.session_date = '{$date_esc}'";
}

$counseling_sql .= " ORDER BY cr.session_date DESC";
$counseling_res = $conn->query($counseling_sql);
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Discipline & Counseling Records</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* ------------------------- GLOBAL ------------------------- */
body {
    margin:0;
    font-family:"Segoe UI",sans-serif;
    display:flex;
    min-height:100vh;
    background:linear-gradient(135deg,#2980b9,#6dd5fa,#fff);
    transition:.3s;
}
body.dark {
    background:#111 !important;
    color:#eee;
}

/* ------------------------- SIDEBAR ------------------------- */
.sidebar {
    width:260px;
    background:rgba(0,0,0,.35);
    padding-top:30px;
    position:fixed;
    height:100vh;
    box-shadow:0 0 25px rgba(0,0,0,0.3);
}
.sidebar h2 {
    color: white;
    text-align: center;
    margin-bottom: 25px;
    font-size: 22px;
}
.sidebar h2,.sidebar a{color:#fff;}
.sidebar a {
    display:block;
    padding:14px 25px;
    text-decoration:none;
    transition:.2s;
}
.sidebar a:hover{background:rgba(255,255,255,.15);}
.sidebar i {margin-right:10px;}
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
    margin-left:260px;
    padding:30px;
    width:calc(100% - 260px);
}
.page-title {font-size:30px;font-weight:bold;margin-bottom:20px;}

/* ------------------------- FILTER ------------------------- */
.filter-box{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
    background:#fff;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
}
.filter-box input,
.filter-box select,
.filter-box button{
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
}
.filter-box button{
    background:#2980b9;
    color:white;
    border:none;
}

/* ------------------------- TABLES ------------------------- */
table{
    width:100%;
    border-collapse:collapse;
    background:#fff;
    margin-bottom:30px;
}
th,td{padding:12px;border-bottom:1px solid #ccc;text-align:left;}
th{background:#2980b9;color:#fff;}
.btn-edit{background:#27ae60;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;}
.btn-delete{background:#c0392b;color:#fff;padding:6px 12px;border-radius:6px;text-decoration:none;}

/* ------------------------- DARK MODE ------------------------- */
body.dark .sidebar { background: rgba(0,0,0,0.7); }
body.dark .filter-box { background: rgba(40,40,40,0.7); border-color: #555; }
body.dark table { background: rgba(40,40,40,0.75); color:#eee; }
body.dark th { background: #1f2f3a; color:#fff; }
body.dark td { border-color: #555; color:#eee; }
body.dark .btn-edit { background:#27ae60; color:#fff; }
body.dark .btn-delete { background:#c0392b; color:#fff; }
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
<div class="page-title">Records</div>

<form class="filter-box">
    <input type="text" name="search" placeholder="Search..." value="<?= htmlspecialchars($search) ?>">
    <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>">
    <select name="type">
        <option value="all" <?= $type=="all"?"selected":"" ?>>All</option>
        <option value="discipline" <?= $type=="discipline"?"selected":"" ?>>Discipline</option>
        <option value="counseling" <?= $type=="counseling"?"selected":"" ?>>Counseling</option>
    </select>
    <button type="submit">Filter</button>
</form>

<h2>Discipline Records</h2>
<table>
<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Counselor</th>
    <th>Date</th>
    <th>Type</th>
    <th>Location</th> <!-- added -->
    <th>Description</th>
    <th>Action</th>
</tr>
<?php if($discipline_res->num_rows): while($r=$discipline_res->fetch_assoc()): ?>
<tr>
<td><?= $r['id'] ?></td>
<td><?= htmlspecialchars($r['student_name']) ?></td>
<td><?= htmlspecialchars($r['counselor_name']) ?></td>
<td><?= $r['incident_date'] ?></td>
<td><?= $r['incident_type'] ?></td>
<td><?= htmlspecialchars($r['incident_location']) ?></td> <!-- added -->
<td><?= $r['description'] ?></td>
<td>
<a href="edit_discipline.php?id=<?= $r['id'] ?>" class="btn-edit">Edit</a>
<a href="delete_discipline.php?id=<?= $r['id'] ?>" class="btn-delete">Delete</a>
</td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="8" style="text-align:center">No discipline records found</td></tr>
<?php endif; ?>
</table>

<h2>Counseling Records</h2>
<table>
<tr>
    <th>ID</th>
    <th>Student</th>
    <th>Counselor</th>
    <th>Date</th>
    <th>Notes</th>
    <th>Follow Up</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php if($counseling_res->num_rows): while($r=$counseling_res->fetch_assoc()): ?>
<tr>
    <td><?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['student_name']) ?></td>
    <td><?= htmlspecialchars($r['counselor_name']) ?></td>
    <td><?= $r['session_date'] ?></td>
    <td><?= htmlspecialchars($r['notes']) ?></td>
    <td><?= $r['follow_up_required'] ? 'Yes' : 'No' ?></td>
    <td><?= ucfirst($r['status']) ?></td>
    <td>
        <a href="edit_counseling.php?id=<?= $r['id'] ?>" class="btn-edit">Edit</a>
        <a href="delete_counseling.php?id=<?= $r['id'] ?>" class="btn-delete" onclick="return confirm('Delete this counseling record?')">Delete</a>
        <a href="done_counseling.php?id=<?= $r['id'] ?>" class="btn-edit" onclick="return confirm('Mark this counseling as finished?')">Finish</a>
    </td>
</tr>
<?php endwhile; else: ?>
<tr><td colspan="8" style="text-align:center;">No counseling records found</td></tr>
<?php endif; ?>
</table>

</div>

<script>
// LOGOUT CONFIRM
document.getElementById('logoutLink').addEventListener('click', function(e){
    if(!confirm("Logout now?")) e.preventDefault();
});

// DARK MODE
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
