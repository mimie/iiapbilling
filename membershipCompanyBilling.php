<html lang="en">
<head>
  <title>Membership Company Billing</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
<?php
  include 'login_functions.php';
  include 'pdo_conn.php';
  include 'membership_functions.php';

  $dbh = civicrmConnect();
  
  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  @$userId = $_GET["user"];

  $logout = logoutDiv($dbh,$userId);
  echo $logout;
  $header = headerDiv();
  echo $header;
  $companies = getAllCompanies($dbh);

  $displayCompanies = displayAllCompanies($dbh,$companies);
  echo $displayCompanies;
?>
</body>
</html>
