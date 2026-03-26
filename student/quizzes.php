<?php
session_start();
include("../config.php");

// Check if student is logged in
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

// Get all quizzes for student's department modules
$dept_id = $user['department_id'];
$quizzes = $conn->query("
    SELECT q.*, m.module_name 
    FROM quizzes q
    JOIN modules m ON q.module_id = m.id
    WHERE m.department_id = '$dept_id'
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Quizzes</title>
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
.quiz-card{background:white;padding:20px;margin-bottom:15px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);transition:0.3s;}
.quiz-card:hover{transform:translateY(-3px);}
.quiz-card h3{margin-bottom:10px;}
.quiz-card p{margin-bottom:10px;color:#555;}
.quiz-card a{text-decoration:none;background:#4e73df;color:white;padding:6px 10px;border-radius:5px;transition:0.3s;}
.quiz-card a:hover{background:#2e59d9;}
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
        <h3>Quizzes</h3>
        <span><?= htmlspecialchars($user['name']) ?> 👋</span>
    </div>

    <?php
    if($quizzes->num_rows > 0){
        while($quiz = $quizzes->fetch_assoc()){
            echo '<div class="quiz-card">';
            echo '<h3>'.htmlspecialchars($quiz['title']).'</h3>';
            echo '<p>Module: '.htmlspecialchars($quiz['module_name']).'</p>';
            echo '<a href="take_quiz.php?quiz_id='.$quiz['id'].'">Attempt Quiz</a>';
            echo '</div>';
        }
    } else {
        echo "<p>No quizzes available for your department yet.</p>";
    }
    ?>
</div>

</body>
</html>