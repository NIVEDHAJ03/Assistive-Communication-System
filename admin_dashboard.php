<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Emergency Dashboard</title>
<link rel="stylesheet" href="css/style.css">
<style>

/* Background */
body {
    font-family: 'Segoe UI', sans-serif;
    background: linear-gradient(135deg, #4e73df, #1cc88a);
    margin: 0;
    padding: 30px;
}

/* Main Card */
.dashboard {
    background: white;
    padding: 30px;
    border-radius: 15px;
    max-width: 900px;
    margin: auto;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2);
}

/* Heading */
h2 {
    text-align: center;
    color: #4e73df;
    margin-bottom: 20px;
}

/* Logout Link */
a {
    display: inline-block;
    margin-bottom: 20px;
    text-decoration: none;
    background: #dc3545;
    color: white;
    padding: 8px 15px;
    border-radius: 8px;
    transition: 0.3s;
}

a:hover {
    background: #c82333;
}

/* Alert Cards */
.card {
    padding: 18px;
    margin: 15px 0;
    border-radius: 12px;
    color: white;
    font-weight: 500;
    box-shadow: 0 8px 20px rgba(0,0,0,0.2);
    transition: 0.3s;
}

.card:hover {
    transform: translateY(-3px);
}

/* High Priority */
.high {
    background: linear-gradient(45deg, #ff416c, #ff4b2b);
    animation: pulse 1.5s infinite;
}

/* Normal Priority */
.normal {
    background: linear-gradient(45deg, #1cc88a, #36b9cc);
}

/* Pulse Animation */
@keyframes pulse {
    0% { box-shadow: 0 0 5px #ff4b2b; }
    50% { box-shadow: 0 0 25px #ff4b2b; }
    100% { box-shadow: 0 0 5px #ff4b2b; }
}

/* Loading Text */
#alertContainer {
    margin-top: 20px;
    font-size: 15px;
}

</style>
</head>
<body>

<h2>🚨 Admin Emergency Dashboard</h2>
<a href="logout.php">Logout</a>

<div id="alertContainer">
    Loading alerts...
</div>

<audio id="alertSound">
  <source src="https://www.soundjay.com/buttons/beep-01a.mp3" type="audio/mpeg">
</audio>

<script>
function loadAlerts(){

    fetch("fetch_emergencies.php")
    .then(response => response.text())
    .then(data => {

        document.getElementById("alertContainer").innerHTML = data;

        if(data.includes("High Priority")){
            document.getElementById("alertSound").play();
        }

    });

}

// Load immediately
loadAlerts();

// Reload every 5 seconds
setInterval(loadAlerts, 5000);

</script>

</body>
</html>