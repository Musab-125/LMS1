<?php
session_start();
include("../config.php");

// Check admin login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

// Handle actions
if(isset($_GET['action'], $_GET['id'])){
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == 'approve'){
        $conn->query("UPDATE instructor_modules SET status='approved' WHERE id=$id");
    } elseif($action == 'reject'){
        $conn->query("UPDATE instructor_modules SET status='rejected' WHERE id=$id");
    }
}

// Get all requests
$requests = $conn->query("
    SELECT im.*, u.name AS instructor_name, m.module_name, d.department_name
    FROM instructor_modules im
    JOIN users u ON im.instructor_id = u.id
    JOIN modules m ON im.module_id = m.id
    JOIN departments d ON m.department_id = d.id
    ORDER BY im.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Module Approvals</title>

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
    text-align:left;
}
th{
    background:#4e73df;
    color:white;
}

/* Buttons */
a.btn{
    padding:6px 10px;
    border-radius:5px;
    color:white;
    text-decoration:none;
    margin-right:5px;
    font-size:14px;
}
.approve{background:#28a745;}
.approve:hover{background:#218838;}
.reject{background:#dc3545;}
.reject:hover{background:#c82333;}

/* Status */
.status-pending{color:orange;font-weight:bold;}
.status-approved{color:green;font-weight:bold;}
.status-rejected{color:red;font-weight:bold;}
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
    <h3>📊 Instructor Module Requests</h3>
    <span>Admin Panel</span>
</div>

<table>
<tr>
    <th>#</th>
    <th>Instructor</th>
    <th>Department</th>
    <th>Module</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($r = $requests->fetch_assoc()){ ?>
<tr>
    <td><?= $r['id'] ?></td>
    <td><?= htmlspecialchars($r['instructor_name']) ?></td>
    <td><?= htmlspecialchars($r['department_name']) ?></td>
    <td><?= htmlspecialchars($r['module_name']) ?></td>

    <td class="status-<?= $r['status'] ?>">
        <?= ucfirst($r['status']) ?>
    </td>

    <td>
        <?php if($r['status']=='pending'){ ?>
            <a class="btn approve" href="?action=approve&id=<?= $r['id'] ?>">Approve</a>
            <a class="btn reject" href="?action=reject&id=<?= $r['id'] ?>">Reject</a>
        <?php } else { ?>
            ---
        <?php } ?>
    </td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>