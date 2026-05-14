<?php
include "db.php";
session_start();

if(!isset($_SESSION['user_id'])){
    exit();
}

$sender = $_SESSION['user_id'];
$receiver = intval($_POST['receiver_id']);
$message = $conn->real_escape_string($_POST['message']);

$conn->query("
INSERT INTO messages (sender_id, receiver_id, message)
VALUES ('$sender', '$receiver', '$message')
");
?>