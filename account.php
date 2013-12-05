<html>
<head>
<title>My Account</title>
  <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
<?php

  include 'login_functions.php';

  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  echo "<div align='center' style='padding:6px;'>";
  $logout = logoutDiv();
  echo $logout;
  echo "</div";

  echo "<div align='center' style='padding:6px;'>";
  $header = headerDiv();
  echo $header;
  echo "</div>";

?>
</body>
</html>
