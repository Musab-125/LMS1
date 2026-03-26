<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LMS Home</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        /* Reset & font */
        *{margin:0;padding:0;box-sizing:border-box;font-family:'Segoe UI',sans-serif;}
        body{height:100vh;background:linear-gradient(135deg,#4e73df,#1cc88a);display:flex;justify-content:center;align-items:center;color:#fff;}
        
        .container{
            background:rgba(255,255,255,0.1);
            padding:40px 50px;
            border-radius:15px;
            text-align:center;
            box-shadow:0 8px 30px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            width:90%;
            max-width:500px;
        }

        h1{
            font-size:2.5rem;
            margin-bottom:10px;
        }

        p{
            font-size:1.1rem;
            margin-bottom:30px;
        }

        .btn{
            display:inline-block;
            padding:12px 30px;
            font-size:1rem;
            border:none;
            border-radius:8px;
            background:#fff;
            color:#4e73df;
            font-weight:bold;
            cursor:pointer;
            transition:0.3s;
            text-decoration:none;
        }

        .btn:hover{
            background:#f0f0f0;
            transform:translateY(-2px);
        }

        .icon{
            font-size:4rem;
            margin-bottom:20px;
            color:#fff;
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%,100%{transform:translateY(0);}
            50%{transform:translateY(-15px);}
        }

        @media(max-width:480px){
            h1{font-size:2rem;}
            p{font-size:1rem;}
        }
    </style>
</head>
<body>

<div class="container">
    <div class="icon"><i class="fas fa-graduation-cap"></i></div>
    <h1>Welcome to LMS</h1>
    <p>Your Learning Management System is ready. Click below to login and manage your courses.</p>
    <a href="login.php" class="btn">Go to Login</a>
</div>

</body>
</html>