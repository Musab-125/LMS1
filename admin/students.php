<?php
session_start();
include("../config.php");

// Admin session check
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

// Get all departments
$departments = $conn->query("SELECT * FROM departments");

// Selected department filter
$selected_department = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

// Handle actions
if(isset($_GET['action'], $_GET['id'])){
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == 'block'){
        $conn->query("UPDATE users SET status='blocked' WHERE id=$id AND role='student'");
    } elseif($action == 'unblock'){
        $conn->query("UPDATE users SET status='active' WHERE id=$id AND role='student'");
    } elseif($action == 'delete'){
        $conn->query("DELETE FROM users WHERE id=$id AND role='student'");
    }
}

// Fetch students
if($selected_department > 0){
    $students = $conn->query("
        SELECT u.*, d.department_name
        FROM users u
        LEFT JOIN departments d ON u.department_id = d.id
        WHERE role='student' AND department_id = $selected_department
        ORDER BY u.id DESC
    ");
} else {
    $students = $conn->query("
        SELECT u.*, d.department_name
        FROM users u
        LEFT JOIN departments d ON u.department_id = d.id
        WHERE role='student'
        ORDER BY u.id DESC
    ");
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Students Management</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}

body{
    display:flex;
    min-height:100vh;
    background:#f4f6f9;
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
.main{flex:1;padding:20px;}

/* Topbar */
.topbar{
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

/* Filter Card */
.filter-card{
    background:white;
    padding:15px;
    border-radius:10px;
    margin-bottom:20px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

/* Select */
select{
    padding:10px;
    border-radius:6px;
    border:1px solid #ccc;
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

th, td{
    padding:12px;
    border-bottom:1px solid #eee;
    text-align:left;
}

th{
    background:#2563eb;
    color:white;
}

/* Buttons */
.action-btn{
    padding:6px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:13px;
    margin-right:5px;
}

.block{background:#f59e0b;}
.unblock{background:#10b981;}
.delete{background:#ef4444;}

.action-btn:hover{opacity:0.85;}

/* Status */
.status-active{color:#10b981;font-weight:bold;}
.status-blocked{color:#ef4444;font-weight:bold;}

/* Empty */
.empty{
    background:white;
    padding:20px;
    border-radius:10px;
    text-align:center;
    color:#555;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
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
    <h3>Manage Students</h3>
    <span>Admin Panel</span>
</div>

<!-- Filter -->
<div class="filter-card">
<form method="GET">
    <label><b>Filter by Department:</b></label><br><br>
    <select name="department_id" onchange="this.form.submit()">
        <option value="0">All Departments</option>
        <?php while($d = $departments->fetch_assoc()){ ?>
            <option value="<?= $d['id'] ?>" <?= ($selected_department == $d['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($d['department_name']) ?>
            </option>
        <?php } ?>
    </select>
</form>
</div>

<!-- Table -->
<?php if($students->num_rows > 0){ ?>
<table>
<tr>
    <th>#</th>
    <th>Name</th>
    <th>Email</th>
    <th>Department</th>
    <th>Status</th>
    <th>Action</th>
</tr>

<?php while($s = $students->fetch_assoc()){ ?>
<tr>
    <td><?= $s['id'] ?></td>
    <td><?= htmlspecialchars($s['name']) ?></td>
    <td><?= htmlspecialchars($s['email']) ?></td>
    <td><?= htmlspecialchars($s['department_name']) ?></td>

    <td class="status-<?= $s['status'] ?>">
        <?= ucfirst($s['status']) ?>
    </td>

    <td>
        <?php if($s['status']=='active'){ ?>
            <a class="action-btn block" href="?action=block&id=<?= $s['id'] ?>">Block</a>
        <?php } else { ?>
            <a class="action-btn unblock" href="?action=unblock&id=<?= $s['id'] ?>">Unblock</a>
        <?php } ?>

        <a class="action-btn delete"
           href="?action=delete&id=<?= $s['id'] ?>"
           onclick="return confirm('Are you sure to delete this student?')">
           Delete
        </a>
    </td>
</tr>
<?php } ?>

</table>

<?php } else { ?>
<div class="empty">
    <h3>No students found</h3>
</div>
<?php } ?>

</div>

</body>
</html>