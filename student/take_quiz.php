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

// Get quiz_id from URL
if(!isset($_GET['quiz_id'])){
    header("Location: quizzes.php");
    exit();
}
$quiz_id = intval($_GET['quiz_id']);

// Get quiz info
$quiz_check = $conn->query("SELECT * FROM quizzes WHERE id='$quiz_id'");
if($quiz_check->num_rows == 0){
    die("Quiz not found.");
}
$quiz = $quiz_check->fetch_assoc();

// Get all questions
$questions = $conn->query("SELECT * FROM questions WHERE quiz_id='$quiz_id'");

// Handle form submission
if(isset($_POST['submit_quiz'])){
    $score = 0;

    while($q = $questions->fetch_assoc()){
        $qid = $q['id'];
        $selected = isset($_POST['question_'.$qid]) ? intval($_POST['question_'.$qid]) : 0;
        if($selected == $q['correct_option']){
            $score++;
        }
    }

    // Store result
    $stmt = $conn->prepare("INSERT INTO results (student_id, quiz_id, score) VALUES (?,?,?)");
    $stmt->bind_param("iii", $user_id, $quiz_id, $score);
    $stmt->execute();

    header("Location: results.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Take Quiz - <?= htmlspecialchars($quiz['title']) ?></title>
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
.question-card{background:white;padding:20px;margin-bottom:15px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);}
.question-card h4{margin-bottom:10px;}
.question-card label{display:block;margin-bottom:8px;cursor:pointer;}
button.submit-btn{padding:10px 15px;border:none;border-radius:6px;background:#4e73df;color:white;cursor:pointer;transition:0.3s;margin-top:10px;}
button.submit-btn:hover{background:#2e59d9;}
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
        <h3>Quiz: <?= htmlspecialchars($quiz['title']) ?></h3>
        <span><?= htmlspecialchars($user['name']) ?> 👋</span>
    </div>

    <form method="POST">
    <?php
    $questions->data_seek(0); // Reset result pointer
    $q_no = 1;
    while($q = $questions->fetch_assoc()){
        echo '<div class="question-card">';
        echo '<h4>Q'.$q_no.': '.htmlspecialchars($q['question']).'</h4>';
        for($i=1;$i<=4;$i++){
            $option = $q['option'.$i];
            echo '<label><input type="radio" name="question_'.$q['id'].'" value="'.$i.'"> '.htmlspecialchars($option).'</label>';
        }
        echo '</div>';
        $q_no++;
    }
    ?>
    <button type="submit" name="submit_quiz" class="submit-btn">Submit Quiz</button>
    </form>
</div>

</body>
</html>