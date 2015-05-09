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

function displayDBConnectionInfo($dbhost, $dbname){
    global $mysqli;
     if (!$mysqli || $mysqli->connect_error) {
        echo "<div> Connected to host: $dbhost </div>";
        echo "<div> Connected to database: $dbname </div>";  
     }
}

function createtable($dbname){
    global $mysqli;
    $createtablesql = "CREATE TABLE $dbname.inventory (id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,name VARCHAR( 255 ) NOT NULL ,category VARCHAR( 255 ) NULL ,length TINYINT NULL ,rented BOOL NOT NULL ,UNIQUE (name))";
    global $mysqli;
    if($mysqli->query($createtablesql)){
        echo "Created Table";
    } else {
        echo "table creation was not successful";
    }
}

function droptable($dbname){
    global $mysqli;
    $droptablesql = "DROP TABLE $dbname.inventory";
    global $mysqli;
    if($mysqli->query($droptablesql)){
        echo "Dropped Table";
    } else {
        echo "table drop was not successful";
    }
}

createDBConnection($dbhost, $dbuser, $dbpass, $dbname);

function displayCreateTableMessage($dbname){
    if(isset($_POST["createtable"])){
        echo "creating table";
        createtable($dbname);
    }
}

function displayDropTableMessage($dbname){
    if(isset($_POST["droptable"])){
        echo "dropping table";
        droptable($dbname);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Setup</title>
</head>
<body>
<div>
<?php displayDBConnectionInfo($dbhost, $dbname) ?>
</div>
<div>
<form action="setup.php" method="post">
    <input type="submit" value="Create Table" name="createtable">
</form>
<div><?php displayCreateTableMessage($dbname) ?></div>

<form action="setup.php" method="post">
    <input type="submit" value="Drop Table" name="droptable">
</form>
<div><?php displayDropTableMessage($dbname) ?></div>
</div>
</body>
</html>