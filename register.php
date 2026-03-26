<?php
include "config.php";

$error = "";

// Get departments
$departments = $conn->query("SELECT * FROM departments");

if (isset($_POST['register'])) {

    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $raw_password = $_POST['password'];
    $department_id = !empty($_POST['department_id']) ? $_POST['department_id'] : NULL;
    $status = "active"; // Students are active by default
    $role = "student"; // Role is fixed to student

    // Password validation
    if (strlen($raw_password) < 4 || strlen($raw_password) > 8) {
        $error = "Password must be 4–8 characters!";
    } else {

        // Check email (prepared statement)
        $stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $check = $stmt->get_result();

        if ($check->num_rows > 0) {
            $error = "Email already exists!";
        } else {

            $password = password_hash($raw_password, PASSWORD_DEFAULT);

            // Insert student
            $stmt = $conn->prepare("INSERT INTO users (name,email,password,role,status,department_id) VALUES (?,?,?,?,?,?)");
            $stmt->bind_param("sssssi", $name, $email, $password, $role, $status, $department_id);

            if ($stmt->execute()) {
                header("Location: login.php");
                exit();
            } else {
                $error = "Something went wrong!";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>SLGTI LMS Student Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{height:100vh;display:flex;justify-content:center;align-items:center;background:url('assets/image/login.jpg') no-repeat center/cover;position:relative;}
body::before{content:"";position:absolute;width:100%;height:100%;background:rgba(0,0,0,0.6);}
.auth-card{position:relative;width:100%;max-width:400px;padding:35px;background:rgba(255,255,255,0.1);backdrop-filter:blur(12px);border-radius:15px;box-shadow:0 10px 30px rgba(0,0,0,0.5);text-align:center;color:white;z-index:1;}
.auth-card h2{margin-bottom:20px;}
.auth-card input, .auth-card select{width:100%;padding:12px;margin-bottom:15px;border:none;border-radius:8px;outline:none;}
.auth-card button{width:100%;padding:12px;border:none;border-radius:8px;background:#4e73df;color:white;font-weight:bold;cursor:pointer;}
.auth-card button:hover{background:#2e59d9;}
.error-msg{background:rgba(255,0,0,0.3);padding:10px;border-radius:6px;margin-bottom:10px;}
.auth-card p{margin-top:10px;}
.auth-card a{color:white;text-decoration:underline;}
</style>
</head>

<body>

<div class="auth-card">

<h2>Create Student Account</h2>

<?php if(!empty($error)) { ?>
<div class="error-msg"><?= $error; ?></div>
<?php } ?>

<form method="POST">

<input type="text" name="name" placeholder="Enter Full Name" required>

<input type="email" name="email" placeholder="Enter Email" required>

<input type="password" name="password" placeholder="4-8 Characters Password" required>

<!-- Department selection -->
<select name="department_id" required>
<option value="">Select Department</option>
<?php while($row=$departments->fetch_assoc()){ ?>
<option value="<?= $row['id']; ?>">
<?= htmlspecialchars($row['department_name']); ?>
</option>
<?php } ?>
</select>

<button type="submit" name="register">Register</button>

</form>

<p>Already have an account?
<a href="login.php">Login Here</a>
</p>

</div>

</body>
</html>