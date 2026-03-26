<?php
session_start();
include("../config.php");

// Check login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get student info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Get all results for this student
$results = $conn->query("
    SELECT r.*, q.title AS quiz_title, m.module_name 
    FROM results r
    JOIN quizzes q ON r.quiz_id = q.id
    JOIN modules m ON q.module_id = m.id
    WHERE r.student_id='$user_id'
    ORDER BY r.taken_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Results</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{display:flex;min-height:100vh;background:#f0f2f5;}
.sidebar{width:220px;background:#2c3e50;color:white;display:flex;flex-direction:column;padding-top:20px;}
.sidebar h2{text-align:center;margin-bottom:20px;}
.sidebar a{padding:15px 20px;text-decoration:none;color:white;transition:0.2s;}
.sidebar a:hover{background:#34495e;}
.main{flex:1;padding:20px;}
.topbar{background:white;padding:15px 20px;border-radius:10px;margin-bottom:20px;box-shadow:0 2px 8px rgba(0,0,0,0.1);display:flex;justify-content:space-between;align-items:center;}
.result-card{background:white;padding:20px;margin-bottom:15px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.result-card h4{margin-bottom:10px;}
.result-card p{margin-bottom:5px;color:#555;}
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
        <h3>My Results</h3>
        <span><?= htmlspecialchars($user['name']) ?> 👋</span>
    </div>

    <?php
    if($results->num_rows > 0){
        while($res = $results->fetch_assoc()){
            echo '<div class="result-card">';
            echo '<h4>Quiz: '.htmlspecialchars($res['quiz_title']).'</h4>';
            echo '<p>Module: '.htmlspecialchars($res['module_name']).'</p>';
            echo '<p>Score: '.intval($res['score']).'</p>';
            echo '<p>Taken at: '.htmlspecialchars($res['taken_at']).'</p>';
            echo '</div>';
        }
    } else {
        echo "<p>No results found yet.</p>";
    }
    ?>
</div>

</body>
</html>