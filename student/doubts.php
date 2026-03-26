<?php
session_start();
include("../config.php");

// Check student login
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

// Handle form submission
if(isset($_POST['ask_doubt'])){
    $module_id = intval($_POST['module_id']);
    $question = trim($_POST['question']);

    if(!empty($question)){
        $stmt = $conn->prepare("INSERT INTO doubts (student_id, module_id, question, status) VALUES (?,?,?, 'pending')");
        $stmt->bind_param("iis", $user_id, $module_id, $question);
        $stmt->execute();
        $msg = "Your question has been submitted!";
    }
}

// Get all modules for student's department
$modules = $conn->query("SELECT * FROM modules WHERE department_id=".$user['department_id']);

// Get all doubts by this student
$doubts = $conn->query("
    SELECT d.*, m.module_name 
    FROM doubts d
    JOIN modules m ON d.module_id = m.id
    WHERE d.student_id='$user_id'
    ORDER BY d.created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Doubts</title>
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
form{background:white;padding:20px;border-radius:12px;box-shadow:0 4px 12px rgba(0,0,0,0.1);margin-bottom:20px;}
form select, form textarea, form button{width:100%;padding:10px;margin-bottom:10px;border-radius:6px;border:1px solid #ccc;}
form button{background:#4e73df;color:white;border:none;cursor:pointer;transition:0.3s;}
form button:hover{background:#2e59d9;}
.doubt-card{background:white;padding:15px;margin-bottom:10px;border-radius:10px;box-shadow:0 3px 10px rgba(0,0,0,0.1);}
.doubt-card p{margin-bottom:5px;}
.status-pending{color:orange;font-weight:bold;}
.status-answered{color:green;font-weight:bold;}
.msg{color:green;margin-bottom:10px;}
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
        <h3>My Doubts</h3>
        <span><?= htmlspecialchars($user['name']) ?> 👋</span>
    </div>

    <?php if(isset($msg)){ echo "<p class='msg'>$msg</p>"; } ?>

    <!-- Ask a doubt form -->
    <form method="POST">
        <label>Select Module</label>
        <select name="module_id" required>
            <option value="">-- Select Module --</option>
            <?php while($mod = $modules->fetch_assoc()){ ?>
                <option value="<?= $mod['id'] ?>"><?= htmlspecialchars($mod['module_name']) ?></option>
            <?php } ?>
        </select>

        <label>Question</label>
        <textarea name="question" rows="4" placeholder="Type your question..." required></textarea>

        <button type="submit" name="ask_doubt">Submit Question</button>
    </form>

    <!-- List of doubts -->
    <?php
    if($doubts->num_rows > 0){
        while($d = $doubts->fetch_assoc()){
            echo '<div class="doubt-card">';
            echo '<p><strong>Module:</strong> '.htmlspecialchars($d['module_name']).'</p>';
            echo '<p><strong>Question:</strong> '.htmlspecialchars($d['question']).'</p>';
            echo '<p><strong>Status:</strong> <span class="status-'.htmlspecialchars($d['status']).'">'.ucfirst($d['status']).'</span></p>';
            if(!empty($d['reply'])){
                echo '<p><strong>Instructor Reply:</strong> '.htmlspecialchars($d['reply']).'</p>';
            }
            echo '<p><small>Asked on: '.htmlspecialchars($d['created_at']).'</small></p>';
            echo '</div>';
        }
    } else {
        echo "<p>No doubts submitted yet.</p>";
    }
    ?>
</div>

</body>
</html>