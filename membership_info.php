<html lang="en">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Membership Billing</title>
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
<?php
  $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin');
  include 'login_functions.php';
  include 'membership_functions.php';
  include 'billing_functions.php';

  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  $userId = $_GET["user"];
  $id = $_GET["id"];
  echo "<div style='padding:16px;' width='100%'>";  
  $logout = logoutDiv($dbh,$userId);
  echo $logout;

  $currentYear = date('Y');
  $expiredDate = date('Y-m-d',strtotime($currentYear.'-12-31'));

  $membersToExpire = getMembersToExpire($dbh,$expiredDate);
  $members = array();
  $memberInfo = array();

  foreach($membersToExpire as $key => $details){ 
    $membershipId = $details["id"];
    $contactId = $details["contact_id"];
    $statusId = $details["status_id"];
    $typeId = $details["membership_type_id"];
    $joinDate = $details["join_date"];
    $startDate = $details["start_date"];
    $endDate = $details["end_date"];
    $status = getMembershipStatus($dbh,$statusId);
    $memberType = getMemberType($dbh,$typeId);

    $contactDetails = getContactDetails($dbh,$contactId);
    $name = $contactDetails["name"];
    $orgname = $contactDetails["companyName"];
    $email = getContactEmail($dbh,$contactId);
    $feeAmount = getMemberFeeAmount($dbh,$typeId);
    $addressDetails = getAddressDetails($dbh,$contactId);
    $street = $addressDetails["street"];
    $city = $addressDetails["city"];
    $address = $street." ".$city;
    //echo mb_convert_encoding($name, "UTF-8");

    $memberInfo["contact_id"] = $contactId;
    $memberInfo["status"] = $status;
    $memberInfo["name"] = $name;
    $memberInfo["company"] = $orgname;
    $memberInfo["email"] = $email;
    $memberInfo["fee_amount"] = $feeAmount;
    $memberInfo["address"] = $address;
    $memberInfo["join_date"] = $joinDate;
    $memberInfo["start_date"] = $startDate;
    $memberInfo["end_date"] = $endDate;
    $memberInfo["member_type"] = $memberType;

    $members[$membershipId] = $memberInfo;
    unset($memberInfo);
    $memberInfo = array();
  }

  $memberDetails = $members[$id];
  $displayMemberInfo = displayMemberInfo($dbh,$memberDetails);
  echo $displayMemberInfo;
  echo "</div>";

?>
</body>
</html>
