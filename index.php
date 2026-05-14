<?php
session_start();
include "db.php";

$error = "";

if(isset($_POST['login'])){

    $email = $_POST['email'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE email='$email' AND password='$password'";
    $result = $conn->query($sql);

    if($result->num_rows > 0){
        $row = $result->fetch_assoc();

        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role'] = $row['role'];
        $_SESSION['name'] = $row['name'];

        if($row['role'] == "admin"){
            header("Location: admin.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Login</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background: linear-gradient(120deg,#1e3c72,#2a5298);
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

/* Login Card */
.login-box{
    background:white;
    padding:35px;
    width:350px;
    border-radius:15px;
    box-shadow:0 20px 40px rgba(0,0,0,0.3);
    text-align:center;
}

.login-box h2{
    margin-bottom:20px;
    color:#2a5298;
}

/* Input Fields */
input{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

input:focus{
    border-color:#2a5298;
    outline:none;
}

/* Button */
button{
    width:100%;
    padding:10px;
    background:#2a5298;
    color:white;
    border:none;
    border-radius:25px;
    font-size:15px;
    cursor:pointer;
    margin-top:10px;
}

button:hover{
    background:#1e3c72;
}

/* Error Message */
.error{
    background:#ffdddd;
    color:red;
    padding:8px;
    border-radius:8px;
    margin-bottom:10px;
    font-size:14px;
}

/* Footer */
.footer{
    margin-top:15px;
    font-size:13px;
    color:#666;
}

</style>
</head>

<body>

<div class="login-box">

<h2>🔐 Assistive System Login</h2>

<?php if($error != ""){ ?>
    <div class="error"><?php echo $error; ?></div>
<?php } ?>

<form method="POST">

<input type="email" name="email" placeholder="Enter Email" required>

<input type="password" name="password" placeholder="Enter Password" required>

<button type="submit" name="login">Login</button>

</form>

<div class="footer">
© 2026 Assistive Emergency Support System
</div>

</div>

</body>
</html>