<?php
session_start();
include "db.php";

/* ROLE CHECK */
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','counselor'])) {
    header('Location: login.php');
    exit;
}

/* VALIDATE ID */
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) die('Invalid ID');
$id = (int)$_GET['id'];

/* FETCH RECORD — MATCHES list_discipline_counseling.php */
$stmt = $conn->prepare("
    SELECT 
        cr.*,
        s.name AS student_name,
        s.id   AS stud_code
    FROM counseling_records cr
    JOIN logs s ON cr.student_id = s.id
    WHERE cr.id = ?
");
$stmt->bind_param('i', $id);
$stmt->execute();
$rec = $stmt->get_result()->fetch_assoc();

if (!$rec) die('Record not found');

/* UPDATE */
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $session_date = $_POST['session_date'] ?? '';
    $notes        = trim($_POST['notes'] ?? '');
    $follow       = isset($_POST['follow_up']) ? 1 : 0;

    $u = $conn->prepare("
        UPDATE counseling_records
        SET session_date = ?, notes = ?, follow_up_required = ?
        WHERE id = ?
    ");
    $u->bind_param('ssii', $session_date, $notes, $follow, $id);

    if ($u->execute()) {
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
<title>Edit Counseling</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
*{box-sizing:border-box}
body{
    margin:0;
    font-family:Inter,Segoe UI,system-ui,sans-serif;
    background:linear-gradient(135deg,#2980b9,#6dd5fa,#ffffff);
    min-height:100vh;
    display:flex
}
.sidebar{
    width:260px;height:100vh;position:fixed;left:0;top:0;
    backdrop-filter:blur(12px);
    background:rgba(0,0,0,0.35);
    padding:25px 20px;
    display:flex;flex-direction:column
}
.sidebar h2{color:#fff;text-align:center;margin-bottom:25px}
.sidebar a{
    padding:12px 14px;margin:6px 0;border-radius:10px;
    color:#fff;text-decoration:none;display:flex;gap:10px;
    background:rgba(255,255,255,0.05);transition:.25s
}
.sidebar a:hover{background:rgba(255,255,255,0.18);transform:translateX(5px)}
.sidebar .active{background:rgba(255,255,255,0.35);font-weight:600}

.content{margin-left:260px;padding:36px;flex:1}
.card{
    background:#fff;border-radius:12px;padding:22px;
    box-shadow:0 10px 30px rgba(2,6,23,0.12);
    max-width:900px
}
.input{
    width:100%;padding:12px;border-radius:10px;
    border:1px solid #e3e6ee
}
.actions{display:flex;gap:10px;margin-top:16px}
.btn-primary{
    background:#2d9cdb;color:#fff;
    padding:10px 16px;border-radius:10px;border:none
}
.btn{
    padding:10px 16px;border-radius:10px;
    background:#eee;text-decoration:none;color:#333
}
</style>
</head>

<body>

<div class="sidebar">
    <h2>Counselor Panel</h2>
    <a href="counselor_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
    <a href="student_counselor.php"><i class="fa fa-users"></i> Manage Students</a>
    <a href="list_discipline_counseling.php" class="active"><i class="fa fa-file-alt"></i> Records</a>
    <a href="add_counseling.php"><i class="fa fa-user-plus"></i> Add Counseling</a>
    <a href="add_discipline.php"><i class="fa fa-exclamation-circle"></i> Add Discipline</a>
    <a href="analytics_dashboard.php"><i class="fa fa-chart-line"></i> Analytics Dashboard</a>
    <a href="logout.php" id="logoutLink"><i class="fa fa-door-open"></i> Logout</a>
</div>

<div class="content">
<div class="card">
<div style="display:flex;justify-content:space-between;align-items:center">
    <div>
        <h2>Edit Counseling Session</h2>
        <small>
            Student: <?= htmlspecialchars($rec['student_name']) ?> (ID <?= $rec['stud_code'] ?>)
        </small>
    </div>
    <a href="list_discipline_counseling.php" class="btn">Back</a>
</div>

<?php if($msg): ?>
<div style="background:#fdd;padding:10px;border-radius:8px"><?= $msg ?></div>
<?php endif; ?>

<form method="post">
    <label>Session Date</label>
    <input class="input" type="date" name="session_date"
           value="<?= htmlspecialchars($rec['session_date']) ?>" required>

    <label style="margin-top:12px">Notes</label>
    <textarea class="input" name="notes" rows="6"><?= htmlspecialchars($rec['notes']) ?></textarea>

    <div style="margin-top:10px">
        <label>
            <input type="checkbox" name="follow_up"
                <?= $rec['follow_up_required'] ? 'checked' : '' ?>>
            Require Follow-up
        </label>
    </div>

    <div class="actions">
        <button class="btn-primary"><i class="fa fa-save"></i> Save</button>
        <a class="btn" href="delete_counseling.php?id=<?= $id ?>"
           onclick="return confirm('Delete this counseling session?')">
           Delete
        </a>
    </div>
</form>
</div>
</div>

<script>
document.getElementById('logoutLink').onclick = e => {
    if(!confirm('Logout now?')) e.preventDefault();
};
</script>

</body>
</html>
