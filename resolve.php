<?php
include "db.php";

$id = $_GET['id'];

$conn->query("
UPDATE emergency_alerts 
SET status='Resolved' 
WHERE id='$id'
");

header("Location: admin.php");
exit();
?>