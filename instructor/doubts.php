<?php
session_start();
include("../config.php");

// Check instructor login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor'){
    header("Location: ../login.php");
    exit();
}

$instructor_id = $_SESSION['user_id'];

// Handle reply
if(isset($_POST['reply_submit'])){
    $doubt_id = intval($_POST['doubt_id']);
    $reply = trim($_POST['reply']);

    if(!empty($reply)){
        $stmt = $conn->prepare("UPDATE doubts SET reply=?, status='answered' WHERE id=?");
        $stmt->bind_param("si", $reply, $doubt_id);
        $stmt->execute();
        $msg = "Reply sent successfully!";
    }
}

// Get doubts
$doubts = $conn->query("
    SELECT d.*, m.module_name, u.name AS student_name
    FROM doubts d
    JOIN modules m ON d.module_id = m.id
    JOIN users u ON d.student_id = u.id
    JOIN instructor_modules im ON im.module_id = m.id
    WHERE im.instructor_id='$instructor_id'
    ORDER BY d.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Doubts</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{display:flex;min-height:100vh;background:#f4f6f9;}

/* Sidebar (same as dashboard) */
.sidebar{
    width:230px;
    background:#1e293b;
    color:white;
    padding-top:20px;
}
.sidebar h2{text-align:center;margin-bottom:20px;}
.sidebar a{
    display:block;
    padding:15px;
    color:white;
    text-decoration:none;
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
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

/* Doubt Card */
.doubt-card{
    background:white;
    padding:20px;
    margin-bottom:15px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    transition:0.3s;
}
.doubt-card:hover{
    transform:translateY(-3px);
}

/* Status */
.status-pending{color:orange;font-weight:bold;}
.status-answered{color:green;font-weight:bold;}

/* Reply form */
textarea{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
    margin-top:10px;
}

button{
    margin-top:8px;
    background:#2563eb;
    color:white;
    border:none;
    padding:8px 12px;
    border-radius:6px;
    cursor:pointer;
}
button:hover{background:#1d4ed8;}

.msg{
    background:#d1fae5;
    padding:10px;
    border-radius:8px;
    margin-bottom:15px;
    color:#065f46;
}
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
    <h3>Student Doubts</h3>
    <span>Instructor 👨‍🏫</span>
</div>

<?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?>

<?php
if($doubts->num_rows > 0){
    while($d = $doubts->fetch_assoc()){
        echo '<div class="doubt-card">';
        echo '<p><strong>Student:</strong> '.htmlspecialchars($d['student_name']).'</p>';
        echo '<p><strong>Module:</strong> '.htmlspecialchars($d['module_name']).'</p>';
        echo '<p><strong>Question:</strong> '.htmlspecialchars($d['question']).'</p>';

        echo '<p><strong>Status:</strong> 
              <span class="status-'.htmlspecialchars($d['status']).'">'
              .ucfirst($d['status']).'</span></p>';

        if(!empty($d['reply'])){
            echo '<p><strong>Your Reply:</strong> '.htmlspecialchars($d['reply']).'</p>';
        } else {
            echo '
            <form method="POST">
                <input type="hidden" name="doubt_id" value="'.$d['id'].'">
                <textarea name="reply" placeholder="Write your reply..." required></textarea>
                <button name="reply_submit">Send Reply</button>
            </form>';
        }

        echo '<p><small>Asked: '.$d['created_at'].'</small></p>';
        echo '</div>';
    }
}else{
    echo "<p>No doubts yet.</p>";
}
?>

</div>

</body>
</html>