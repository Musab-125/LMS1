<?php
session_start();
include("../config.php");

if(!isset($_GET['quiz_id'])) die("Quiz not found.");
$quiz_id = intval($_GET['quiz_id']);

$msg = "";

if(isset($_POST['add_question'])){
    $question = $_POST['question'];
    $o1 = $_POST['option1'];
    $o2 = $_POST['option2'];
    $o3 = $_POST['option3'];
    $o4 = $_POST['option4'];
    $correct = intval($_POST['correct']);

    $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question, option1, option2, option3, option4, correct_option) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssi", $quiz_id, $question, $o1, $o2, $o3, $o4, $correct);
    if($stmt->execute()){
        $msg = "Question added successfully!";
    } else {
        $msg = "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Add Questions</title>
<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
body{background:#f0f2f5;padding:20px;}

/* Card */
.card{
    background:white;
    max-width:700px;
    margin:20px auto;
    padding:25px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
}

/* Form elements */
.card h3{
    margin-bottom:20px;
    color:#1e40af;
}
.card label{
    display:block;
    margin-top:12px;
    font-weight:bold;
}
.card input[type="text"],
.card textarea,
.card input[type="number"]{
    width:100%;
    padding:10px;
    margin-top:5px;
    border-radius:6px;
    border:1px solid #ccc;
    font-size:14px;
}
.card textarea{
    resize:vertical;
}

/* Button */
.card button{
    margin-top:20px;
    padding:12px 20px;
    background:#2563eb;
    color:white;
    border:none;
    border-radius:6px;
    font-size:16px;
    cursor:pointer;
}
.card button:hover{
    background:#1d4ed8;
}

/* Success/Error Message */
.msg{
    padding:10px;
    background:#d1fae5;
    color:#065f46;
    border-radius:6px;
    margin-bottom:15px;
    text-align:center;
    font-weight:bold;
}
</style>
</head>
<body>

<div class="card">
    <h3>Add Question</h3>

    <?php if($msg){ echo "<div class='msg'>$msg</div>"; } ?>

    <form method="POST">
        <label>Question:</label>
        <textarea name="question" rows="3" placeholder="Type your question here..." required></textarea>

        <label>Option 1:</label>
        <input type="text" name="option1" placeholder="Enter option 1" required>

        <label>Option 2:</label>
        <input type="text" name="option2" placeholder="Enter option 2" required>

        <label>Option 3:</label>
        <input type="text" name="option3" placeholder="Enter option 3" required>

        <label>Option 4:</label>
        <input type="text" name="option4" placeholder="Enter option 4" required>

        <label>Correct Option (1-4):</label>
        <input type="number" name="correct" min="1" max="4" required>

        <button type="submit" name="add_question">Add Question</button>
    </form>
</div>

</body>
</html>