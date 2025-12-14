<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user'])) {
    $role = $_SESSION['user']['role'];
    header("Location: {$role}_dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Student Discipline & Counseling System</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
/* Reset and font */
* { margin:0; padding:0; box-sizing:border-box; font-family: 'Poppins', sans-serif; }

body {
    min-height:100vh; 
    display:flex; 
    justify-content:center; 
    align-items:center; 
    background: linear-gradient(135deg, #2b5876, #4e4376);
    background-image: url('lyceum.jpeg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
    overflow:hidden; 
    transition:.3s;
}

/* Overlay for readability */
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
    width:95%; max-width:600px;
    padding:40px;
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(18px);
    border-radius: 25px;
    text-align:center;
    box-shadow: 0 12px 40px rgba(0,0,0,0.4);
    position:relative;
    z-index:1;
    animation: fadeIn 0.8s ease;
}

/* Animations */
@keyframes fadeIn { from{opacity:0; transform:translateY(-20px);} to{opacity:1; transform:translateY(0);} }

h1 { font-size:32px; margin-bottom:10px; font-weight:600; color:#fff; }
h2.tagline { font-size:16px; font-weight:400; margin-bottom:25px; color:#e0e0e0; }

.features-grid {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
    margin-bottom:20px;
}

.feature-card {
    background: rgba(255,255,255,0.1);
    padding:20px;
    border-radius:15px;
    display:flex;
    flex-direction:column;
    align-items:center;
    transition: transform .3s, background .3s;
    color:#fff;
}
.feature-card i { font-size:24px; margin-bottom:10px; color:#4ade80; }
.feature-card:hover { transform: translateY(-5px); background: rgba(255,255,255,0.2); }

.btn { width:100%; padding:14px; margin:10px 0; background:#2d9cdb; color:#fff; border:none; border-radius:14px; font-size:16px; cursor:pointer; transition:.3s; text-decoration:none; display:inline-block; text-align:center; }
.btn:hover { background:#1b7bbf; transform:scale(1.05); box-shadow:0 8px 20px rgba(27,123,191,0.4); }

.testimonial-carousel { font-size:14px; font-style:italic; color:#ccc; margin:20px 0; min-height:40px; }
.testimonial-carousel.fade { transition: opacity .6s ease; opacity:0; }

.footer { font-size:12px; color:#aaa; margin-top:15px; }
.footer a { color:#aaa; text-decoration:none; margin:0 5px; }
.footer a:hover { text-decoration:underline; }

.dark-toggle { margin-top:15px; cursor:pointer; font-size:14px; background:#444; color:#fff; border:none; padding:10px 12px; border-radius:8px; }

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

</style>
</head>
<body>

<!-- Floating shapes -->
<div class="bg-shape bg1"></div>
<div class="bg-shape bg2"></div>
<div class="bg-shape bg3"></div>

<div class="container">
    <h1>Welcome</h1>
    <h2 class="tagline">Empowering schools to manage discipline and guide students effectively.</h2>

    <div class="features-grid">
        <div class="feature-card"><i class="fa fa-user-check"></i><p>Track student behavior</p></div>
        <div class="feature-card"><i class="fa fa-comment-alt"></i><p>Record counseling sessions</p></div>
        <div class="feature-card"><i class="fa fa-chart-bar"></i><p>Generate reports & statistics</p></div>
        <div class="feature-card"><i class="fa fa-tasks"></i><p>Ensure follow-ups & accountability</p></div>
    </div>

    <button class="btn" onclick="window.location.href='login.php'"><i class="fa fa-sign-in-alt"></i> Login</button>
    <button class="btn" onclick="window.location.href='register.php'"><i class="fa fa-user-plus"></i> Register</button>

    <div class="testimonial-carousel" id="testimonial">
        "This system has helped our school maintain discipline and guide students effectively." – Principal
    </div>

    <div class="footer">
        &copy; 2025 Lyceum of Southern Luzon Balayan Campus | 
        <a href="policy.php">Privacy Policy</a> | 
        <a href="terms.php">Terms of Service</a> | 
    </div>
</div>

<script>
// Dark mode toggle

// Testimonial carousel
const testimonials = [
    '"This system made tracking student behavior effortless." – Teacher',
    '"Counseling records are now neatly organized." – Counselor',
    '"Reports are so easy to generate!" – Principal'
];
let index = 0;
const testimonialEl = document.getElementById("testimonial");
setInterval(() => {
    testimonialEl.classList.add("fade");
    setTimeout(() => {
        testimonialEl.textContent = testimonials[index];
        testimonialEl.classList.remove("fade");
        index = (index + 1) % testimonials.length;
    }, 600);
}, 5000);
</script>

</body>
</html>
