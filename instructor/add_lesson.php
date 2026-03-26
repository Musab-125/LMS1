<?php
session_start();

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'instructor'){
    header("Location: ../login.php");
    exit();
}

include("../config.php");

$instructor_id = $_SESSION['user_id'];

// Get approved modules
$modules = mysqli_query($conn, "
    SELECT m.id, m.module_name
    FROM modules m
    JOIN instructor_modules im ON m.id = im.module_id
    WHERE im.instructor_id='$instructor_id' AND im.status='approved'
");

// Add lesson
$msg = "";
if(isset($_POST['submit'])){

    $module_id = $_POST['module_id'];
    $title = $_POST['title'];
    $video_url = $_POST['video_url'];

    $video_file = $_FILES['video_file']['name'];
    $pdf_file = $_FILES['pdf_file']['name'];

    // Upload files
    if(!empty($video_file)){
        move_uploaded_file($_FILES['video_file']['tmp_name'], "../uploads/videos/".$video_file);
    }
    if(!empty($pdf_file)){
        move_uploaded_file($_FILES['pdf_file']['tmp_name'], "../uploads/pdfs/".$pdf_file);
    }

    $sql = "INSERT INTO lessons (module_id, instructor_id, title, video_url, video_file, file_path)
            VALUES ('$module_id','$instructor_id','$title','$video_url','$video_file','$pdf_file')";

    if(mysqli_query($conn, $sql)){
        $msg = "Lesson Added Successfully!";
    } else {
        $msg = "Error: ".mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Lesson</title>

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

/* Form Card */
.card{
    background:white;
    padding:25px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.1);
    max-width:600px;
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
    <h3>Add Lesson</h3>
    <span>Instructor Panel</span>
</div>

<div class="card">

<?php if($msg){ echo "<div class='msg'>$msg</div>"; } ?>

<form method="post" enctype="multipart/form-data">

<label>Select Module</label>
<select name="module_id" required>
    <option value="">Select Module</option>
    <?php while($row = mysqli_fetch_assoc($modules)) { ?>
        <option value="<?= $row['id']; ?>">
            <?= htmlspecialchars($row['module_name']); ?>
        </option>
    <?php } ?>
</select>

<label>Lesson Title</label>
<input type="text" name="title" placeholder="Enter lesson title" required>

<label>YouTube Link</label>
<input type="text" name="video_url" placeholder="https://youtube.com/...">

<label>Upload Video File</label>
<input type="file" name="video_file">

<label>Upload PDF File</label>
<input type="file" name="pdf_file">

<button type="submit" name="submit">Add Lesson</button>

</form>

</div>

</div>

</body>
</html>