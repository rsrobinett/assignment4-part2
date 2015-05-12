<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="style.css">
    <title>Video Rental</title>
</head>
<body>
    <header>
        <h1>Video Rental</h1>
    </header>
<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$dbCredentials = "dbCredentials.php";
include($dbCredentials);

function createDBConnection($dbhost, $dbuser, $dbpass, $dbname){
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    return $mysqli;
    // Check connection
    if (!$mysqli || $mysqli->connect_error) {
        echo "<div class='error'>Connection error " .$mysqli->connect_error. " " .$mysqli->connect_error. "</div>";
    } 
}

function changeStatus($mysqli, $db, $id, $currentstatus){

    $newstatus = !$currentstatus;
     
    if(!($stmt  = $mysqli->prepare("UPDATE $db.inventory SET rented = ? where id = ?"))){
        echo "<div class='error'>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }
    
    if (!$stmt->bind_param("ii", $newstatus, $id )) {
        echo "<div class='error'>Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error. "</div>";
    }  
    
    if (!$stmt->execute()) {
        echo "<div class='error'>Execute failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }
    unset($stmt);
}

function deleteIdFromInventory($mysqli, $db, $id){

    if(!($stmt  = $mysqli->prepare("DELETE FROM $db.inventory where id = ?"))){
        echo "<div class='error'>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }
    
    if (!$stmt->bind_param("i", $id )) {
        echo "<div class='error'>Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error. "</div>";
    }  
    
    if (!$stmt->execute()) {
        echo "<div class='error'>Execute failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }
    unset($stmt);
}

function deleteAllInventory($mysqli, $db){

    if(!($stmt  = $mysqli->prepare("DELETE FROM $db.inventory"))){
        echo "<div class='error'>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }

    if (!$stmt->execute()) {
        echo "<div class='error'>Execute failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }
    unset($stmt);
}
    
function addToInventory($mysqli, $db){

    if (!$mysqli || $mysqli->connect_error) {
            echo "<div class='error'>Connection error " .$mysqli->connect_error. " " .$mysqli->connect_error. "</div>";
    } 
    
    
    if(!($stmt = $mysqli->prepare("INSERT INTO $db.inventory (name, category, length, rented) VALUES (?, ?, ?, ?)"))){
        echo "<div class='error'>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }

    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    if(trim($_POST['length'])==0||trim($_POST['length'])==null){$length = null;}else{$length = trim($_POST['length']);}
    $rented = 0;

    if (!$stmt->bind_param("ssii", $name, $category, $length, $rented )) {
        echo "<div class='error'>Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error. "</div>";
    }   
    
    if (!$stmt->execute()) {
        echo "<div class='error'>Execute failed: (" . $stmt->errno . ") " . $stmt->error. "</div>";
    }

    unset($stmt);
}

function getInventory($mysqli, $db){
    $inventory;
    if(isset($_POST['filtercategory']) && $_POST['filtercategory'] !== 'other'){
    //case when filter is set and it is not set to other
        $filterValue = $_POST['filtercategory'];

        if(!($inventory  = $mysqli->prepare("SELECT id, name, category, length, rented FROM $db.inventory where category = ? ORDER BY name"))){
            echo "<div class='error'>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
        }
        
        if (!$inventory->bind_param("s", $filterValue )) {
            echo "<div class='error'>Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error. "</div>";
        }  
        
    } else {
    //case when no filter is set or filter is set to other    
        if(!($inventory  = $mysqli->prepare("SELECT id, name, category, length, rented FROM $db.inventory ORDER BY name"))){
            echo "<div class='error'>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
        }
    }
    
    if (!$inventory->execute()) {
        echo "<div class='error'>Execute failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    } else {
        return $inventory;
    }
}
 
function createInventoryTable($inventory){
    $id = NULL;
    $name = NULL;
    $category = NULL;
    $length =  NULL;
    $rented = NULL;
    
    if (!$inventory->bind_result($id, $name, $category, $length, $rented )) {
        echo "<div class='error'>Binding results failed: (" . $stmt->errno . ") " . $stmt->error. "</div>";
    }  
    
    echo "<table>";
    echo "<thead><tr><td><td>name<td>category<td>length<td>status</td></thead>";
    echo "<tbody>";
    while ($inventory->fetch()) {
        $strRented = transformRentBool($rented);
        $checkinout =transformCheckoutType($rented);
        echo "<tr>";
        echo "<td><form class='mini-form' action='inventory.php' method='post'><input class='btn btn--delete' type='submit' value='Delete' name='deletebyid'/><input type='hidden' name='id' value='$id' required></form>";
        echo "<td>$name";
        echo "<td>$category";
        echo "<td>$length";
        echo "<td>$strRented";
        echo "<td><form class='mini-form' action='inventory.php' method='post'><input class='btn btn--check' type='submit' value='$checkinout' name='changestatus'/><input type='hidden' name='id' value='$id' required><input type='hidden' name='currentstatus' value='$rented' required></form>";
        echo "</tr>";
    }
    echo "</tbody><tfooter><tr><td colspan='6'>";
    echo "<form action='inventory.php' method='post' >";
    echo "<input class='btn btn--delete' type='submit' value='Delete All' name='deleteall'/>";
    echo "</form></td></tr></tfooter></table>";
    
    unset($inventory);
}

function transformRentBool($rented){
    if(!$rented){
        return "available";
    }
    return "checked out";
}

function transformCheckoutType($rented){
    if(!$rented){
        return "check out";
    }
    return "checked in";
}

$mysqli = createDBConnection($dbhost, $dbuser, $dbpass, $dbname);

if(isset($_POST["addvideo"])){
    if(validateInput($mysqli, $db)==0){
        addToInventory($mysqli, $db);
    }
}

function validateInput($mysqli, $db){
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $length = trim($_POST['length']);
    $validationerrors = 0;
    
    //check if name is not null
    if($name === '' || $name === null){
        echo "<div class='error'>$name cannot be blank. </div>";
        $validationerrors++;
    }
    
    //check if name exists in database
    if(!($stmt = $mysqli->prepare("SELECT id FROM $db.inventory where name = ?"))){
        echo "<div class='error'>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }
    
    if (!$stmt->bind_param("s", $name )) {
        echo "<div class='error'>Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error. "</div>";
    }  
    
    if (!$stmt->execute()) {
        echo "<div class='error'>Execute failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    } 
    if($stmt->fetch()){
        echo "<div class='error'><strong>$name</strong> already exists in the database.  The name should be unique. </div>";
        $validationerrors++;
    }
    unset($stmt);
    
    //check if name too long
    if(strlen($name) > 255){
        echo "<div class='error'><strong>$name</strong> is an invalid name.  The name is too long it must be 255 characters or less. </div>";
        $validationerrors++;
    }
    
    //check in category too long
    if(strlen($category) > 255){
        echo "<div class='error'><strong>$category</strong> is an invalid category.  The category is too long it must be 255 characters or less. </div>";
        $validationerrors++;
    }
    
    //check if length is null or greater than 0. 
    if(!isPositiveInteger($length) && $length != null && $length != '' ){
        echo "<div class='error'><strong>$length</strong> is an invalid length.  The length must be blank or a positive integer. </div>";
        $validationerrors++;
    }
    
    return $validationerrors;
}

function isPositiveInteger($string){
  if(is_numeric($string) && ($string == (int)$string) && ((int)$string > 0)) {
    return true;
  }
  return false; 
}


function getCategorySelectOptions($mysqli, $db){
    $categoryfilterQuery;
    $categoryfilter = null;
    if(!($categoryfilterQuery = $mysqli->prepare("SELECT DISTINCT category FROM $db.inventory order by category"))){
        echo "<div class='error'>Prepare failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    }

    if (!$categoryfilterQuery->execute()) {
        echo "<div class='error'>Execute failed: (" . $mysqli->errno . ") " . $mysqli->error. "</div>";
    } 
    
    $category = null;
    
    if (!$categoryfilterQuery->bind_result($category )) {
        echo "<div class='error'>Binding results failed: (" . $categoryfilterQuery->errno . ") " . $categoryfilterQuery->error. "</div>";
    } 
    
    while ($categoryfilterQuery->fetch()) {
        if($category !== null && trim($category) !=='')
        $categoryfilter[] = $category;
    }
    
    unset($categoryfilterQuery);
    return $categoryfilter;
}

function printCategories($categoryfilter){
    foreach($categoryfilter as $category){
        echo "<option value='$category'>$category</option>";
    }
}

if(isset($_POST["deleteall"])){
    deleteAllInventory($mysqli, $db);
}
if(isset($_POST["deletebyid"])){
    deleteIdFromInventory($mysqli, $db,$_POST["id"]);
}
if(isset($_POST["changestatus"])){
    changeStatus($mysqli, $db,$_POST["id"],$_POST["currentstatus"]);
}

$categoryfilter = getCategorySelectOptions($mysqli, $db);
$inventory = getInventory($mysqli, $db);


?>
<div class="container">
    <form action="inventory.php" method="post">
        <div class="form-group">
            <label for="name">Name</label> <input type="text" name="name" required>
        </div>
        <div class="form-group">
            <label for="category">Category</label> <input type="text" name="category">
        </div>
        <div class="form-group">
            <label for="length">Length</label>
            <input type="text" name="length">
        </div>
            <input class="btn btn--add" type="submit" value="Add" name="addvideo">
    </form>
    <form action="inventory.php" method="post">
        <div class="form-group">
        <label>Category</label>
           <select name="filtercategory">
              <option value="other" selected>all movies</option>
              <?php printCategories($categoryfilter); ?>
            </select>
            </div>
            <div class="form-group">
        <input class="btn" type="submit" value="Filter">
        </div>
    </form>
<div>
    <?php createInventoryTable($inventory); ?>
</div>

</div>
</body>
</html>