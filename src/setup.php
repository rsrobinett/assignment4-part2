<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Setup</title>
</head>
<body>
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$dbCredentials = "dbCredentials.php";
include($dbCredentials);

// Create connection
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
// Check connection
if (!$mysqli || $mysqli->connect_error) {
    echo "Connection error " .$mysqli->connect_error. " " .$mysqli->connect_error;
} 
else {
    echo "<div> Connected to host: $dbhost </div>";
    echo "<div> Connected to database: $dbname </div>"; 
}
?>
</body>
</html>