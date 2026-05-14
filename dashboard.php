<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'person'){
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Person Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background: linear-gradient(120deg,#1e3c72,#2a5298);
}

.container{
    width:90%;
    max-width:900px;
    margin:40px auto;
    background:white;
    padding:30px;
    border-radius:15px;
    box-shadow:0 20px 40px rgba(0,0,0,0.3);
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.logout{
    background:#ff4b5c;
    color:white;
    padding:8px 18px;
    border-radius:25px;
    text-decoration:none;
}

.section-title{
    margin-top:30px;
    margin-bottom:15px;
    color:#1e3c72;
}

.emergency-buttons button{
    padding:12px 18px;
    margin:8px;
    border:none;
    border-radius:25px;
    cursor:pointer;
    font-size:14px;
    color:white;
}

.water{ background:#17a2b8; }
.food{ background:#28a745; }
.medicine{ background:#6f42c1; }
.help{ background:#fd7e14; }

.chat-btn{
    display:inline-block;
    background:#2a5298;
    color:white;
    padding:10px 20px;
    border-radius:25px;
    text-decoration:none;
}

.sos-section{
    text-align:center;
    margin-top:30px;
}

.sos-btn{
    background:red;
    color:white;
    padding:20px 40px;
    font-size:20px;
    border:none;
    border-radius:50px;
    cursor:pointer;
    animation:pulse 1.5s infinite;
}

@keyframes pulse{
    0%{ box-shadow:0 0 10px red; }
    50%{ box-shadow:0 0 30px red; }
    100%{ box-shadow:0 0 10px red; }
}

.footer{
    text-align:center;
    margin-top:25px;
    font-size:13px;
    color:#666;
}
</style>
</head>

<body>

<div class="container">

<div class="header">
    <h2>Welcome <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
    <a href="logout.php" class="logout">Logout</a>
</div>

<hr>

<!-- Emergency Panel -->
<h3 class="section-title">🚨 Emergency Panel</h3>

<form action="send_emergency.php" method="POST" class="emergency-buttons" id="emergencyForm">
    
    <!-- Hidden location fields -->
    <input type="hidden" name="latitude" id="lat">
    <input type="hidden" name="longitude" id="lon">

    <button class="water" name="type" value="Need Water">💧 Need Water</button>
    <button class="food" name="type" value="Need Food">🍽 Need Food</button>
    <button class="medicine" name="type" value="Need Medicine">💊 Need Medicine</button>
    <button class="help" name="type" value="Call Help">📞 Call Help</button>
</form>

<hr>

<h3 class="section-title">💬 Communication</h3>
<a href="chat.php" class="chat-btn">Go to Chat</a>

<hr>

<!-- SOS Button -->
<div class="sos-section">
    <h3 style="color:red;">🆘 Emergency SOS</h3>

    <button onclick="triggerEmergency()" class="sos-btn">
        EMERGENCY HELP
    </button>
</div>

<div class="footer">
© 2026 Assistive Emergency Support System
</div>

</div>

<script>

/* Get Location Automatically */
if(navigator.geolocation){
    navigator.geolocation.getCurrentPosition(function(position){
        document.getElementById("lat").value = position.coords.latitude;
        document.getElementById("lon").value = position.coords.longitude;
    }, function(){
        alert("Please allow location access.");
    });
}

/* SOS Button */
function triggerEmergency(){

    let speech = new SpeechSynthesisUtterance(
        "Emergency alert activated. Help request has been sent."
    );
    speech.lang = "en-US";
    window.speechSynthesis.speak(speech);

    if(navigator.geolocation){

        navigator.geolocation.getCurrentPosition(function(position){

            let form = document.createElement("form");
            form.method = "POST";
            form.action = "send_emergency.php";

            let latInput = document.createElement("input");
            latInput.type = "hidden";
            latInput.name = "latitude";
            latInput.value = position.coords.latitude;

            let lonInput = document.createElement("input");
            lonInput.type = "hidden";
            lonInput.name = "longitude";
            lonInput.value = position.coords.longitude;

            let typeInput = document.createElement("input");
            typeInput.type = "hidden";
            typeInput.name = "type";
            typeInput.value = "SOS Emergency";

            form.appendChild(latInput);
            form.appendChild(lonInput);
            form.appendChild(typeInput);

            document.body.appendChild(form);
            form.submit();

        }, function(){
            alert("Location access required.");
        });
    }
}
</script>

</body>
</html>