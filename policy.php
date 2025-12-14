<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Privacy Policy - Student Discipline System</title>
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
    <h1>Privacy Policy</h1>
    <p>At Lyceum of Southern Luzon Balayan Campus, we are committed to protecting the privacy of our students, staff, and users. This privacy policy outlines how we collect, use, and protect your information in the Student Discipline & Counseling Record System.</p>

    <h2>Information We Collect</h2>
    <ul>
        <li>Personal identification information (name, email, role)</li>
        <li>Login credentials</li>
        <li>Behavior records and counseling session data</li>
        <li>Cookies and usage information for system functionality</li>
    </ul>

    <h2>How We Use Your Information</h2>
    <ul>
        <li>To manage student behavior and counseling records</li>
        <li>To provide access to authorized users</li>
        <li>To generate reports for administrative purposes</li>
        <li>To improve system performance and user experience</li>
    </ul>

    <h2>Data Protection</h2>
    <p>We implement appropriate security measures, including secure sessions and encrypted passwords, to protect your information from unauthorized access, disclosure, alteration, or destruction.</p>

    <h2>Cookies</h2>
    <p>Cookies may be used to remember your login details for convenience. You can disable cookies in your browser settings, but this may affect system functionality.</p>

    <h2>Third-Party Services</h2>
    <p>We do not share personal information with third-party services except as required for system functionality or legal obligations.</p>

    <h2>Changes to this Policy</h2>
    <p>We may update this privacy policy from time to time. Updates will be posted on this page. Continued use of the system signifies acceptance of the updated policy.</p>


    <div class="footer">
        &copy; 2025 Lyceum of Southern Luzon Balayan Campus | 
        <a href="index.php">Home</a> | 
        <a href="terms.php">Terms of Service</a> | 
    </div>
</div>

</body>
</html>
