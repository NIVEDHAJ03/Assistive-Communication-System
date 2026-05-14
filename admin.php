<?php 
session_start(); 
include "db.php";  

if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: index.php");
    exit();
}

/* =============================
   STATISTICS CALCULATION
============================= */

$totalAlerts = $conn->query("SELECT COUNT(*) as total FROM emergency_alerts")
                    ->fetch_assoc()['total'];

$pendingAlerts = $conn->query("SELECT COUNT(*) as total FROM emergency_alerts 
                               WHERE LOWER(status)='pending'")
                      ->fetch_assoc()['total'];

$resolvedAlerts = $conn->query("SELECT COUNT(*) as total FROM emergency_alerts 
                                WHERE LOWER(status)='resolved'")
                       ->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background: linear-gradient(120deg,#1e3c72,#2a5298);
}

.container{
    width:90%;
    max-width:1200px;
    margin:30px auto;
    background:white;
    padding:25px;
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

.logout:hover{
    background:#e60023;
}

/* =============================
   STATISTICS CARDS
============================= */

.stats{
    display:flex;
    gap:20px;
    margin-top:20px;
    flex-wrap:wrap;
}

.card{
    flex:1;
    min-width:250px;
    padding:20px;
    border-radius:12px;
    color:white;
    text-align:center;
    font-size:18px;
    font-weight:bold;
}

.card h1{
    margin:10px 0;
    font-size:40px;
}

.total{ background:#007bff; }
.pending{ background:#dc3545; }
.resolved{ background:#28a745; }

/* =============================
   TABLE DESIGN
============================= */

.section-title{
    margin-top:30px;
    color:#1e3c72;
}

table{
    width:100%;
    border-collapse:collapse;
    margin-top:10px;
}

table th{
    background:#2a5298;
    color:white;
    padding:12px;
}

table td{
    padding:10px;
    text-align:center;
    border-bottom:1px solid #ddd;
}

table tr:hover{
    background:#f2f2f2;
}

.blink{
    animation: blinkRed 1s infinite;
}

@keyframes blinkRed{
    0%{ background:#ffe6e6; }
    50%{ background:#ff4d4d; color:white; }
    100%{ background:#ffe6e6; }
}

.btn{
    padding:6px 12px;
    border-radius:6px;
    text-decoration:none;
    color:white;
    font-size:14px;
}

.resolve-btn{ background:#28a745; }
.map-btn{ background:#17a2b8; }

.footer{
    text-align:center;
    margin-top:20px;
    font-size:14px;
    color:#555;
}
</style>
</head>

<body>

<div class="container">

<div class="header">
    <h2>Welcome Admin: <?php echo htmlspecialchars($_SESSION['name']); ?></h2>
    <a href="logout.php" class="logout">Logout</a>
</div>

<hr>

<!-- =============================
     STATISTICS CARDS
============================= -->

<div class="stats">
    <div class="card total">
        📊 Total Alerts
        <h1><?php echo $totalAlerts; ?></h1>
    </div>

    <div class="card pending">
        🚨 Pending Alerts
        <h1><?php echo $pendingAlerts; ?></h1>
    </div>

    <div class="card resolved">
        ✔ Resolved Alerts
        <h1><?php echo $resolvedAlerts; ?></h1>
    </div>
</div>

<hr>

<!-- USERS SECTION -->
<h3 class="section-title">👥 All Registered Users</h3>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Disability</th>
</tr>

<?php 
$result = $conn->query("SELECT * FROM users");
while($row = $result->fetch_assoc()){
    echo "<tr>
            <td>".htmlspecialchars($row['id'])."</td>
            <td>".htmlspecialchars($row['name'])."</td>
            <td>".htmlspecialchars($row['email'])."</td>
            <td>".htmlspecialchars($row['role'])."</td>
            <td>".htmlspecialchars($row['disability'])."</td>
          </tr>";
}
?>
</table>

<hr>

<!-- EMERGENCY SECTION -->
<h3 class="section-title">🚨 Emergency Alerts</h3>

<table>
<tr>
    <th>User ID</th>
    <th>Type</th>
    <th>Status</th>
    <th>Time</th>
    <th>Location</th>
    <th>Action</th>
</tr>

<?php 
$result = $conn->query("SELECT * FROM emergency_alerts ORDER BY created_at DESC");

while($row = $result->fetch_assoc()){

    $rowClass = (strtolower($row['status']) == 'pending') ? "class='blink'" : "";

    echo "<tr $rowClass>
            <td>".htmlspecialchars($row['user_id'])."</td>
            <td>".htmlspecialchars($row['type'])."</td>
            <td>".htmlspecialchars($row['status'])."</td>
            <td>".htmlspecialchars($row['created_at'])."</td>";

    $latitude = trim($row['latitude'] ?? '');
    $longitude = trim($row['longitude'] ?? '');

    echo "<td>";

    if($latitude !== '' && $longitude !== ''){
        echo "<a class='btn map-btn' target='_blank'
              href='https://www.google.com/maps?q={$latitude},{$longitude}'>
              View Map
              </a>";
    } else {
        echo "<span style='color:red;'>Location Not Available</span>";
    }

    echo "</td>";

    echo "<td>";

    if(strtolower($row['status']) == 'pending'){
        echo "<a class='btn resolve-btn' href='resolve.php?id=".$row['id']."'>✔ Resolve</a>";
    } else {
        echo "Resolved";
    }

    echo "</td></tr>";
}
?>
</table>

<audio id="alarmSound">
    <source src="https://www.soundjay.com/button/beep-07.wav" type="audio/wav">
</audio>

<div class="footer">
    © 2026 Emergency Assistance System | Admin Panel
</div>

</div>

<script>

let lastAlertCount = <?php echo $totalAlerts; ?>;

function checkAlerts(){
    fetch("check_alerts.php")
    .then(response => response.text())
    .then(data => {
        if(parseInt(data) > lastAlertCount){
            document.getElementById("alarmSound").play();
        }
        lastAlertCount = parseInt(data);
    });
}

setInterval(function(){
    location.reload();
}, 5000);

setInterval(checkAlerts, 5000);

</script>

</body>
</html>