<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
$dbCredentials = "dbCredentials.php";
include($dbCredentials);

global $mysqli;

function createDBConnection($dbhost, $dbuser, $dbpass, $dbname){
    $mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
    return $mysqli;
    // Check connection
    if (!$mysqli || $mysqli->connect_error) {
        echo "Connection error " .$mysqli->connect_error. " " .$mysqli->connect_error;
    } 
}

function displayDBConnectionInfo($mysqli, $dbhost, $dbname){
     if (!$mysqli || $mysqli->connect_error) {
        echo "<div> Connected to host: $dbhost </div>";
        echo "<div> Connected to database: $dbname </div>";  
     }
}

function createtable($mysqli, $db){
    $createtablesql = "CREATE TABLE $db.inventory (
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY 
    , name VARCHAR( 255 ) NOT NULL 
    , category VARCHAR( 255 ) NULL 
    , length INT NULL
    , rented BOOL NOT NULL DEFAULT 0 
    , UNIQUE (name)
    , CHECK (length > 0 OR ISNULL(length)))";
    if($mysqli->query($createtablesql)){
        echo "Created Table";
    } else {
        echo "Create Table Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
}

function droptable($mysqli, $db){
    $droptablesql = "DROP TABLE $db.inventory";
    if($mysqli->query($droptablesql)){
        echo "Dropped Table";
    } else {
        echo "Drop Table Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
}

$mysqli = createDBConnection($dbhost, $dbuser, $dbpass, $dbname);

function displayCreateTableMessage($mysqli, $db){
    if(isset($_POST["createtable"])){
        echo "creating table";
        createtable($mysqli, $db);
    }
}

function displayDropTableMessage($mysqli, $db){
    if(isset($_POST["droptable"])){
        echo "dropping table";
        droptable($mysqli, $db);
    }
}

function instertTestData($mysqli, $db){
    $sql = "
    INSERT INTO $db.inventory (name, category, length) VALUES
    ('Avengers: Age of Ultron', 'Action', 141),
    ('Furious Seven', 'Action', 137),
    ('Mad Max: Fury Road', 'Action', 120),
    ('The Avengers', 'Action', 143),
    ('Jupiter Ascending', 'Action', 127),
    ('Kingsman: The Secret Service', 'Comedy', 129),
    ('Mortdecai', 'Comedy', 107),
    ('Paul Blart: Mall Cop 2', 'Comedy', 94),
    ('The Wedding Ringer', 'Comedy', 101),
    ('Home', 'Comedy', 94),
    ('The Age of Adaline', 'Drama', 112),
    ('Ex Machina', 'Drama', 108),
    ('The Water Diviner', 'Drama', 111),
    ('Fifty Shades of Gray', 'Drama', 125),
    ('The Longest Ride', 'Drama', 139),
    ('ztest', null , null);
    ";

    if (!$mysqli || $mysqli->connect_error) {
        echo "Connection error " .$mysqli->connect_error. " " .$mysqli->connect_error;
    } 
   
   if($mysqli->query($sql)){
        echo "Inserted Test Data";
    } else {
        echo "Query failed: (" . $mysqli->errno . ") " . $mysqli->error;
    }
}

function displayInstertTestDataMessage($mysqli, $dbname){
    if(isset($_POST["inserttestdata"])){
        echo "inserting test data ";
        instertTestData($mysqli, $dbname);
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
<?php displayDBConnectionInfo($mysqli, $dbhost, $dbname) ?>
</div>
<div>
<form action="setup.php" method="post">
    <input type="submit" value="Create Table" name="createtable">
</form>
<div><?php displayCreateTableMessage($mysqli, $db) ?></div>

<form action="setup.php" method="post">
    <input type="submit" value="Drop Table" name="droptable">
</form>
<div><?php displayDropTableMessage($mysqli, $db) ?></div>
</div>
<form action="setup.php" method="post">
    <input type="submit" value="Add Test Data" name="inserttestdata">
</form>
<div><?php displayInstertTestDataMessage($mysqli, $db) ?></div>
</body>
</html>