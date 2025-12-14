<?php
session_start();
include "db.php";

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$err = "";
$success = "";

// UPDATE ACCOUNT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST["name"]);
    $email = trim($_POST["email"]);

    if (!$name || !$email) {
        $err = "All fields are required.";
    } else {
        $stmt = $conn->prepare("UPDATE logs SET name=?, email=? WHERE id=?");
        $stmt->bind_param("ssi", $name, $email, $user['id']);
        if ($stmt->execute()) {
            $_SESSION['user']['name']  = $name;
            $_SESSION['user']['email'] = $email;
            $success = "Account updated successfully!";
        } else {
            $err = "Update failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit Account</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    margin: 0;
    padding: 0;
    font-family: Arial, sans-serif;
    background: #f0f2f5;
}

.navbar {
    width: 100%;
    background: #1f5eff;
    padding: 15px 20px;
    color: white;
    font-size: 20px;
    font-weight: bold;
}

.container {
    max-width: 450px;
    background: white;
    margin: 40px auto;
    padding: 30px;
    border-radius: 18px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    animation: fadeIn 0.5s ease-in-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.98); }
    to { opacity: 1; transform: scale(1); }
}

h2 {
    text-align: center;
    margin-bottom: 20px;
    color: #333;
}

label {
    font-size: 15px;
    font-weight: bold;
    display: block;
    margin-bottom: 5px;
}

input {
    width: 100%;
    padding: 12px;
    border: 1px solid #bbb;
    border-radius: 10px;
    margin-bottom: 15px;
    font-size: 15px;
}

button {
    width: 100%;
    padding: 12px;
    background: #1f5eff;
    border: none;
    color: white;
    border-radius: 10px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.2s;
}

button:hover {
    background: #0044cc;
}

.alert {
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 10px;
    font-weight: bold;
    text-align: center;
}

.error { background: #ffcccc; color: #a30000; }
.success { background: #d4f8d4; color: #0f7b0f; }

.back-link {
    display: block;
    margin-top: 15px;
    text-align: center;
    color: #1f5eff;
    font-weight: bold;
    text-decoration: none;
}

.back-link:hover {
    text-decoration: underline;
}
</style>
</head>

<body>

<div class="navbar">
    <i class="fa fa-user-gear"></i> Edit Account
</div>

<div class="container">
    <h2>Edit Your Account</h2>

    <?php if ($err): ?>
        <div class="alert error"><?= $err ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert success"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST">
        <label>Name</label>
        <input type="text" name="name" value="<?= $user['name'] ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>

        <button type="submit"><i class="fa fa-save"></i> Update Account</button>
    </form>

    <a class="back-link" href="student_counselor.php">
        <i class="fa fa-arrow-left"></i> Back to Manage Accounts
    </a>
</div>

</body>
</html>
