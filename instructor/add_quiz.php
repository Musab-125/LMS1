<?php
session_start();
include("../config.php");

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor'){
    header("Location: ../login.php");
    exit();
}

$instructor_id = $_SESSION['user_id'];

// Get approved modules
$modules = $conn->query("
    SELECT m.* 
    FROM modules m
    JOIN instructor_modules im ON m.id = im.module_id
    WHERE im.instructor_id='$instructor_id' AND im.status='approved'
");

$msg = "";

// Create quiz
if(isset($_POST['create_quiz'])){
    $module_id = intval($_POST['module_id']);
    $title = trim($_POST['title']);

    if(!empty($title)){
        $conn->query("INSERT INTO quizzes (module_id, title) VALUES ('$module_id', '$title')");
        $quiz_id = $conn->insert_id;

        header("Location: add_questions.php?quiz_id=$quiz_id");
        exit();
    } else {
        $msg = "Quiz title is required!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Quiz</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}

body{
    display:flex;
    min-height:100vh;
    background:#f0f2f5;
}

/* Sidebar */
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
.main{
    flex:1;
    padding:20px;
}

/* Topbar */
.topbar{
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

/* Card */
.card{
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    max-width:500px;
}

/* Inputs */
.card input,
.card select{
    width:100%;
    padding:10px;
    margin-bottom:15px;
    border-radius:6px;
    border:1px solid #ccc;
}

/* Button */
.card button{
    background:#2563eb;
    color:white;
    border:none;
    padding:10px;
    border-radius:6px;
    cursor:pointer;
}
.card button:hover{background:#1d4ed8;}

/* Message */
.msg{
    color:red;
    margin-bottom:10px;
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
    <h3>Create Quiz</h3>
    <span>Instructor Panel</span>
</div>

<div class="card">

<?php if($msg){ echo "<div class='msg'>$msg</div>"; } ?>

<form method="POST">

<label>Select Module</label>
<select name="module_id" required>
    <option value="">Select Module</option>
    <?php while($m = $modules->fetch_assoc()){ ?>
        <option value="<?= $m['id']; ?>">
            <?= htmlspecialchars($m['module_name']); ?>
        </option>
    <?php } ?>
</select>

<label>Quiz Title</label>
<input type="text" name="title" placeholder="Enter quiz title" required>

<button type="submit" name="create_quiz">Create Quiz</button>

</form>

</div>

</div>

</body>
</html>