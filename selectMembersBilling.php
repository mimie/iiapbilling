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
  $lastYear = date('Y',strtotime('-1 year'));
  $currentYear = date('Y');
  $nextYear = date('Y',strtotime('+1 year'));

  //$expiredDate = date('Y-m-d',strtotime($currentYear.'-12-31'));
  $currentExpiredDate = date('Y-m-d',strtotime($currentYear.'-12-31'));
  $lastExpiredDate = date('Y-m-d',strtotime($lastYear.'-12-31'));
  $nextExpiredDate = date('Y-m-d',strtotime($nextYear.'-12-31'));

  $formatCurrent = date('F j Y',strtotime($currentExpiredDate));
  $formatLast = date('F j Y',strtotime($lastExpiredDate));
  $formatNext = date('F j Y',strtotime($nextExpiredDate));

?>
<form method="POST" action="">
  <select name="expiredDate">
    <option value="select">- Select date of expiration -</option>
    <option value="<?=$lastExpiredDate?>"><?=$formatLast?></option>
    <option value="<?=$currentExpiredDate?>"><?=$formatCurrent?></option>
    <option value="<?=$nextExpiredDate?>"><?=$formatNext?></option>
  </select>&nbsp;
  <input type="submit" name="dates" value="View Members" onclick="defaultSelect(document.getElementById('expiredDate'),'Please select an expired date to view members.')">
</form>
<?php

   $membersList = getMembersByOrgId($dbh,$orgId);
   $members = array();
   
   foreach($membersList as $member){
     $memberInfo = array();

     $membershipId = $member["id"];
     $name = $member["name"];
     $contactId = $member["contact_id"];
     $orgName = $member["organization_name"];
     $endDate = $member["end_date"];
     $startDate = $member["start_date"];
     $joinDate = $member["join_date"];
     $statusId = $member["status_id"];
     $typeId = $member["membership_type_id"];
     
     $status = getMembershipStatus($dbh,$statusId);
     $memberId = getMemberId($dbh,$contactId);

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

   if(isset($_POST["expiredDate"])){
     $date = $_POST["expiredDate"];
     $membersList = getMembersByDate($dbh,$orgId,$date);
     $members = array();
   
     foreach($membersList as $member){
       $memberInfo = array();

       $membershipId = $member["id"];
       $name = $member["name"];
       $contactId = $member["contact_id"];
       $orgName = $member["organization_name"];
       $endDate = $member["end_date"];
       $startDate = $member["start_date"];
       $joinDate = $member["join_date"];
       $statusId = $member["status_id"];
       $typeId = $member["membership_type_id"];
     
       $status = getMembershipStatus($dbh,$statusId);
       $memberId = getMemberId($dbh,$contactId);

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
   $billedMembers = displayBilledMembers($members);
   echo $billedMembers;

   }
   else{
   $billedMembers = displayBilledMembers($members);
   echo $billedMembers;
   }

?>
</body>
</html>
