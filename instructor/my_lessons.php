<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor'){
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$instructor_id = $_SESSION['user_id'];

// DELETE LESSON
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM lessons WHERE id='$id' AND instructor_id='$instructor_id'");
    header("Location: my_lessons.php");
}

// GET LESSONS
$lessons = mysqli_query($conn, "
    SELECT l.*, m.module_name
    FROM lessons l
    JOIN modules m ON l.module_id = m.id
    WHERE l.instructor_id='$instructor_id'
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Lessons</title>

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

/* Table Card */
.card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
}

table th, table td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:left;
}

table th{
    background:#f8fafc;
}

/* Buttons */
.btn{
    padding:5px 10px;
    border-radius:5px;
    text-decoration:none;
    color:white;
    font-size:14px;
}

.edit{background:#10b981;}
.delete{background:#ef4444;}
.view{background:#2563eb;}

.btn:hover{opacity:0.8;}
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
    <h3>My Lessons</h3>
    <a href="dashboard.php" class="btn view">⬅ Dashboard</a>
</div>

<div class="card">

<table>
<tr>
    <th>Title</th>
    <th>Module</th>
    <th>Video</th>
    <th>PDF</th>
    <th>Actions</th>
</tr>

<?php while($row = mysqli_fetch_assoc($lessons)) { ?>
<tr>
    <td><?= htmlspecialchars($row['title']); ?></td>
    <td><?= htmlspecialchars($row['module_name']); ?></td>

    <td>
        <?php if($row['video_url']) { ?>
            <a class="btn view" href="<?= $row['video_url']; ?>" target="_blank">YouTube</a>
        <?php } ?>

        <?php if($row['video_file']) { ?>
            <br><br>
            <a class="btn view" href="../uploads/videos/<?= $row['video_file']; ?>" target="_blank">Video</a>
        <?php } ?>
    </td>

    <td>
        <?php if($row['file_path']) { ?>
            <a class="btn view" href="../uploads/pdfs/<?= $row['file_path']; ?>" target="_blank">PDF</a>
        <?php } ?>
    </td>

    <td>
        <a class="btn edit" href="edit_lesson.php?id=<?= $row['id']; ?>">Edit</a>
        <a class="btn delete" href="my_lessons.php?delete=<?= $row['id']; ?>" onclick="return confirm('Delete this lesson?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</div>

</div>

</body>
</html>