<html>
<head>
<title>Billing List</title>
<link rel="stylesheet" type="text/css" href="billingStyle.css">
<link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
<?php

  $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin');
  include 'dbcon.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'billing_functions.php';
  include '../weberpdev/postFunction.php';
  include 'send_functions.php';
  include 'login_functions.php';
 
  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }
  
  $userId = $_GET["user"];
  
  $logout = logoutDiv($dbh,$userId);
  echo $logout;
  echo "<br>";

   $weberpConn = new PDO('mysql:host=localhost;dbname=webapp_civicrm','root','mysqladmin');
   @$eventId = $_GET["eventId"];

   $eventDetails = getEventDetails($dbh,$eventId);
   $eventName = $eventDetails["event_name"];
   $eventStartDate = $eventDetails["start_date"];
   $eventEndDate = $eventDetails["end_date"];
   $eventTypeName = getEventTypeName($dbh,$eventId);
   $locationDetails = getEventLocation($dbh,$eventId);
   $eventLocation = formatEventLocation($locationDetails);
   //navigation
   echo "<div id = 'navigation'>";
   echo "<a href='events2.php?&user=$userId'><b>Event List</b></a>";
   echo "&nbsp;&nbsp;<b>&gt;</b>&nbsp;";
   echo "<i>$eventName</i>";
   echo "</div>";

   echo "<div id='eventDetails'>";
   echo "<table border = '1'>";
   echo "<tr>";
   echo "<th>Event Name</th><td><b><i>$eventName</i></b></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Start Date</th><td><i>$eventStartDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>End Date</th><td><i>$eventEndDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Event Type</th><td><i>$eventTypeName</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Event Location</th><td><i>$eventLocation</i></td>";
   echo "</tr>";
   echo "</table>";
   echo "</div>";

?>

<?php 

   echo "<div id='billingNav'>";
   echo "<table width='100%'>";
   echo "<tr>";
   echo "<td align='center'><a href='individualBilling.php?eventId=$eventId&billingType=individual&user=$userId'>INDIVIDUAL BILLING</a></td>";
   echo "<td align='center' bgcolor='#084B8A'><a href='companyBilling.php?eventId=$eventId&billingType=company&user=$userId'>COMPANY BILLING</td>";
   echo "</tr>";
   echo "</table>";  
   echo "</div>";
   
   $customGroupDetails = getCustomGroupDetails($dbh,"Billing");
   $customGroupId = $customGroupDetails["id"];
   $tableName = $customGroupDetails["table_name"];
   
   $column = getColumnNameStoredValues($dbh,$customGroupId);
   $columnName = $column["column_name"];

   $billingType =  getTypeOfBilling($dbh,$tableName,$columnName);
   $orgs = getOrganization($dbh);
   $participants = getEventParticipantIds($dbh, $eventId);
   $eventBillingTypes = getParticipantsBillingType($billingType,$participants);
   $individualBillingTypes = $eventBillingTypes["Individual"];

?>
<b>Select action to process:</b>&nbsp;
<form name="process" method="Post" action="individualBilling.php?eventId=<?=$eventId?>&billingType=individual">
  <select name="processType">
    <option value="select">Select ation type</option>
    <option value="Generate Bill">Generate Bill</option>
    <option value="Send Bill">Send Bill</option>
    <option value="Post to Weberp">Post to Weberp</option>
  </select>
  <input type="submit" value="Process Action" name="processAction">
<?

   echo "<br><br><br>";
   echo "<table border='1' width='100%'>";
   echo "<tr><th colspan='15'>Individual Billing</th></tr>";
   echo "<tr>";
   echo "<th>Participant Name</th>";
   echo "<th>Email</th>";
   echo "<th>Participant Status</th>";
   echo "<th>Organization Name</th>";
   echo "<th>Fee Amount</th>";
   echo "<th>Subtotal</th>";
   echo "<th>12% VAT</th>";
   echo "<th>Generate Bill</th>";
   echo "<th>Send Bill</th>";
   echo "<th>Post Bill</th>";
   echo "<th>Payment Status</th>";
   echo "<th>Billing Reference No.</th>";
   echo "<th>Billing Date</th>";
   echo "<th>Billing Address</th>";
   echo "<th>Billing PDF Download</th>";
   echo "</tr>";
   

   foreach($individualBillingTypes as $participantId){
       $contact = $participants[$participantId];
       $contact_id = $contact["contact_id"];
       $fee_amount = $contact["fee_amount"];
       $contactDetails = getContactDetails($dbh, $contact_id);
       $participant_name = $contactDetails["name"];
       $organization_name = $contactDetails["companyName"];
       $email = getContactEmail($dbh,$contact_id);
       $status = getStatusType($dbh,$participantId);
       
       if($eventTypeName == 'CON'){
          $subtotal = $fee_amount;
          $tax = 0.0;
       }

       else{
         $tax = round($fee_amount/9.3333,2);
         $subtotal = round($fee_amount - $tax,2);
         
       }

       echo "<tr>";
       echo "<td align='center'>$participant_name</td>";
       echo "<td align='center'>$email</td>";
       echo "<td align='center'>$status</td>";
       echo "<td align='center'>$organization_name</td>";
       echo "<td align='center'>$fee_amount</td>";
       echo "<td align='center'>$subtotal</td>";
       echo "<td align='center'>$tax</td>";
       echo "<td align='center'>";

       $isBillGenerated = checkBillGenerated($dbh,$participantId,$eventId);
       if($isBillGenerated == 1){

          $paymentStatus = getPaymentStatus($dbh,$contact_id,$eventId);

          $billingNo = getIndividualBillingNo($dbh,$participantId,$eventId);
          $billingDate = getIndividualBillingDate($dbh,$participantId,$eventId);
          $billingAddress = getIndividualBillingAddress($dbh,$participantId,$eventId);
   
          if($eventTypeName == 'CON'){
             echo "<a href='individualConvention.php?billingRef=$billingNo&eventId=$eventId&user=$userId' style='text-decoration:none;' target ='_blank'><img src='printer-icon.png' width='50' height='50'>";
          }

          else{
             echo "<a href='individualBillingReference.php?billingRef=$billingNo&eventId=$eventId&user=$userId' style='text-decoration:none;' target ='_blank'><img src='printer-icon.png' width='50' height='50'>";
          }
          echo "<br>Print</a>";
          echo "</td>";
          echo "<td align='center'>";
          echo "<a href='sendIndividualBilling.php?billingRef=$billingNo&eventId=$eventId' style='text-decoration:none;' target ='_blank'><img src='email.jpg' width='50' height='50'>";
          echo "<br>Email</a>";
          echo "</td>";

          if($status == 'Registered'){
            echo "<td align='center'><input type='checkbox' name='postIds[]' value='$contact_id' disabled></td>";
          }

          else{
            echo "<td align='center'><input type='checkbox' name='postIds[]' value='$contact_id'></td>";
          }
      
          echo "<td align='center'>$paymentStatus</td>";
          echo "<td align='center'>$billingNo</td>";
          echo "<td align='center'>$billingDate</td>";
          echo "<td align='center'>$billingAddress</td>";
     
          $pdfFile = "pdf/individualBilling/".$billingNo.".pdf";
          if(file_exists($pdfFile)){
             echo "<td><a href='pdf/individualBilling/".$billingNo.".pdf' download='IIAP_MembershipBilling_".$billingNo."' title='Click to download pdf file'><img src='images/pdf_download.jpg' width='40' height='40'></td>";
          }
           
          else{
       //     echo "<td><img src='images/not_available_download.png' width='40' height='40'></td>";
             echo "<td><a href='pdf/individualBilling/generatePDFIndividualBilling.php?billingRef=$billingNo&eventId=$eventId&user=$userId' title='Click to generate pdf'><img src='images/pdf_me.png' width='50' height='50'> </a></td>";
          }
       }

       elseif($isBillGenerated == 0){
          echo "<input type='checkbox' name='participantIds[]' value='$participantId'>";
          echo "</td>";
          echo "<td align='center'><input type='checkbox' name='sendIds[]' value='$contact_id' disabled></td>";
          echo "<td align='center'><input type='checkbox' name='postIds[]' value='$contact_id' disabled></td>";
          echo "<td align='center'>Pay Later</td>";
          echo "<td align='center'>Number</td>";
          echo "<td align='center'>Date</td>";
          echo "<td align='center'>Address</td>";
          echo "<td><img src='images/not_available_download.png' width='40' height='40'></td>";

       }
          echo "<tr>";
      
    }    
   
    echo "</table>";
    echo "</form>";

  if(isset($_POST["processType"])){
      $processType = $_POST["processType"];

      if($processType == 'Generate Bill'){
         @$participantsSelected = $_POST['participantIds'];
   
      foreach($participantsSelected as $participant_id){
        $contact = $participants[$participant_id];
        $contact_id = $contact["contact_id"];
        $fee_amount = $contact["fee_amount"];
        if($eventTypeName == 'CON'){
           $subtotal = $fee_amount;
           $tax = 0.0;
         }

        else{
           $tax = round($fee_amount/9.3333,2);
           $subtotal = round($fee_amount - $tax,2);

        }

        $contactDetails = getContactDetails($dbh, $contact_id);
        $participant_name = $contactDetails["name"];
        $email = getContactEmail($dbh,$contact_id);
        $status = getStatusType($dbh,$participantId);
        $billingAddress = getContactAddress($dbh,$contact_id);
        $organization_name = $contactDetails["companyName"];
        $orgId = $orgs[$organization_name];
        $participantBillingType = $billingType[$participant_id];
        $eventTypeName = getEventTypeName($dbh,$eventId);
        $billingId = "";
        $billingNo = $eventTypeName.$billingId.$participant_id;

        $sql = $dbh->prepare("INSERT INTO billing_details
                         (participant_id,contact_id,event_id,event_type,event_name,participant_name,email,participant_status,organization_name,org_contact_id,billing_type,fee_amount,subtotal,vat,billing_no,bill_address)
                        VALUES('$participant_id','$contact_id','$eventId','$eventTypeName','$eventName','$participant_name','$email','$status','$organization_name','$orgId','$participantBillingType','$fee_amount','$subtotal','$tax','$billingNo','$billingAddress')");

       $sql->execute();

      }

     }

     elseif($processType == 'Send Bill'){

         $ids = $_POST["sendIds"];

         foreach($ids as $contactId){
           updateSendBill($dbh,$contactId,$eventId);
         }

      }

     elseif($processType == 'Post to Weberp'){
        $ids = $_POST["postIds"];

        $customerDetails = array();

        foreach($ids as $contactId){
          updateBillPosting($dbh,$contactId,$eventId);
          updatePaidBill($dbh,$contactId,$eventId);
          $addressDetails = getAddressDetails($dbh,$contactId);
          $street = $addressDetails["street"];
          $city = $addressDetails["city"];
          $memberId = getMemberId($dbh,$contactId);
          $name = getCustomerName($dbh,$contactId,$eventId);
          $email = getContactEmail($dbh,$contactId);
          $amount = getCustomerBillingAmount($dbh,$contactId,$eventId);

          $customerDetails["contact_id"] = $contactId;
          $customerDetails["participant_name"] = $name;
          $customerDetails["street"] = $street;
          $customerDetails["city"] = $city;
          $customerDetails["member_id"] = $memberId;
          $customerDetails["email"] = $email;

          insertCustomer($weberpConn,$customerDetails);
          unset($customerDetails);
          $customerDetails = array();
          myPost($eventTypeName,$eventName,$amount,$name);
 
        }
     }
   }//end if of individual Process Type

?>
</body>
</html>
