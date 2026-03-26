<?php
session_start();
include("../config.php");

// Check instructor login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor'){
    header("Location: ../login.php");
    exit();
}

$instructor_id = $_SESSION['user_id'];

// Get instructor department
$instructor = $conn->query("SELECT * FROM users WHERE id='$instructor_id'")->fetch_assoc();
$department_id = $instructor['department_id'];

// Handle module request
if(isset($_GET['request_module'])){
    $module_id = intval($_GET['request_module']);

    $check = $conn->query("SELECT * FROM instructor_modules 
                           WHERE instructor_id='$instructor_id' AND module_id='$module_id'");

    if($check->num_rows == 0){
        $conn->query("INSERT INTO instructor_modules (instructor_id, module_id, status) 
                      VALUES ('$instructor_id','$module_id','pending')");
    }

    header("Location: modules.php");
    exit();
}

// Get modules
$modules = $conn->query("
    SELECT m.*, 
    (SELECT status FROM instructor_modules 
     WHERE instructor_id='$instructor_id' AND module_id=m.id LIMIT 1) as request_status
    FROM modules m
    WHERE m.department_id='$department_id'
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Modules</title>

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
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
}

th, td{
    padding:12px;
    border-bottom:1px solid #ddd;
    text-align:left;
}

th{
    background:#f8fafc;
}

/* Buttons */
.btn{
    padding:6px 10px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:14px;
}

.request{background:#2563eb;}
.pending{background:orange;}
.approved{background:green;}
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
    <h3>My Department Modules</h3>
    <span>Request modules to teach</span>
</div>

<div class="card">

<table>
<tr>
    <th>ID</th>
    <th>Module Name</th>
    <th>Status</th>
</tr>

<?php while($m = $modules->fetch_assoc()){ ?>
<tr>
    <td><?= $m['id'] ?></td>
    <td><?= htmlspecialchars($m['module_name']) ?></td>
    <td>
        <?php if(!$m['request_status']){ ?>
            <a class="btn request" href="?request_module=<?= $m['id'] ?>">Request</a>
        <?php } elseif($m['request_status']=='pending'){ ?>
            <span class="btn pending">Pending</span>
        <?php } else { ?>
            <span class="btn approved">Approved</span>
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>

</div>

</div>

</body>
</html>