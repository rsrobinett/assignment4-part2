<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$dbCredentials = "dbCredentials.php";
include($dbCredentials);

global $mysqli;
global $inventory;
global $filteredInventory;

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

    if (!$mysqli || $mysqli->connect_error) {
            echo "Connection error " .$mysqli->connect_error. " " .$mysqli->connect_error;
    } 
    
    
    if(!($stmt = $mysqli->prepare("INSERT INTO $dbname.inventory (name, category, length, rented) VALUES (?, ?, ?, ?)"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    $name = $_POST['name'];
    $category = $_POST['category'];
    $length =  $_POST['length'];
    $rented = 1;

    if (!$stmt->bind_param("ssii", $name, $category, $length, $rented )) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }   
    
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
    }

/*
    if(isset($_POST["addvideo"])){
        echo "<div>Adding Video<div>";
        echo "<label>Name: </label> <div>$_POST[name]</div>";
        echo "<label>Category: </label> <div>$_POST[category]</div>";
        echo "<label>Length: <label> </label> <div>$_POST[length]</div>";
    }
    */
    
    unset($stmt);
}

function getInventory(){
    global $inventory;
    global $mysqli;
    global $dbname;
    
    if(!($inventory  = $mysqli->prepare("SELECT id, name, category, length, rented FROM $dbname.inventory"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    
    if (!$inventory->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
}
 
function createInventoryTable(){
    
    global $inventory;
    
    $id = NULL;
    $name = NULL;
    $category = NULL;
    $length =  NULL;
    $rented = NULL;
    
    if (!$inventory->bind_result($id, $name, $category, $length, $rented )) {
        echo "Binding results failed: (" . $stmt->errno . ") " . $stmt->error;
    }  
    
    echo "<table><tbody>";
    echo "<tr><th>name<th>category<th>length<th>rented</tr>";
    
    while ($inventory->fetch()) {
        echo "<tr id='$id'><td>$name<td>$category<td>$length<td>$rented</tr>";
    }
    
    echo "</tbody></table>";
}

createDBConnection($dbhost, $dbuser, $dbpass, $dbname);
if(isset($_POST["addvideo"])){
    addToInventory();
}
getInventory();

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
<div>
    <?php createInventoryTable(); ?>
</div>
</body>
</html>