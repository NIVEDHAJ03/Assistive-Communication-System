<?php
session_start();
include "db.php";

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit();
}

$current_user = $_SESSION['user_id'];

/* Insert Message */
if(isset($_POST['send'])){
    $receiver = $_POST['receiver_id'];
    $message = $_POST['message'];

    $sql = "INSERT INTO messages (sender_id, receiver_id, message)
            VALUES ('$current_user', '$receiver', '$message')";
    $conn->query($sql);
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Private Chat</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

body{
    margin:0;
    font-family:'Segoe UI',sans-serif;
    background: linear-gradient(120deg,#1e3c72,#2a5298);
}

/* Main Container */
.container{
    width:90%;
    max-width:900px;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:15px;
    box-shadow:0 20px 40px rgba(0,0,0,0.3);
}

/* Header */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.header h2{
    margin:0;
    color:#2a5298;
}

.back-btn{
    text-decoration:none;
    background:#17a2b8;
    color:white;
    padding:8px 18px;
    border-radius:25px;
}

.back-btn:hover{
    background:#117a8b;
}

/* Form */
form{
    margin-top:20px;
}

select, textarea{
    width:100%;
    padding:10px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
}

textarea{
    height:70px;
    resize:none;
}

/* Buttons */
.btn{
    padding:8px 15px;
    border:none;
    border-radius:20px;
    color:white;
    cursor:pointer;
    font-size:14px;
}

.speak-btn{
    background:#ffc107;
}

.send-btn{
    background:#28a745;
}

.speak-btn:hover{
    background:#e0a800;
}

.send-btn:hover{
    background:#218838;
}

/* Chat Box */
#chatBox{
    margin-top:20px;
    height:300px;
    overflow-y:auto;
    border:1px solid #ddd;
    padding:15px;
    border-radius:10px;
    background:#f8f9fc;
}

/* Chat Messages */
.message{
    padding:8px 12px;
    margin-bottom:10px;
    border-radius:12px;
    max-width:70%;
    font-size:14px;
}

.sent{
    background:#2a5298;
    color:white;
    margin-left:auto;
}

.received{
    background:#e2e6ea;
    color:black;
}

.footer{
    text-align:center;
    margin-top:15px;
    font-size:13px;
    color:#666;
}

</style>
</head>

<body>

<div class="container">

<div class="header">
    <h2>💬 Private Chat</h2>
    <a href="dashboard.php" class="back-btn">Back</a>
</div>

<form method="POST">

<label>Select User:</label>
<select name="receiver_id" id="receiverSelect" required>
<?php
$users = $conn->query("SELECT * FROM users WHERE id != $current_user AND role='person'");
while($row = $users->fetch_assoc()){
    echo "<option value='".$row['id']."'>".$row['name']."</option>";
}
?>
</select>

<br><br>

<label>Message:</label>
<textarea id="messageBox" name="message" required></textarea>
<br><br>

<button type="button" class="btn speak-btn" onclick="startListening()">🎤 Speak</button>
<button type="submit" name="send" class="btn send-btn">Send</button>

</form>

<h3>Chat History</h3>
<div id="chatBox"></div>

<div class="footer">
© 2026 Assistive Communication System
</div>

</div>

<!-- Speech To Text -->
<script>
function startListening() {
    const recognition = new (window.SpeechRecognition || window.webkitSpeechRecognition)();
    recognition.lang = "en-US";
    recognition.start();

    recognition.onresult = function(event) {
        document.getElementById("messageBox").value =
            event.results[0][0].transcript;
    };
}
</script>

<!-- Text To Speech -->
<script>
function speakText(text){
    let speech = new SpeechSynthesisUtterance(text);
    speech.lang = "en-US";
    window.speechSynthesis.speak(speech);
}
</script>

<!-- Auto Load Messages -->
<script>
let lastSpokenMessageId = 0;

function loadMessages(){

    let receiver = document.getElementById("receiverSelect").value;

    fetch("fetch_messages.php?receiver_id=" + receiver)
    .then(response => response.text())
    .then(data => {

        document.getElementById("chatBox").innerHTML = data;

        let messages = document.querySelectorAll("#chatBox p");

        messages.forEach(function(msg){

            let msgId = parseInt(msg.getAttribute("data-id"));
            let senderId = parseInt(msg.getAttribute("data-sender"));

            if(msgId > lastSpokenMessageId && senderId != <?php echo $current_user; ?>){
                speakText(msg.innerText);
                lastSpokenMessageId = msgId;
            }
        });

        let chatBox = document.getElementById("chatBox");
        chatBox.scrollTop = chatBox.scrollHeight;
    });
}

setInterval(loadMessages, 2000);
loadMessages();
</script>

</body>
</html>