<?php

  include 'pdo_conn.php';
  include 'membership_functions.php';
  include 'billing_functions.php';
  
  $dbh = civicrmConnect();
  $orgId = $_GET["orgId"];
?>
<html lang="en">
<head>
<title>Select members for billing</title>
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Membership Billing</title>
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <script src="js/jquery-jPaginate.js"></script>
  <script src="js/jquery.tablesorter.js"></script>
</head>
<body>
<?php

  $membersList = groupMembersByCompany($dbh);
  
  if(array_key_exists($orgId,$membersList)){
    $contactList = $membersList[$orgId];
    $members = array();
    
    foreach($contactList as $contactId){

       $memberInfo = array();
    
       $membershipDetails = getIndividualMemberDetails($dbh,$contactId);
       $membershipId = $membershipDetails["id"];
       $typeId = $membershipDetails["membership_type_id"];
       $statusId = $membershipDetails["status_id"];
       $joinDate = $membershipDetails["join_date"];
       $startDate = $membershipDetails["start_date"];
       $endDate = $membershipDetails["end_date"];
       $status = getMembershipStatus($dbh,$statusId);
       $memberId = getMemberId($dbh,$contactId);
       
       $contactDetails = getContactDetails($dbh,$contactId);
       $name = $contactDetails["name"];
       $email = getContactEmail($dbh,$contactId);
       $feeAmount = getMemberFeeAmount($dbh,$typeId);
       $addressDetails = getAddressDetails($dbh,$contactId);
       $street = $addressDetails["street"];
       $city = $addressDetails["city"];
       $address = $street." ".$city;
 
       $memberInfo["name"] = $name;
       $memberInfo["email"] = $email;
       $memberInfo["status"] = $status;
       $memberInfo["fee_amount"] = $feeAmount;
       $memberInfo["address"] = $address;
       $memberInfo["member_id"] = $memberId;
       $memberInfo["join_date"] = $joinDate;
       $memberInfo["start_date"] = $startDate;
       $memberInfo["end_date"] = $endDate;

       $members[$membershipId] = $memberInfo;
    }

    echo "<pre>";
    print_r($members);
    echo "</pre>";
  }

  else{
    echo "There are no members in this company.";
  }



?>
</body>
</html>
