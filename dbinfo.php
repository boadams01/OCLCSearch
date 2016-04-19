<?php


//MySQL Database
/*
*Information used to connect to MySQL database
*$dbhost is the host name of the server with MySQL installed
*$dbuser and $dbpass are the username and password that have
*	SELECT, INSERT, UPDATE, and DELETE privileges on the openroom database
*$dbdatabase is the name of the database this application uses (default: SpecialCollectionsReservations)
*/

//for development
$dbhost = "localhost";


$dbuser = "bo";
$dbpass = "bo";

$dbdatabase = "Commentaries";



/*I'm using PDO
This is a newer way of doing it, and it's better
Old way was basic mysql
*/
$pdostring="mysql:host=" . $dbhost . ";dbname=" . $dbdatabase . ";charset=utf8";


$db = new PDO($pdostring, $dbuser, $dbpass, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);


//mysql_connect($dbhost, $dbuser, $dbpass) or die('Can\'t connect to the database. Error: ' . mysql_error());
//mysql_select_db($dbdatabase) or die('Can\'t connect to the database. Error: ' . mysql_error());

?>

