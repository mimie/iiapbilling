<html>
<head>
<title>My Account</title>
  <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
<?php

  $dbh =  new PDO('mysql:host=localhost;dbname=webapp_civicrm','root', 'mysqladmin');
  include 'login_functions.php';

  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  $userId = $_GET["user"];

  echo "<div align='center' style='padding:6px;'>";
  $logout = logoutDiv($dbh,$userId);
  echo $logout;
  echo "</div";

  echo "<div align='center' style='padding:6px;'>";
  $header = headerDiv();
  echo $header;
  echo "</div>";

?>
</body>
</html>
