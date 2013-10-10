<?php
$limit = $_GET['l'];
$table = $_GET['t'];

$server = "localhost";
$username = "root";
$password = "root";
$database = "ncaaranking";
 
$conn =  mysql_connect($server, $username, $password) or die("Couldn't connect to MySQL" . mysql_error());
mysql_select_db($database, $conn) or die ("Couldn't open $test: " . mysql_error());
$result = mysql_query("SELECT * FROM " . $table . " LIMIT " . $limit);
//$records = mysql_num_rows($result);

$rows = array();
while($r = mysql_fetch_assoc($result)) {
    $rows[] = $r;
}

print json_encode($rows);
 
mysql_close($conn);
?>
