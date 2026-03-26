<?php
session_start();
include("../config.php"); // Make sure this path is correct

// Check if student is logged in
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student'){
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Get student info
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Check module_id
if(!isset($_GET['module_id'])){
    header("Location: modules.php");
    exit();
}

$module_id = intval($_GET['module_id']);
$dept_id = intval($user['department_id']);

// Check module belongs to student's department
$module_check = $conn->query("
    SELECT * FROM modules 
    WHERE id='$module_id' AND department_id='$dept_id'
");

if($module_check->num_rows == 0){
    echo "<h3 style='color:red;'>⚠ This module is not assigned to your department</h3>";
    echo "<a href='modules.php'>⬅ Back to Modules</a>";
    exit();
}

$module = $module_check->fetch_assoc();

// Get lessons for this module
$lessons = $conn->query("SELECT * FROM lessons WHERE module_id='$module_id'");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($module['module_name']) ?> - Lessons</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{display:flex;min-height:100vh;background:#f4f6f9;}

.sidebar{
    width:220px;background:#1e293b;color:white;padding-top:20px;
}
.sidebar h2{text-align:center;margin-bottom:20px;}
.sidebar a{
    display:block;padding:15px;color:white;text-decoration:none;
}
.sidebar a:hover{background:#334155;}

.main{flex:1;padding:20px;}

.topbar{
    background:white;padding:15px;border-radius:10px;
    margin-bottom:20px;display:flex;justify-content:space-between;
}

.lesson-card{
    background:white;padding:20px;margin-bottom:20px;
    border-radius:10px;
    box-shadow:0 2px 8px rgba(0,0,0,0.1);
}

.lesson-card h3{margin-bottom:10px;}
.lesson-card iframe, .lesson-card video{width:100%;max-width:700px;margin-bottom:10px;}
.lesson-card a{display:inline-block;text-decoration:none;background:#4e73df;color:white;padding:6px 10px;border-radius:5px;transition:0.3s;margin-right:5px;}
.lesson-card a:hover{background:#2e59d9;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>🎓 LMS</h2>
    <a href="dashboard.php">Dashboard</a>
    <a href="modules.php">Modules</a>
    <a href="doubts.php">Doubts</a>
    <a href="quizzes.php">Quizzes</a>
    <a href="results.php">Results</a>
    <a href="../logout.php">Logout</a>
</div>

<!-- Main content -->
<div class="main">
    <div class="topbar">
        <h3><?= htmlspecialchars($module['module_name']) ?> - Lessons</h3>
        <span><?= htmlspecialchars($user['name']) ?> 👋</span>
    </div>

    <a href="modules.php">⬅ Back to Modules</a>
    <br><br>

    <?php
    if($lessons->num_rows > 0){
        while($lesson = $lessons->fetch_assoc()){
            echo '<div class="lesson-card">';
            echo '<h3>'.htmlspecialchars($lesson['title']).'</h3>';

            // YouTube video support (standard + short links)
            if(!empty($lesson['video_url'])){
                $url = $lesson['video_url'];
                $url = explode('?', $url)[0]; // remove extra parameters

                if(strpos($url, 'youtu.be') !== false){
                    $parts = explode('/', $url);
                    $video_id = end($parts);
                    $embed = "https://www.youtube.com/embed/".$video_id;
                } elseif(strpos($url, 'youtube.com/watch?v=') !== false){
                    $embed = str_replace("watch?v=", "embed/", $url);
                } else {
                    $embed = $url; // fallback
                }

                echo '<iframe height="360" src="'.$embed.'" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
            }

            // Uploaded video
            if(!empty($lesson['video_file'])){
                echo '<video controls>
                        <source src="../uploads/videos/'.htmlspecialchars($lesson['video_file']).'" type="video/mp4">
                      Your browser does not support HTML5 video.
                      </video>';
            }

            // PDF file
            if(!empty($lesson['file_path'])){
                echo '<br><a href="../uploads/pdfs/'.htmlspecialchars($lesson['file_path']).'" target="_blank">📄 View PDF</a>';
            }

            echo '</div>';
        }
    } else {
        echo "<p>No lessons uploaded for this module yet.</p>";
    }
    ?>
</div>

</body>
</html>