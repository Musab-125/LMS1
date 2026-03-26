<?php
session_start();
include("../config.php");

// Admin session check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

// Handle add department
if(isset($_POST['add_department'])){
    $department_name = trim($_POST['department_name']);
    if(!empty($department_name)){
        $conn->query("INSERT INTO departments (department_name) VALUES ('". $conn->real_escape_string($department_name) ."')");
    }
}

// Fetch all departments
$departments = $conn->query("SELECT * FROM departments");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Departments</title>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{display:flex;min-height:100vh;background:#f4f6f9;}

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
    transition:0.2s;
}
.sidebar a:hover{background:#334155;}

/* Main */
.main{flex:1;padding:20px;}

/* Topbar */
.topbar{
    background:white;
    padding:15px 20px;
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
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    margin-bottom:20px;
}

/* Form */
.card input{
    width:300px;
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
    margin-right:10px;
}
.card button{
    padding:10px 15px;
    border-radius:6px;
    background:#2563eb;
    color:white;
    border:none;
    cursor:pointer;
}
.card button:hover{background:#1d4ed8;}

/* Table */
table{
    width:100%;
    border-collapse:collapse;
    background:white;
    border-radius:10px;
    overflow:hidden;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}
th,td{
    padding:12px;
    border-bottom:1px solid #ddd;
}
th{
    background:#4e73df;
    color:white;
    text-align:left;
}
tr:hover{
    background:#f9fafb;
}
</style>

</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>🎓 LMS Admin</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="instructors.php">Instructors</a>
    <a href="approvals.php">Module Approvals</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Main -->
<div class="main">

<div class="topbar">
    <h3>Manage Departments</h3>
    <span>Admin Panel</span>
</div>

<!-- Add Department -->
<div class="card">
    <h4>Add New Department</h4><br>
    <form method="POST">
        <input type="text" name="department_name" placeholder="Enter Department Name" required>
        <button type="submit" name="add_department">Add</button>
    </form>
</div>

<!-- Department List -->
<div class="card">
    <h4>All Departments</h4><br>

    <table>
        <tr>
            <th>ID</th>
            <th>Department Name</th>
        </tr>

        <?php while($d=$departments->fetch_assoc()){ ?>
        <tr>
            <td><?= $d['id'] ?></td>
            <td><?= htmlspecialchars($d['department_name']) ?></td>
        </tr>
        <?php } ?>

    </table>
</div>

</div>

</body>
</html>