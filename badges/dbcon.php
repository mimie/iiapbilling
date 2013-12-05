<?php
$db=mysql_connect('localhost', 'root', 'mysqladmin');
if (!$db) {
          die('Could not connect: ' . mysql_error());
 }

mysql_select_db("webapp_civicrm", $db);

?>

