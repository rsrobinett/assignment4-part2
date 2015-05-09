<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$dbCredentials = "dbCredentials.php";
include($dbCredentials);

global $mysqli;

function createDBConnection($dbhost, $dbuser, $dbpass, $dbname){
    global $mysqli;
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    // Check connection
    if (!$mysqli || $mysqli->connect_error) {
        echo "Connection error " .$mysqli->connect_error. " " .$mysqli->connect_error;
    } 
}

function addToInventory(){
    global $mysqli;
    global $dbname;
    // $stmt = $mysqli->prepare("INSERT INTO $dbname.inventory SET name = $_POST[name], category=$_POST[category], length = $_POST[length]");
     $stmt = $mysqli->prepare("INSERT INTO $dbname.inventory (name, category, length, rented) VALUES (:name, :category, :length, :rented))");
     
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':value', $value);
  $stmt->bindParam(':name', $name);
    $stmt->bindParam(':value', $value);

     $stmt->execute();
    
    if(isset($_POST["addvideo"])){
        echo "<div>Adding Video<div>";
        echo "<label>Name: </label> <div>$_POST[name]</div>";
        echo "<label>Category: </label> <div>$_POST[category]</div>";
        echo "<label>Length: <label> </label> <div>$_POST[length]</div>";
    }
}

function getInventory(){
    $getInventory = $mysqli->prepare("SELECT name, category, length, rented FROM $dbname.inventory");
}

function getFilteredInventory(){
    $getFilteredInventory = $mysqli->prepare("SELECT name, category, length, rented FROM $dbname.inventory WHERE category = ?");
}

createDBConnection($dbhost, $dbuser, $dbpass, $dbname);
addToInventory();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Anachronistic Video Rental Example</title>
</head>
<body>
<h1>
Anachronistic Video Rental Example
</h1>
<div>
<form action="inventory.php" method="post">
    <input type="submit" value="Add" name="addvideo">
    <label>Name: </label> <input type="text" name="name" required>
    <label>Category: </label> <input type="text" name="category">
    <label>Length: <label> </label> <input type="number" name="length">
</form>
</div>


</body>
</html>