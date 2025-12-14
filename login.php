<?php
session_start();
require "db.php";

$err = "";

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'] ?? '';

    if ($role === 'user') {  // student
        header("Location: student_dashboard.php");
        exit();
    } elseif ($role === 'counselor') {
        header("Location: counselor_dashboard.php");
        exit();
    } elseif ($role === 'admin') {
        header("Location: admin_dashboard.php");
        exit();
    }

    session_destroy();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $remember = isset($_POST["remember"]);

    if (!$email || !$password) {
        $err = "Please fill in all fields.";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role FROM logs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 0) {
            $err = "Email does not exist.";
        } else {
            $user = $res->fetch_assoc();

            if (!password_verify($password, $user["password"])) {
                $err = "Incorrect password.";
            } else {

                // Secure session
                session_regenerate_id(true);

                $_SESSION['user'] = [
                    "id"    => $user["id"],
                    "name"  => $user["name"],
                    "email" => $user["email"],
                    "role"  => $user["role"]
                ];

                // Remember Me
                if ($remember) {
                    setcookie("remember_email", $email, time() + (86400 * 30), "/");
                } else {
                    setcookie("remember_email", "", time() - 3600, "/");
                }

                // Redirect based on actual DB role
                if ($user['role'] === 'user') {
                    header("Location: student_dashboard.php");
                } elseif ($user['role'] === 'counselor') {
                    header("Location: counselor_dashboard.php");
                } elseif ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    $err = "Invalid role in database.";
                    session_destroy();
                }

                exit();
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
<title>Advanced Login</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: "Segoe UI", sans-serif;
}

body {
    min-height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background: linear-gradient(135deg, #2b5876, #4e4376);
    background-image: url('lyceum.jpeg'); /* your logo as background */
    background-size: cover;   /* makes it cover the whole area */
    background-position: center;
    background-repeat: no-repeat;
    overflow: hidden;
    transition: .3s;
}

body.dark {
    background: #111 url('lyceum.jpeg') no-repeat center center;
    background-size: cover;
    color: #eee;
}



.container {
    width: 380px;
    padding: 30px;
    background: rgba(255,255,255,0.12);
    box-shadow: 0 8px 32px rgba(0,0,0,0.3);
    backdrop-filter: blur(8px);
    border-radius: 15px;
    color: #fff;
    animation: fadeIn 0.6s ease;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-15px); }
    to { opacity: 1; transform: translateY(0); }
}

h2 {
    text-align: center;
    margin-bottom: 20px;
}

input {
    width: 100%;
    padding: 12px;
    margin: 10px 0;
    border-radius: 8px;
    border: none;
    outline: none;
}

button { width:100%; padding:14px; margin:10px 0; background:#2d9cdb; color:#fff; border:none; border-radius:14px; font-size:16px; cursor:pointer; transition:.3s; text-decoration:none; display:inline-block; text-align:center; }
button:hover { background:#1b7bbf; transform:scale(1.05); box-shadow:0 8px 20px rgba(27,123,191,0.4); }


.error-box {
    background: rgba(255,0,0,0.2);
    padding: 10px;
    border-left: 4px solid red;
    margin-bottom: 12px;
    border-radius: 6px;
}

.register-box {
    margin-top: 15px;
    text-align: center;
}

.register-box a {
    color: #ffe;
    text-decoration: underline;
    font-weight: bold;
}

label {
    font-size: 14px;
}

</style>
</head>

<body>

<div class="container">
    <h2>Account Login</h2>

    <?php if ($err): ?>
        <div class="error-box"><?= $err ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="email" 
               name="email" 
               value="<?= $_COOKIE['remember_email'] ?? '' ?>"
               placeholder="Email" required>

        <input type="password" name="password" placeholder="Password" required>

        <label><input type="checkbox" name="remember"> Remember Me</label>

        <button type="submit">Login</button>
    </form>

    <div class="register-box">
        <p>Don’t have an account?</p>
        <a href="register.php">Register here</a>
    </div>
</div>

</body>
</html>
