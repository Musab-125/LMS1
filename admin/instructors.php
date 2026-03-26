<?php
session_start();
include("../config.php");

// Check admin login
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../login.php");
    exit();
}

// Fetch departments
$departments = $conn->query("SELECT * FROM departments");

// Add instructor
if(isset($_POST['add_instructor'])){
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $department_id = intval($_POST['department_id']);

    if(!empty($name) && !empty($email) && $department_id > 0){
        $conn->query("INSERT INTO users (name,email,password,role,status,department_id) 
                     VALUES ('$name','$email','$password','instructor','active','$department_id')");
    }
}

// Filter
$selected_department = isset($_GET['department_id']) ? intval($_GET['department_id']) : 0;

// Actions
if(isset($_GET['action'], $_GET['id'])){
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if($action == 'block'){
        $conn->query("UPDATE users SET status='blocked' WHERE id=$id AND role='instructor'");
    } elseif($action == 'unblock'){
        $conn->query("UPDATE users SET status='active' WHERE id=$id AND role='instructor'");
    } elseif($action == 'delete'){
        $conn->query("DELETE FROM users WHERE id=$id AND role='instructor'");
    }
}

// Fetch instructors
$where = $selected_department > 0 
    ? "WHERE role='instructor' AND department_id=$selected_department" 
    : "WHERE role='instructor'";

$instructors = $conn->query("
    SELECT u.*, d.department_name
    FROM users u
    LEFT JOIN departments d ON u.department_id = d.id
    $where
    ORDER BY u.id DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Instructors</title>

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
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

/* Card */
.card{
    background:white;
    padding:20px;
    border-radius:12px;
    margin-bottom:20px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

/* Inputs */
.card input,
.card select{
    padding:10px;
    margin:5px;
    border-radius:6px;
    border:1px solid #ccc;
}

/* Button */
.card button{
    padding:10px 15px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:6px;
    cursor:pointer;
}
.card button:hover{background:#1d4ed8;}

/* Filter */
.filter{
    margin-bottom:15px;
}

/* Table */
.table-card{
    background:white;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

table{width:100%;border-collapse:collapse;}
th{
    background:#2563eb;
    color:white;
    padding:12px;
}
td{
    padding:12px;
    border-bottom:1px solid #eee;
}

/* Buttons */
.btn{
    padding:6px 10px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:13px;
}

.block{background:#f59e0b;}
.unblock{background:#10b981;}
.delete{background:#ef4444;}

.btn:hover{opacity:0.85;}

/* Status */
.status-active{color:#10b981;font-weight:bold;}
.status-blocked{color:#ef4444;font-weight:bold;}
</style>

</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="instructors.php">Instructors</a>
    <a href="approvals.php">Module Approvals</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Main -->
<div class="main">

<div class="topbar">
    <h3>Manage Instructors</h3>
    <span>Admin</span>
</div>

<!-- Add Instructor -->
<div class="card">
<h4>Add New Instructor</h4>

<form method="POST">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>

    <select name="department_id" required>
        <option value="">Select Department</option>
        <?php while($d = $departments->fetch_assoc()){ ?>
            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['department_name']) ?></option>
        <?php } ?>
    </select>

    <button type="submit" name="add_instructor">Add Instructor</button>
</form>
</div>

<!-- Filter -->
<div class="card filter">
<form method="GET">
    <select name="department_id" onchange="this.form.submit()">
        <option value="0">All Departments</option>
        <?php
        $departments = $conn->query("SELECT * FROM departments");
        while($d = $departments->fetch_assoc()){ ?>
            <option value="<?= $d['id'] ?>" <?= ($selected_department == $d['id'])?'selected':'' ?>>
                <?= htmlspecialchars($d['department_name']) ?>
            </option>
        <?php } ?>
    </select>
</form>
</div>

<!-- Table -->
<div class="table-card">

<?php if($instructors->num_rows > 0){ ?>
<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Department</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while($i = $instructors->fetch_assoc()){ ?>
<tr>
    <td><?= $i['id'] ?></td>
    <td><?= htmlspecialchars($i['name']) ?></td>
    <td><?= htmlspecialchars($i['email']) ?></td>
    <td><?= htmlspecialchars($i['department_name']) ?></td>
    <td class="status-<?= $i['status'] ?>"><?= ucfirst($i['status']) ?></td>
    <td>
        <?php if($i['status']=='active'){ ?>
            <a class="btn block" href="?action=block&id=<?= $i['id'] ?>">Block</a>
        <?php } else { ?>
            <a class="btn unblock" href="?action=unblock&id=<?= $i['id'] ?>">Unblock</a>
        <?php } ?>
        <a class="btn delete" href="?action=delete&id=<?= $i['id'] ?>" onclick="return confirm('Delete this instructor?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>
<?php } else { ?>
    <p style="padding:20px;">No instructors found.</p>
<?php } ?>

</div>

</div>

</body>
</html>