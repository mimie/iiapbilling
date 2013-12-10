<html>
<head>
<title>My Account</title>
  <link rel="stylesheet" type="text/css" href="menu.css">
  <!--<link rel="stylesheet" type="text/css" href="billingStyle.css">-->
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

  $userDetails = getUserDetailsById($dbh,$userId);
  $username = $userDetails["username"];
  $firstname = $userDetails["firstname"];
  $middlename = $userDetails["middlename"];
  $lastname = $userDetails["lastname"];
  $designation = $userDetails["designation"];

?>
  <br><br>
  <table align="center" style="border:1px solid #00BFFF;border-collapse:collapse;padding: 10px;">
   <tr>
    <th colspan="2" align="right" style="border:1px solid #00BFFF;border-collapse:collapse;padding: 7px;" bgcolor="#81DAF5"><font size="6" color="#0174DF">ACCOUNT DETAILS</font></th>
   </tr>
   <tr>
    <td colspan="2" align="right">
      <img src="images/forgot_password.png" height="40" width="40" title="Click to edit password">
      <img src="images/edit.png" height="40" width="40" title="Click to edit account information"></td>
   </tr>
   <tr>
    <th style="padding: 7px;" align="right"><font color="#0174DF">Username:</font></th>
    <td style="padding: 7px;"><font color="#01A9DB"><b><?=$username?></b></font></td>
   </tr>
   <tr>
    <th style="padding: 7px;" align="right"><font color="#0174DF">Firstname:</font></th>
    <td style="padding: 7px;"><font color="#01A9DB"><b><?=$firstname?></b></td>
   </tr>
   <tr>
    <th style="padding:7px;" align="right"><font color="#0174DF">Middlename:</font></th>
    <td style="padding:7px;"><font color="#01A9DB"><b><?=$middlename?></b></td>
   </tr>
   <tr>
    <th style="padding: 7px;" align="right"><font color="#0174DF">Lastname:</font></th>
    <td style="padding:7px;"><font color="#01A9DB"><b><?=$lastname?></b></td>
   </tr>
   <tr>
    <th style="padding:7px;" align="right"><font color="#0174DF">Designation:</font></th>
    <td style="padding:7px;"><font color="#01A9DB"><b><?=$designation?></b></td>
   </tr>
  </font>
  </table>
</body>
</html>
