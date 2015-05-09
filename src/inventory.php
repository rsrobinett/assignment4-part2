<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$dbCredentials = "dbCredentials.php";
include($dbCredentials);

global $mysqli;
global $inventory;
global $categoryfilter;

function createDBConnection($dbhost, $dbuser, $dbpass, $dbname){
    global $mysqli;
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    // Check connection
    if (!$mysqli || $mysqli->connect_error) {
        echo "Connection error " .$mysqli->connect_error. " " .$mysqli->connect_error;
    } 
}

function deleteIdFromInventory($id){
    global $mysqli;
    global $dbname;
     
    if(!($stmt  = $mysqli->prepare("DELETE FROM $dbname.inventory where id = ?"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
    
    if (!$stmt->bind_param("i", $id )) {
        echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
    }  
    
    if (!$stmt->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
}

function deleteAllInventory(){
    global $mysqli;
    global $dbname;
    
    if(!($stmt  = $mysqli->prepare("DELETE FROM $dbname.inventory"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    if (!$stmt->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
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

    unset($stmt);
}

function getInventory(){
    global $inventory;
    global $mysqli;
    global $dbname;
    
    if(isset($_POST['filtercategory']) && $_POST['filtercategory'] !== 'other'){
    //case when filter is set and it is not set to other
        $filterValue = $_POST['filtercategory'];

        if(!($inventory  = $mysqli->prepare("SELECT id, name, category, length, rented FROM $dbname.inventory where category = ? ORDER BY name"))){
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
        
        if (!$inventory->bind_param("s", $filterValue )) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }  
        
        
    } else {
    //case when no filter is set or filter is set to other    
        if(!($inventory  = $mysqli->prepare("SELECT id, name, category, length, rented FROM $dbname.inventory ORDER BY name"))){
            echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
        }
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
    echo "<tr><th><th>name<th>category<th>length<th>status</tr>";
    
    while ($inventory->fetch()) {
        $strRented = transformRentBool($rented);
        echo "<tr><td><form  action='inventory.php' method='post'><input type='submit' value='Delete' name='deletebyid'/><input type='hidden' name='id' value='$id' required></form><td>$name<td>$category<td>$length<td>$strRented</tr>";
    }
    
    echo "</tbody></table>";
}

function transformRentBool($rented){
    if(!$rented){
        return "available";
    }
    return "checked out";
}

createDBConnection($dbhost, $dbuser, $dbpass, $dbname);
if(isset($_POST["addvideo"])){
    addToInventory();
}

function getCategorySelectOptions(){
    global $mysqli;
    global $dbname;
    global $categoryfilter;
    
    if(!($categoryfilterQuery  = $mysqli->prepare("SELECT DISTINCT category FROM $dbname.inventory order by category"))){
        echo "Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }

    if (!$categoryfilterQuery->execute()) {
        echo "Execute failed: (" . $mysqli->errno . ") " . $mysqli->error;
    } 
    
    $category = NULL;
    
    if (!$categoryfilterQuery->bind_result($category )) {
        echo "Binding results failed: (" . $categoryfilterQuery->errno . ") " . $categoryfilterQuery->error;
    } 
    
    while ($categoryfilterQuery->fetch()) {
        $categoryfilter[] = $category;
    }
}

function printCategories($categoryfilter){
    foreach($categoryfilter as $category){
        echo "<option value='$category'>$category</option>";
    }
}

if(isset($_POST["deleteall"])){
    deleteAllInventory();
}
if(isset($_POST["deletebyid"])){
    deleteIdFromInventory($_POST["id"]);
}

getCategorySelectOptions();
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
    <label for="name">Name: </label> <input type="text" name="name" required>
    <label for="category">Category: </label> <input type="text" name="category">
    <label for="length">Length: <label> </label> <input type="number" name="length">
</form>
</div>

<div>
<form action="inventory.php" method="post">
    <label>Category Filter: </label>
       <select name="filtercategory">
          <option value="other" selected>all movies</option>
          <?php printCategories($categoryfilter); ?>
      </select>
    <input type="submit" value="Filter">
</form>
</div>
<div>
    <?php createInventoryTable(); ?>
</div>
<form action="inventory.php" method="post" >
    <input type="submit" value="Delete All" name="deleteall"/>
</form>
</body>
</html>