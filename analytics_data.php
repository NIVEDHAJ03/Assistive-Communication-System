<?php
include "db.php";

$result = $conn->query("
SELECT DATE(created_at) as date, COUNT(*) as total
FROM emergency_alerts
GROUP BY DATE(created_at)
");

$labels = [];
$values = [];

while($row = $result->fetch_assoc()){
    $labels[] = $row['date'];
    $values[] = $row['total'];
}

echo json_encode([
    "labels"=>$labels,
    "values"=>$values
]);
?>