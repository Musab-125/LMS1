<?php
session_start();
include("../config.php");

/* Session check */
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Get student info securely */
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* Safe: If user not found (just in case) */
if(!$user){
    session_destroy();
    header("Location: ../login.php");
    exit();
}

/* Counts for cards */
$department_id = $user['department_id'];

/* Total modules in student's department */
$stmt_mod = $conn->prepare("SELECT COUNT(*) AS total FROM modules WHERE department_id=?");
$stmt_mod->bind_param("i", $department_id);
$stmt_mod->execute();
$module_count = $stmt_mod->get_result()->fetch_assoc();

/* Total quizzes */
$stmt_quiz = $conn->prepare("SELECT COUNT(*) AS total FROM quizzes");
$stmt_quiz->execute();
$quiz_count = $stmt_quiz->get_result()->fetch_assoc();

/* Total doubts by this student */
$stmt_doubt = $conn->prepare("SELECT COUNT(*) AS total FROM doubts WHERE student_id=?");
$stmt_doubt->bind_param("i", $user_id);
$stmt_doubt->execute();
$doubt_count = $stmt_doubt->get_result()->fetch_assoc();

/* Total results by this student */
$stmt_result = $conn->prepare("SELECT COUNT(*) AS total FROM results WHERE student_id=?");
$stmt_result->bind_param("i", $user_id);
$stmt_result->execute();
$result_count = $stmt_result->get_result()->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
/* Reset */
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:'Segoe UI',sans-serif;
}

/* Body */
body{
    display:flex;
    min-height:100vh;
    background:#f0f2f5;
}

/* Sidebar */
.sidebar{
    width:220px;
    background:#2c3e50;
    color:white;
    display:flex;
    flex-direction:column;
    padding-top:20px;
}
.sidebar h2{
    text-align:center;
    margin-bottom:20px;
}
.sidebar a{
    padding:15px 20px;
    text-decoration:none;
    color:white;
    transition:0.2s;
}
.sidebar a:hover{
    background:#34495e;
}

/* Main content */
.main{
    flex:1;
    padding:20px;
}

/* Topbar */
.topbar{
    background:white;
    padding:15px 20px;
    border-radius:10px;
    margin-bottom:20px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* Cards */
.cards{
    display:flex;
    flex-wrap:wrap;
    gap:20px;
}

.card{
    background:white;
    flex:1 1 200px;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    transition:0.3s;
    text-align:center;
}

.card:hover{
    transform:translateY(-5px);
}

.card h2{
    font-size:32px;
    margin-bottom:10px;
}

.card p{
    font-size:16px;
    margin-bottom:15px;
}

.card a{
    text-decoration:none;
    background:#4e73df;
    color:white;
    padding:8px 12px;
    border-radius:6px;
    transition:0.3s;
}

.card a:hover{
    background:#2e59d9;
}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>🎓 LMS</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="modules.php">Modules</a>
    <a href="doubts.php">Doubts</a>
    <a href="quizzes.php">Quizzes</a>
    <a href="results.php">Results</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Main content -->
<div class="main">
    <div class="topbar">
        <h3>Welcome, <?= htmlspecialchars($user['name']) ?> 👋</h3>
        <span>Student Panel</span>
    </div>

    <div class="cards">
        <div class="card">
            <h2><?= $module_count['total'] ?></h2>
            <p>Modules</p>
            <a href="modules.php">View</a>
        </div>

        <div class="card">
            <h2><?= $doubt_count['total'] ?></h2>
            <p>My Doubts</p>
            <a href="doubts.php">Open</a>
        </div>

        <div class="card">
            <h2><?= $quiz_count['total'] ?></h2>
            <p>Quizzes</p>
            <a href="quizzes.php">Start</a>
        </div>

        <div class="card">
            <h2><?= $result_count['total'] ?></h2>
            <p>Results</p>
            <a href="results.php">View</a>
        </div>
    </div>
</div>

</body>
</html>