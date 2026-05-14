<?php
include "db.php";

$message = "";
$success = false;

if(isset($_POST['register'])){

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $role = $_POST['role'];
    $disability = $_POST['disability'];

    $sql = "INSERT INTO users (name,email,password,role,disability)
            VALUES ('$name','$email','$password','$role','$disability')";

    if($conn->query($sql)){
        $message = "Registered Successfully!";
        $success = true;
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Register</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background: linear-gradient(120deg,#1e3c72,#2a5298);
    display:flex;
    justify-content:center;
    align-items:center;
    min-height:100vh;
}

/* Card */
.register-box{
    background:white;
    padding:35px;
    width:400px;
    border-radius:15px;
    box-shadow:0 20px 40px rgba(0,0,0,0.3);
    text-align:center;
}

.register-box h2{
    margin-bottom:20px;
    color:#2a5298;
}

/* Inputs */
input, select{
    width:100%;
    padding:10px;
    margin:8px 0;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

input:focus, select:focus{
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

/* Messages */
.success{
    background:#d4edda;
    color:#155724;
    padding:8px;
    border-radius:8px;
    margin-bottom:10px;
    font-size:14px;
}

.error{
    background:#ffdddd;
    color:red;
    padding:8px;
    border-radius:8px;
    margin-bottom:10px;
    font-size:14px;
}

/* Link */
.login-link{
    margin-top:15px;
    font-size:14px;
}

.login-link a{
    color:#2a5298;
    text-decoration:none;
}

.login-link a:hover{
    text-decoration:underline;
}

</style>
</head>

<body>

<div class="register-box">

<h2>📝 User Registration</h2>

<?php if($message != ""){ ?>
    <div class="<?php echo $success ? 'success' : 'error'; ?>">
        <?php echo $message; ?>
    </div>
<?php } ?>

<form method="POST">

<input type="text" name="name" placeholder="Enter Full Name" required>

<input type="email" name="email" placeholder="Enter Email" required>

<input type="password" name="password" placeholder="Enter Password" required>

<select name="role">
    <option value="person">Person</option>
    <option value="admin">Admin</option>
</select>

<select name="disability">
    <option value="blind">Blind</option>
    <option value="deaf">Deaf</option>
    <option value="speech">Speech Impaired</option>
</select>

<button type="submit" name="register">Register</button>

</form>

<div class="login-link">
Already have an account? <a href="index.php">Login here</a>
</div>

</div>

</body>
</html>