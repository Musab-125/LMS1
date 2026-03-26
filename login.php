<?php
session_start();
include "config.php";

$error = "";

if(isset($_POST['login'])){

$email = trim($_POST['email']);
$password = $_POST['password'];

// Prepared statement (SECURITY)
$stmt = $conn->prepare("SELECT * FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if($result->num_rows > 0){

$user = $result->fetch_assoc();

// Check if blocked
if($user['status'] == 'blocked'){
    $error = "Your account is blocked!";
}
elseif(password_verify($password, $user['password'])){

$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['role'] = $user['role'];

// Redirect by role
if($user['role'] == 'admin'){
header("Location: admin/dashboard.php");
}
elseif($user['role'] == 'instructor'){
header("Location: instructor/dashboard.php");
}
else{
header("Location: student/dashboard.php");
}
exit();

}else{
$error = "Wrong password!";
}

}else{
$error = "User not found!";
}

}
?>

<!DOCTYPE html>
<html>
<head>
<title>SLGTI LMS Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI',sans-serif;
}

body{
height:100vh;
display:flex;
justify-content:center;
align-items:center;
background:url('assets/image/login.jpg') no-repeat center/cover;
position:relative;
}

/* Overlay */
body::before{
content:"";
position:absolute;
width:100%;
height:100%;
background:rgba(0,0,0,0.6);
}

/* Card */
.auth-card{
position:relative;
width:100%;
max-width:380px;
padding:40px 30px;
background:rgba(255,255,255,0.1);
backdrop-filter:blur(12px);
border-radius:15px;
box-shadow:0 10px 30px rgba(0,0,0,0.5);
text-align:center;
color:white;
z-index:1;
}

.auth-card h2{
margin-bottom:25px;
font-size:26px;
}

/* Inputs */
.auth-card input{
width:100%;
padding:12px;
margin-bottom:15px;
border:none;
border-radius:8px;
outline:none;
font-size:14px;
}

/* Button */
.auth-card button{
width:100%;
padding:12px;
border:none;
border-radius:8px;
background:#4e73df;
color:white;
font-weight:bold;
cursor:pointer;
transition:0.3s;
}

.auth-card button:hover{
background:#2e59d9;
transform:translateY(-2px);
}

/* Error */
.error-msg{
background:rgba(255,0,0,0.3);
padding:10px;
border-radius:6px;
margin-bottom:15px;
}

/* Links */
.auth-card p{
margin-top:15px;
font-size:14px;
}

.auth-card a{
color:#fff;
text-decoration:underline;
}
</style>
</head>

<body>

<div class="auth-card">

<h2>SLGTI LMS Login</h2>

<?php if(!empty($error)) { ?>
<div class="error-msg"><?= $error; ?></div>
<?php } ?>

<form method="POST">

<input type="email" name="email" placeholder="Enter Email" required>

<input type="password" name="password" placeholder="Enter Password" required>

<button type="submit" name="login">Login</button>

</form>

<p>Don't have an account?
<a href="register.php">Register Here</a>
</p>

</div>

</body>
</html>