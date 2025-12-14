<?php
session_start();
include "db.php";

// ROLE CHECK → Only students allowed
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'user') {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>My Profile</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* -------- GLOBAL STYLING -------- */
* { box-sizing: border-box; }
body {
    margin: 0; padding: 0;
    font-family: "Segoe UI", sans-serif;
    background: linear-gradient(135deg, #2980b9, #6dd5fa, #ffffff);
    min-height: 100vh;
    display: flex;
    transition: .3s;
}
body.dark { background: #111 !important; color: #eee; }

/* -------- SIDEBAR -------- */
.sidebar {
    width: 260px; height: 100vh;
    background: rgba(0,0,0,0.35);
    backdrop-filter: blur(12px);
    padding-top: 30px;
    position: fixed;
    box-shadow: 0 0 25px rgba(0,0,0,0.3);
    display: flex; flex-direction: column; justify-content: space-between;
}
.sidebar h2 { color: white; text-align: center; margin-bottom: 30px; font-size: 24px; }
.sidebar a {
    display: block; padding: 14px 25px; color: white; font-size: 16px;
    text-decoration: none; border-left: 4px solid transparent; transition: .3s;
}
.sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.15); border-left: 4px solid #fff; }
.sidebar a i { margin-right: 12px; font-size: 18px; }
#darkModeToggle {
    margin: 20px; padding: 12px; width: calc(100% - 40px);
    background: #444; color: white; border: none; border-radius: 12px; cursor: pointer;
    font-size: 16px; transition: 0.3s;
}
#darkModeToggle:hover { background: #555; }

/* -------- MAIN CONTENT -------- */
.content {
    margin-left: 260px; padding: 50px 40px;
    width: calc(100% - 260px); display: flex; flex-direction: column; gap: 30px;
}
.page-title { color: white; font-size: 34px; font-weight: bold; }

/* -------- CARDS -------- */
.dashboard-cards {
    display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}
.card-box {
    background: rgba(255,255,255,0.55); border-radius: 20px;
    padding: 40px 35px; backdrop-filter: blur(12px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    position: relative;
}
body.dark .card-box { background: rgba(40,40,40,0.75); }
.card-box:hover { transform: translateY(-5px); box-shadow: 0 25px 40px rgba(0,0,0,0.25); }
.card-box i { font-size: 60px; margin-bottom: 20px; color: #3498db; }
.card-title { font-size: 22px; font-weight: 600; margin-top: 10px; }
.card-value { font-size: 18px; margin-top: 15px; line-height: 1.6; color: #222; }
body.dark .card-value { color: #eee; }

/* -------- FORM STYLING -------- */
.profile-form { text-align: left; margin-top: 10px; }
.profile-form label {
    display: block; font-weight: 600; margin-bottom: 5px; margin-top: 15px;
    transition: 0.3s;
}
.profile-form input, .profile-form textarea {
    width: 100%; padding: 12px; border-radius: 12px;
    border: 1px solid #ccc; outline: none; font-size: 16px; transition: 0.3s;
}
.profile-form input:focus, .profile-form textarea:focus {
    border-color: #2980b9; box-shadow: 0 0 8px rgba(41,128,185,0.4);
}
.profile-form button {
    margin-top: 25px; padding: 12px 20px; font-size: 16px;
    border-radius: 12px; cursor: pointer; background: #111; color: white; border: none;
    transition: 0.3s;
}
.profile-form button:hover { background: #333; }

/* -------- RESPONSIVE -------- */
@media (max-width: 768px) {
    .content { padding: 30px 20px; margin-left: 0; width: 100%; }
    .sidebar { width: 100%; height: auto; position: relative; display: flex; flex-direction: row; overflow-x: auto; padding: 10px 0; }
    .sidebar a { flex: 1; text-align: center; border-left: none; }
    .dashboard-cards { grid-template-columns: 1fr; }
}

/* -------- FLOATING LABEL ANIMATION -------- */
.profile-form .input-wrapper { position: relative; margin-top: 20px; }
.profile-form .input-wrapper input,
.profile-form .input-wrapper textarea {
    padding-top: 18px;
}
.profile-form .input-wrapper label {
    position: absolute;
    top: -1px; left: 12px;
    pointer-events: none;
    color: #555;
    font-size: 14px;
    transition: 0.2s ease all;
}
.profile-form .input-wrapper input:focus + label,
.profile-form .input-wrapper input:not(:placeholder-shown) + label,
.profile-form .input-wrapper textarea:focus + label,
.profile-form .input-wrapper textarea:not(:placeholder-shown) + label {
    top: -14px; left: 12px; font-size: 12px; color: #2980b9;
    background: rgba(255,255,255,0.55); padding: 0 4px; border-radius: 4px;
}
</style>
</head>
<body>

<div class="sidebar">
    <div>
        <h2>Student Panel</h2>
        <a href="student_dashboard.php"><i class="fa fa-home"></i> Dashboard</a>
        <a href="my_profile.php" class="active"><i class="fa fa-user"></i> My Profile</a>
        <a href="student_report.php"><i class="fa fa-book"></i> My Records</a>
    </div>
    <div>
        <a href="logout.php" id="logoutLink"><i class="fa fa-sign-out"></i> Logout</a>
        <button id="darkModeToggle"><i class="fa fa-moon"></i> Dark Mode</button>
    </div>
</div>

<div class="content">
    <div class="page-title">My Profile 👤</div>

    <div class="dashboard-cards">
        <!-- PROFILE INFO CARD -->
        <div class="card-box" id="profileCard">
            <i class="fa fa-id-card"></i>
            <div class="card-title">Profile Information</div>
            <div class="card-value" id="profilePreview">
                <strong>Name:</strong> <?= htmlspecialchars($user['name']) ?><br>
                <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?><br>
                <strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A') ?><br>
                <strong>Address:</strong> <?= htmlspecialchars($user['address'] ?? 'N/A') ?><br>
                <strong>Role:</strong> Student
            </div>
        </div>

        <!-- EDIT PROFILE CARD -->
        <div class="card-box">
            <i class="fa fa-edit" style="color:#27ae60;"></i>
            <div class="card-title">Edit Profile</div>
            <div class="card-value">
                <form class="profile-form" id="profileForm" action="update_profile.php" method="POST">
                    <div class="input-wrapper">
                        <input type="text" name="name" id="name" placeholder=" " value="<?= htmlspecialchars($user['name']) ?>" required>
                        <label for="name">Full Name</label>
                    </div>
                    <div class="input-wrapper">
                        <input type="email" name="email" id="email" placeholder=" " value="<?= htmlspecialchars($user['email']) ?>" required>
                        <label for="email">Email</label>
                    </div>
                    <div class="input-wrapper">
                        <input type="text" name="phone" id="phone" placeholder=" " value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
                        <label for="phone">Phone</label>
                    </div>
                    <div class="input-wrapper">
                        <textarea name="address" id="address" placeholder=" "><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                        <label for="address">Address</label>
                    </div>

                    <button type="submit">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// -------- DARK MODE --------
if (localStorage.getItem("theme") === "dark") document.body.classList.add("dark");
document.getElementById("darkModeToggle").onclick = function() {
    document.body.classList.toggle("dark");
    localStorage.setItem("theme", document.body.classList.contains("dark") ? "dark" : "light");
};

// -------- LOGOUT CONFIRM --------
document.getElementById('logoutLink').addEventListener('click', function(e){
    if(!confirm("Logout now?")) e.preventDefault();
});

// -------- LIVE PROFILE PREVIEW --------
const nameInput = document.getElementById('name');
const emailInput = document.getElementById('email');
const phoneInput = document.getElementById('phone');
const addressInput = document.getElementById('address');
const profilePreview = document.getElementById('profilePreview');

function updatePreview() {
    profilePreview.innerHTML = `
        <strong>Name:</strong> ${nameInput.value || 'N/A'}<br>
        <strong>Email:</strong> ${emailInput.value || 'N/A'}<br>
        <strong>Phone:</strong> ${phoneInput.value || 'N/A'}<br>
        <strong>Address:</strong> ${addressInput.value || 'N/A'}<br>
        <strong>Role:</strong> Student
    `;
}

[nameInput, emailInput, phoneInput, addressInput].forEach(input => {
    input.addEventListener('input', updatePreview);
});

// -------- FORM VALIDATION & CONFIRMATION --------
document.getElementById('profileForm').addEventListener('submit', function(e){
    e.preventDefault();

    const name = nameInput.value.trim();
    const email = emailInput.value.trim();

    if(name.length < 3){
        alert("Name must be at least 3 characters long.");
        return;
    }
    if(!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
        alert("Invalid email format.");
        return;
    }

    if(confirm("Are you sure you want to update your profile?")){
        this.submit();
    }
});
</script>

</body>
</html>
