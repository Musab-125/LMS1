<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor'){
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$instructor_id = $_SESSION['user_id'];

if(!isset($_GET['id'])){
    header("Location: my_lessons.php");
    exit();
}

$id = $_GET['id'];

// Get lesson (only instructor own lesson)
$result = mysqli_query($conn, "SELECT * FROM lessons WHERE id='$id' AND instructor_id='$instructor_id'");
$lesson = mysqli_fetch_assoc($result);

if(!$lesson){
    die("Lesson not found!");
}

// Update lesson
$msg = "";
if(isset($_POST['update'])){
    $title = $_POST['title'];
    $video_url = $_POST['video_url'];

    mysqli_query($conn, "
        UPDATE lessons 
        SET title='$title', video_url='$video_url'
        WHERE id='$id' AND instructor_id='$instructor_id'
    ");

    $msg = "Lesson Updated Successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Edit Lesson</title>

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
    max-width:600px;
}

/* Inputs */
.card input{
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
    margin-bottom:10px;
    color:green;
    font-weight:bold;
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
    <h3>Edit Lesson</h3>
    <a href="my_lessons.php" style="text-decoration:none;">⬅ Back</a>
</div>

<div class="card">

<?php if($msg){ echo "<div class='msg'>$msg</div>"; } ?>

<form method="post">

<label>Lesson Title</label>
<input type="text" name="title" value="<?= htmlspecialchars($lesson['title']); ?>" required>

<label>YouTube Link</label>
<input type="text" name="video_url" value="<?= htmlspecialchars($lesson['video_url']); ?>">

<button type="submit" name="update">Update Lesson</button>

</form>

</div>

</div>

</body>
</html>