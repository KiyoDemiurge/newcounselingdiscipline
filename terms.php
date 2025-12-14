<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Terms of Service - Student Discipline System</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
* { margin:0; padding:0; box-sizing:border-box; font-family: 'Poppins', sans-serif; }

body {
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:40px 20px;
    background: linear-gradient(135deg, #2b5876, #4e4376);
    background-image: url('lyceum.jpeg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    overflow:hidden;
    transition:.3s;
}

body::before {
    content:"";
    position:absolute; top:0; left:0; width:100%; height:100%;
    background: rgba(0,0,0,0.4);
    z-index:0;
}

body.dark {
    background:#111 url('lyceum.jpeg') no-repeat center center;
    background-size: cover;
    color:#eee;
}

.container {
    width:100%;
    max-width:800px;
    padding:40px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(18px);
    border-radius: 25px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.4);
    position: relative;
    z-index:1;
    color:#fff;
    animation: fadeIn 0.8s ease;
}

@keyframes fadeIn { from{opacity:0; transform:translateY(-20px);} to{opacity:1; transform:translateY(0);} }

h1 { text-align:center; font-size:28px; margin-bottom:20px; }
h2 { font-size:20px; margin-top:25px; margin-bottom:10px; }
p { margin-bottom:12px; line-height:1.6; color:#e0e0e0; }
ul { margin-left:20px; margin-bottom:12px; }

a { color:#4ade80; text-decoration:underline; }

.dark-toggle {
    margin-top:15px;
    cursor:pointer;
    font-size:14px;
    background:#444;
    color:#fff;
    border:none;
    padding:10px 12px;
    border-radius:8px;
}

/* Floating background shapes */
.bg-shape {
    position:absolute;
    border-radius:50%;
    opacity:0.15;
    filter: blur(80px);
    animation: float 8s infinite alternate;
    z-index:0;
}
.bg1 { width:300px; height:300px; background:#4ade80; top:-100px; left:-100px; animation-duration:10s; }
.bg2 { width:400px; height:400px; background:#22c55e; bottom:-150px; right:-150px; animation-duration:12s; }
.bg3 { width:250px; height:250px; background:#2d9cdb; top:50%; left:70%; animation-duration:15s; }
@keyframes float { 0%{transform: translateY(0px) translateX(0px);} 50%{transform: translateY(20px) translateX(15px);} 100%{transform: translateY(-10px) translateX(-10px);} }

.footer { text-align:center; font-size:12px; color:#aaa; margin-top:20px; }
.footer a { color:#aaa; text-decoration:none; margin:0 5px; }
.footer a:hover { text-decoration:underline; }
</style>
</head>
<body>

<!-- Floating shapes -->
<div class="bg-shape bg1"></div>
<div class="bg-shape bg2"></div>
<div class="bg-shape bg3"></div>

<div class="container">
    <h1>Terms of Service</h1>
    <p>Welcome to the Student Discipline & Counseling Record System. By using this system, you agree to comply with and be bound by the following terms and conditions.</p>

    <h2>1. Acceptance of Terms</h2>
    <p>By accessing or using this system, you agree to follow these terms. If you do not agree, please do not use the system.</p>

    <h2>2. User Responsibilities</h2>
    <ul>
        <li>Provide accurate and current information when registering.</li>
        <li>Maintain the confidentiality of your login credentials.</li>
        <li>Use the system only for authorized purposes.</li>
    </ul>

    <h2>3. Data Usage</h2>
    <p>All student records, counseling information, and other data entered in this system are confidential. Users must not share data with unauthorized individuals.</p>

    <h2>4. System Access</h2>
    <p>Access to the system may be restricted, monitored, or suspended at the discretion of administrators to ensure security and proper use.</p>

    <h2>5. Intellectual Property</h2>
    <p>The system and its content are the property of Lyceum of Southern Luzon Balayan Campus and are protected by intellectual property laws.</p>

    <h2>6. Limitation of Liability</h2>
    <p>The system is provided "as is". The institution is not liable for any damages resulting from the use or inability to use the system.</p>

    <h2>7. Modifications</h2>
    <p>We may update these terms from time to time. Changes will be posted on this page, and continued use constitutes acceptance of updated terms.</p>

    <div class="footer">
        &copy; 2025 Lyceum of Southern Luzon Balayan Campus | 
        <a href="index.php">Home</a> | 
        <a href="policy.php">Privacy Policy</a> | 
    </div>
</div>



</body>
</html>
