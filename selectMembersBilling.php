<?php

  include 'pdo_conn.php';
  include 'membership_functions.php';
  
  $dbh = civicrmConnect();
  $orgId = $_GET["orgId"];
?>
<html lang="en">
<head>
<title>Select members for billing</title>
</head>
<body>
<?php

  $membersList = groupMembersByCompany($dbh);
  $members = $membersList[$orgId];

?>
</body>
</html>
