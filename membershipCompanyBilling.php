<html lang="en">
<head>
  <title>Membership Company Billing</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
</head>
<body>
<?php
  include 'login_functions.php';
  include 'pdo_conn.php';
  include 'membership_functions.php';

  $dbh = civicrmConnect();
  $companies = getAllCompanies($dbh);


?>
</body>
</html>
