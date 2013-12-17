<?php

/**
 *function to get membershipDetails
 *membership_type_id = 'General' from civicrm_membership_type
 */

function getMembersToExpire(PDO $dbh,$endDate){

  $sql = $dbh->prepare("SELECT id,contact_id,end_date,status_id,membership_type_id,join_date,start_date,end_date
                        FROM civicrm_membership
                        WHERE membership_type_id = '1'
                        AND end_date = ?
                       ");
  $sql->bindParam(1,$endDate,PDO::PARAM_STR,10);
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details;
}

function getMembersByName(PDO $dbh, $endDate,$name){

  $sql = $dbh->prepare("SELECT cm.id, cm.contact_id, cm.end_date,cm.status_id,cm.membership_type_id,cm.join_date,cm.start_date,cm.end_date
                        FROM civicrm_membership cm, civicrm_contact cc
                        WHERE membership_type_id = '1'
                        AND cm.end_date = ?
                        AND cc.display_name LIKE '%$name%'
                      ");
  $sql->bindParam(1,$endDate,PDO::PARAM_STR,10);
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details;
}

function getMembershipStatus($dbh,$statusId){

  $sql = $dbh->prepare("SELECT id, name FROM civicrm_membership_status
                        WHERE id = '$statusId'
                       ");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $status = $details["name"];

  return $status;
  
}

function getMemberFeeAmount($dbh,$typeId){

  $sql = $dbh->prepare("SELECT minimum_fee FROM civicrm_membership_type
                        WHERE id = '$typeId'");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $feeAmount = $details["minimum_fee"];

  return $feeAmount;
}

function getMemberType($dbh,$typeId){

  $sql = $dbh->prepare("SELECT name FROM civicrm_membership_type
                        WHERE id = '$typeId'");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $memberType = $details["name"];

  return $memberType;
}

function checkMembershipBilling($dbh,$membershipId,$memberBillingYear){

  $year = $memberBillingYear;
  $sql = $dbh->prepare("SELECT COUNT(*) as exist,billing_no,bill_date FROM billing_membership 
                        WHERE membership_id = '$membershipId'
                        AND year = '$year'
                       ");
  $sql->execute();
  $sqlDetails = $sql->fetch(PDO::FETCH_ASSOC);
 
  return $sqlDetails;
 
}



function displayMemberBilling($dbh,array $members,$expiredDate,$userId){
  
  $expiredYear = date("Y",strtotime($expiredDate));
  $memberBillingYear = intval($expiredYear) + 1;

  //$nextYear = date('Y', strtotime('+1 year'));
  $html = "<table width='100%'>"
        . "<tr>"
        . "<th colspan='15'>Membership Billing</th>"
        . "</tr>"
        . "<tr>"
        . "<th>Select Members</th>"
        . "<th>Member Name</th>"
        . "<th>Email</th>"
        . "<th>Membership Status</th>"
        . "<th>Organization Name</th>"
        . "<th>Member Fee Amount</th>"
        . "<th>Print Bill</th>"
        . "<th>Send Bill</th>"
        . "<th>Payment Status</th>"
        . "<th>Billing Reference No.</th>"
        . "<th>Billing Date</th>"
        . "<th>Billing Address</th>"
        . "<th>Membership Information</th>"
        . "<th>Billing PDF Download</th>"
        . "</tr>";

  foreach($members as $membershipId => $details){

    $name = mb_convert_encoding($details["name"], "UTF-8");
    $email = $details["email"];
    $status = $details["status"];
    $company = $details["company"];
    $amount = $details["fee_amount"];
    $address = mb_convert_encoding($details["address"], "UTF-8");
    $membersLinkInfo = membersLink($membershipId,$userId);
    $infoBilling = checkMembershipBilling($dbh,$membershipId,$memberBillingYear);
    $checkBillExist = $infoBilling["exist"];
    $billingNo = $infoBilling["billing_no"];
    $billingDate = $infoBilling["bill_date"];
    $billingDate = date("Y-m-d",strtotime($billingDate));
  
    $disabled = $checkBillExist == 1 ? 'disabled' : '';
    $checkbox = $checkBillExist == 1 ? '' : 'class=checkbox';
   $html = $html."<tr>"
          . "<td><input type='checkbox' name='membershipIds[]' value='$membershipId' $checkbox $disabled></td>"
          . "<td>$name</td>"
          . "<td>$email</td>"
          . "<td>$status</td>"
          . "<td>$company</td>"
          . "<td>$amount</td>";

    if($checkBillExist == 1){

          $year = $memberBillingYear;
          $billingId = getBillingId($dbh,$membershipId,$year);
          $html = $html . "<td>"
                . "<a href='memberBillingReference.php?billingId=$billingId&user=$userId' target='_blank' title='Click to print membership bill' style='text-decoration: none;'>"
                . "<img src='images/printer-icon.png' width='40' height='40'><br>Print"
                . "</a>"
                . "</td>"
                . "<td><a href='emails/membershipBilling/sendMemberBilling.php?billingId=$billingId&user=$userId' style='text-decoration:none;'><img src='images/email.jpg' width='40' height='40'><br>Send</a></td>"
                . "<td>Pay Later</td>"
                . "<td>$billingNo</td>"
                . "<td>$billingDate</td>"
                . "<td>$address</td>"
                . "<td>$membersLinkInfo</td>";

          $pdfFile = "pdf/membershipBilling/".$billingNo.".pdf";
          if(file_exists($pdfFile)) {
             $html = $html . "<td><a href='pdf/membershipBilling/".$billingNo.".pdf' download='IIAP_MembershipBilling_".$billingNo."' title='Click to download pdf file'><img src='images/pdf_download.jpg' width='40' height='40'></td>"
                    . "</tr>";
          }

          else{
             $html = $html . "<td><a href='pdf/membershipBilling/generatePDFMemberBilling.php?billingId=$billingId&user=$userId' title='Click to generate pdf'><img src='images/pdf_me.png' width='50' height='50'> </a></td>"
                   . "</tr>";
          }
      
    }

    else{
       $html = $html."<td><img src='images/not_available.png' width='25' height='25'><br></td>"
             . "<td><img src='images/not_available.png' width='25' height='25'></td>"
             . "<td></td>"
             . "<td></td>"
             . "<td></td>"
             . "<td>$address</td>"
             . "<td>$membersLinkInfo</td>"
             . "<td><img src='images/not_available_download.png' width='40' height='40'></td>"
             . "</tr>";
    }
    
  }

  $html = $html."</table>";

  return $html;
}

function membersLink($membershipId,$userId){


  $link = "<a href=\"membership_info.php?id=$membershipId&user=$userId\""
        . "title='Click to view membership information'"
        . "onclick=\"javascript:void window.open('membership_info.php?id=$membershipId&user=$userId','1384398816566','width=600,height=400,toolbar=1,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');"
        . "return false;\">"
        . "<img src='view_member.png'>"
        . "</a>"; 

  return $link;


}

function displayMemberInfo(PDO $dbh,array $memberInfo){

   $name = mb_convert_encoding($memberInfo["name"],"UTF-8");
   $email = $memberInfo["email"];
   $status = $memberInfo["status"];
   $company = mb_convert_encoding($memberInfo["company"],"UTF-8");
   $address = mb_convert_encoding($memberInfo["address"],"UTF-8");
   $joinDate = $memberInfo["join_date"];
   $startDate = $memberInfo["start_date"];
   $endDate = $memberInfo["end_date"];
   $contactId = $memberInfo["contact_id"];
   $memberId = getMemberId($dbh,$contactId);
   $memberType = $memberInfo["member_type"];

   $joinDate = date("F j Y",strtotime($joinDate));
   $startDate = date("F j Y",strtotime($startDate));
   $endDate = date("F j Y",strtotime($endDate));

   $html = "<table>"
         . "<tr><th>Member Name</th><td><b>".strtoupper($name)."</b></td></tr>"
         . "<tr><th>Member ID</th><td>$memberId</td></tr>"
         . "<tr><th>Membership Type</th><td>$memberType</td></tr>"
         . "<tr><th>Join Date</th><td>$joinDate</td></tr>"
         . "<tr><th>Start Date</th><td>$startDate</td></tr>"
         . "<tr><th>End Date</th><td>$endDate</td></tr>"
         . "<tr><th>Email</th><td>$email</td></tr>"
         . "<tr><th>Membership Status</th><td>$status</td></tr>"
         . "<tr><th>Organization Name</th><td>$company</td></tr>"
         . "<tr><th>Organization Address</th><td>$address</td></tr>"
         . "</table>";

   return $html;

}

function getOrgId($dbh,$organization){

  //$organization = stripslashes($organization); 
  $sql = $dbh->prepare("SELECT id FROM civicrm_contact
                         WHERE contact_type = 'Organization'
                         AND display_name = :organization_name");
  
  $sql->bindParam(':organization_name', $organization, PDO::PARAM_STR,250);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $orgId = $result["id"];
   
  return $orgId;

}

function insertMemberBilling($dbh,array $memberInfo,$membershipYear){

  $membership_id = $memberInfo["membership_id"];
  $contact_id = $memberInfo["contact_id"];
  $membership_type = $memberInfo["member_type"];
  $member_name = $memberInfo["name"];
  $email = $memberInfo["email"];
  $street = $memberInfo["street"];
  $city = $memberInfo["city"];
  $bill_address = $memberInfo["address"];
  $organization_name = $memberInfo["company"];
  $org_contact_id = $memberInfo["org_contact_id"];
  $fee_amount = $memberInfo["fee_amount"];
  $subtotal = $fee_amount;
  $vat = 0.0;

  $sqlMaxBillingId = $dbh->prepare("SELECT MAX(id) as prevBillingId FROM billing_membership");
  $sqlMaxBillingId->execute();
  $maxBillingId = $sqlMaxBillingId->fetch(PDO::FETCH_ASSOC);
  $maxBillingId = $maxBillingId["prevBillingId"] + 1;
  $billing_no = "MEM".$maxBillingId;
  $year = $membershipYear;
  

  $sql = $dbh->prepare("INSERT INTO billing_membership
                        (membership_id,contact_id,membership_type,member_name,email,street,city,bill_address,organization_name,org_contact_id,fee_amount,subtotal,vat,billing_no,year)
                        VALUES ('$membership_id','$contact_id','$membership_type','$member_name','$email','$street','$city','$bill_address','$organization_name','$org_contact_id','$fee_amount','$subtotal','$vat','$billing_no','$year')
                       ");
  var_dump($sql);
  $sql->execute();
}

function getBillingId($dbh,$membershipId,$year){

  $sql = $dbh->prepare("SELECT id FROM billing_membership
                        WHERE membership_id = '$membershipId'
                        AND year = '$year'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $id = $result["id"];

  return $id;

}

function getMemberBillingDetails($dbh,$billingId){

  $sql = $dbh->prepare("SELECT member_name, organization_name,contact_id,bill_date, billing_no, street,city, fee_amount, year
                        FROM billing_membership
                        WHERE id = '$billingId'
                       ");
  $sql->execute();
  $billingDetails = $sql->fetch(PDO::FETCH_ASSOC);

  return $billingDetails;
}

function getAllMembershipStatus(PDO $dbh){

   $sql = $dbh->prepare("SELECT id, name FROM civicrm_membership_status");
   $sql->execute();

   $result = $sql->fetchAll(PDO::FETCH_ASSOC);
   $status = array();

   foreach($result as $key => $value){

     $id = $value["id"];
     $name = $value["name"];

     $status[$id] = $name;
   }

  return $status;
}

function getAllMembershipType(PDO $dbh){

  $sql = $dbh->prepare("SELECT id,name FROM civicrm_membership_type");
  $sql->execute();

  $result = $sql->fetchAll(PDO::FETCH_ASSOC);
  $type = array();

  foreach($result as $key => $value){
    
    $id = $value["id"];
    $name = $value["name"];

    $type[$id] = $name;
  }

  return $type;
}

/*
 *this will get all organizations listed in the civicrm contact
 */
function getAllCompanies(PDO $dbh){

 $sql = $dbh->prepare("SELECT id, display_name FROM civicrm_contact 
                       WHERE contact_type='Organization' ORDER BY display_name");
 $sql->execute();
 $result = $sql->fetchAll(PDO::FETCH_ASSOC);
 $companies = array();

 foreach($result as $key => $value){
  
   $id = $value["id"];
   $orgName = $value["display_name"];
   $companies[$id] = $orgName;
 }

 return $companies;

}

function displayAllCompanies(PDO $dbh,array $companies){

  $html = "<table width='100%'>"
        . "<tr>"
        . "<th>Organization Name</th>"
        . "<th>Email</th>"
        . "<th>Total Amount</th>"
        . "<th>Print Bill</th>"
        . "<th>Send Bill</th>"
        . "<th>Payment Status</th>"
        . "<th>Billing Reference No.</th>"
        . "<th>Billing Date</th>"
        . "<th>Billing Address</th>"
        . "<th>Select Employees For Billing</th>"
        . "<th>Billing PDF Download</th>"
        . "</tr>";
  foreach($companies as $id => $orgName){
    
    $html = $html."<tr>"
          . "<td>$orgName</td>"
          . "<td>Email</td>"
          . "<td>Total Amount</td>"
          . "<td>Print Bill</td>"
          . "<td>Send Bill</td>"
          . "<td>Payment Status</td>"
          . "<td>Billing Reference No.</td>"
          . "<td>Billing Date</td>"
          . "<td>Billing Address</td>"
          . "<td><a href='selectMembersBilling.php?orgId=$id' target='_blank'><img src='images/add_icon.png'></a></td>"
          . "<td>Billing PDF Download</td>"
          . "</tr>";
  }

  $html = $html."</table>";
  return $html;
}

/*
 *this will group contacts by organization
 *the organization_id => contactIds(that exist in civicrm_membership)
 */
function groupMembersByCompany(PDO $dbh){

  $sql = $dbh->prepare("SELECT DISTINCT(cc.id), cc.organization_name
                        FROM civicrm_contact cc, civicrm_membership cm
                        WHERE cc.id = cm.contact_id
                        AND cc.organization_name != 'NULL'
                       ");
  $sql->execute();
  $result = $sql->fetchAll(PDO::FETCH_ASSOC);

  $groupByCompany = array();

  foreach($result as $key => $value){
    $contacts = array();

    $contactId = $value["id"];
    $orgName = $value["organization_name"];
    $orgId = getOrgId($dbh,$orgName);

    if(array_key_exists($orgId,$groupByCompany)){
       $contacts = $groupByCompany[$orgId];
       array_push($contacts,$contactId);
       $groupByCompany[$orgId] = $contacts;
    }

    else{
       array_push($contacts,$contactId);
       $groupByCompany[$orgId] = $contacts;
    }
  }

  return $groupByCompany;
}

function getIndividualMemberDetails(PDO $dbh,$contactId){

  $sql = $dbh->prepare("SELECT id,membership_type_id, join_date, start_date, end_date, status_id
                        FROM civicrm_membership
                        WHERE contact_id = :contactId
                      ");

  $sql->bindParam(':contactId',$contactId,PDO::PARAM_INT);
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);

  return $result;
  
}

function displayBilledMembers($billedMembers){

  $html = "<table>"
        . "<tr>"
        . "<th>Select Members</th>"
        . "<th>Member Name</th>"
        . "<th>Email</th>"
        . "<th>Membership Status</th>"
        . "<th>Member Fee Amount</th>"
        . "<th>Address</th>"
        . "<th>Member Id</th>"
        . "<th>Join Date</th>"
        . "<th>Start Date</th>"
        . "<th>End Date</th>"
        . "</tr>";

  foreach($billedMembers as $membershipId => $details){
    
     $name = $details["name"];
     $email = $details["email"];
     $status = $details["status"];
     $feeAmount = $details["fee_amount"];
     $address = $details["address"];
     $memberId = $details["member_id"];
     $joinDate = $details["join_date"];
     $joinDate = date("F j Y",strtotime($joinDate));
     $startDate = $details["start_date"];
     $startDate = date("F j Y",strtotime($startDate));
     $endDate = $details["end_date"];
     $endDate = date("F j Y",strtotime($endDate));

    $html = $html."<tr>"
          . "<td><input type='checkbox'></td>"
          . "<td>$name</td>"
          . "<td>$email</td>"
          . "<td>$status</td>"
          . "<td>$feeAmount</td>"
          . "<td>$address</td>"
          . "<td>$memberId</td>"
          . "<td>$joinDate</td>"
          . "<td>$startDate</td>"
          . "<td>$endDate</td>"
          . "</tr>";
  }   
      
  $html = $html."</table>";

  return $html;
}

?>
