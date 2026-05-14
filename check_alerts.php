<?php
include "db.php";

$result = $conn->query("
    SELECT COUNT(*) as total 
    FROM emergency_alerts 
    WHERE status='Pending'
");

$row = $result->fetch_assoc();
echo $row['total'];
?>