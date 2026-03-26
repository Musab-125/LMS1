<?php
session_start();

// Check instructor login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor'){
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$instructor_id = $_SESSION['user_id'];

// =========================
// 1. Instructor Details
// =========================
$sql = "SELECT u.name, u.email, d.department_name 
        FROM users u
        LEFT JOIN departments d ON u.department_id = d.id
        WHERE u.id='$instructor_id'";

$result = mysqli_query($conn, $sql);
if(!$result){ die("SQL Error: ".mysqli_error($conn)); }

$instructor = mysqli_fetch_assoc($result);

// =========================
// 2. Approved Modules
// =========================
$q1 = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM instructor_modules 
    WHERE instructor_id='$instructor_id' AND status='approved'
");
$modules = mysqli_fetch_assoc($q1)['total'];

// =========================
// 3. Pending Requests
// =========================
$q2 = mysqli_query($conn, "
    SELECT COUNT(*) as total 
    FROM instructor_modules 
    WHERE instructor_id='$instructor_id' AND status='pending'
");
$pending = mysqli_fetch_assoc($q2)['total'];

// =========================
// 4. Students Count
// =========================
$q3 = mysqli_query($conn, "
    SELECT COUNT(DISTINCT e.student_id) as total
    FROM enrollments e
    JOIN instructor_modules im ON e.module_id = im.module_id
    WHERE im.instructor_id='$instructor_id' AND im.status='approved'
");
$students = mysqli_fetch_assoc($q3)['total'];
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Instructor Dashboard</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{display:flex;min-height:100vh;background:#f4f6f9;}

/* Sidebar */
.sidebar{
    width:230px;
    background:#1e293b;
    color:white;
    padding-top:20px;
    display:flex;
    flex-direction:column;
}
.sidebar h2{
    text-align:center;
    margin-bottom:20px;
}
.sidebar a{
    display:block;
    padding:15px;
    color:white;
    text-decoration:none;
    transition:0.2s;
}
.sidebar a:hover{background:#334155;}

/* Main */
.main{flex:1;padding:20px;}
.topbar{
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}
.cards{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
}
.card{
    background:white;
    padding:20px;
    border-radius:10px;
    text-align:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
    transition:0.3s;
}
.card:hover{transform:translateY(-3px);}
.card h3{font-size:30px;color:#2563eb;}
.card p{margin-top:10px;color:#555;font-weight:bold;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Instructor Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="modules.php">Request Modules</a>
    <a href="add_lesson.php">Add Lesson</a>
    <a href="my_lessons.php">My Lessons</a>
    <a href="add_quiz.php">Add Quiz</a>
    <a href="doubts.php">Student Doubts</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Main -->
<div class="main">

<div class="topbar">
    <h3>Welcome, <?= htmlspecialchars($instructor['name']); ?></h3>
    <span><?= htmlspecialchars($instructor['department_name']); ?></span>
</div>

<div class="cards">
    <div class="card">
        <h3><?= $modules ?></h3>
        <p>Approved Modules</p>
    </div>

    <div class="card">
        <h3><?= $students ?></h3>
        <p>Total Students</p>
    </div>

    <div class="card">
        <h3><?= $pending ?></h3>
        <p>Pending Requests</p>
    </div>
</div>

</div>

</body>
</html>