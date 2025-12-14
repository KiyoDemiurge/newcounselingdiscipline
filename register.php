<?php
session_start();
require "db.php";

$err = "";
$success = "";

// Fixed counselor code
$COUNSELOR_CODE = "COUNS123";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role     = $_POST['role'] ?? '';
    $counselor_code = trim($_POST['counselor_code'] ?? '');

    if (!$name || !$email || !$password || !$role) {
        $err = "All fields are required.";
    }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Invalid email address.";
    }
    elseif ($role === 'counselor' && $counselor_code !== $COUNSELOR_CODE) {
        $err = "Invalid counselor access code.";
    }
    else {

        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM logs WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $err = "Email already registered.";
        } else {

            // INSERT ONLY INTO LOGS
            $stmt = $conn->prepare(
                "INSERT INTO logs (name, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $success = "✅ Account registered successfully! You can now <a href='login.php'>login</a>.";
            } else {
                $err = "❌ Registration failed: " . $stmt->error;
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
<title>Register</title>
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family:"Segoe UI", sans-serif; }
body { height:100vh; display:flex; justify-content:center; align-items:center; background: linear-gradient(135deg, #2b5876, #4e4376); }
.container { width: 400px; padding: 30px; background: rgba(255,255,255,0.12); box-shadow: 0 8px 32px rgba(0,0,0,0.25); backdrop-filter: blur(10px); border-radius: 15px; color:#fff; text-align:center; }
h2 { margin-bottom:20px; }
input, select { width: 100%; padding: 12px; margin: 10px 0; border-radius: 8px; border:none; outline:none; }
button { width:100%; padding:12px; background:#111; border:none; border-radius:8px; color:#fff; cursor:pointer; margin-top:10px; transition:0.2s; }
button:hover { background:#333; }
.error-box { background: rgba(255,0,0,0.2); padding: 10px; margin-bottom:10px; border-left:4px solid red; border-radius:6px; }
.success-box { background: rgba(0,255,0,0.2); padding: 10px; margin-bottom:10px; border-left:4px solid lime; border-radius:6px; }
a { color:#ffe; text-decoration:underline; }
</style>
<script>
function toggleCounselorCode() {
    const role = document.getElementById('role').value;
    const codeField = document.getElementById('counselor-code-field');
    codeField.style.display = role === 'counselor' ? 'block' : 'none';
}
</script>
</head>
<body>

<div class="container">
    <h2>Register Account</h2>

    <?php if($err): ?><div class="error-box"><?= $err ?></div><?php endif; ?>
    <?php if($success): ?><div class="success-box"><?= $success ?></div><?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
        <input type="password" name="password" placeholder="Password" required>

        <select name="role" id="role" onchange="toggleCounselorCode()" required>
            <option value="">-- Select Account Type --</option>
            <option value="counselor" <?= (($_POST['role']??'')=='counselor')?'selected':'' ?>>Counselor</option>
            <option value="user" <?= (($_POST['role']??'')=='user')?'selected':'' ?>>User / Student</option>
        </select>

        <input type="text" name="counselor_code" id="counselor-code-field" placeholder="Counselor Access Code" style="display:none;" value="<?= htmlspecialchars($_POST['counselor_code'] ?? '') ?>">

        <button type="submit">Register</button>
    </form>

    <p style="margin-top:15px;">Already have an account? <a href="login.php">Login here</a></p>
</div>

<script>toggleCounselorCode();</script>
</body>
</html>
