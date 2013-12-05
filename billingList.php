<html>
<head>
<title>Billing List</title>
<link rel="stylesheet" type="text/css" href="billingStyle.css">
</head>
<body>
<?php

  include 'dbcon.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'billing_functions.php';
  include '../weberpdev/postFunction.php';
  include 'send_functions.php';

   $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin');
   $weberpConn = new PDO('mysql:host=10.110.215.92;dbname=IIAP_DEV','iiap','mysqladmin');
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
   echo "<a href='events2.php'><b>Event List</b></a>";
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
<b>Select billing Type:</b>&nbsp;
<form name="bill" method="Post">
	<select name="billingType">
    <option value="select">Select billing type</option>
  	<option value="Individual">Individual</option>
  	<option value="Company">Company</option>
	</select>
  <input type="submit" value="View Billing">
</form>



<?php   
   
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
   $companyBillingTypes = $eventBillingTypes["Company"];
   $companyBillAmounts = array();

//-------------------FOR COMPANY BILLING------------------------------------------------------------------------------------
   		$participantPerCompanyBill = array();
   		$participantDetails = array();
   		$details = array();
   		$totalAmount = array();

   		foreach($companyBillingTypes as $participantId){
     		
				if(!$participantPerCompanyBill){
       		$contact = $participants[$participantId];
       		$contact_id = $contact["contact_id"];
          $contact_address = getContactAddress($dbh,$contact_id);
       		$fee_amount = $contact["fee_amount"];
       		$contactDetails = getContactDetails($dbh, $contact_id);
       		$participant_name = $contactDetails["name"];
          $email = getContactEmail($dbh,$contact_id);
       		$organization_name = $contactDetails["companyName"];
       		$orgId = $orgs[$organization_name];
//        $participantBillingType = $billingType[$participant_id];
       		$billingId = "";
       		$billingNo = $eventTypeName.$billingId.$participantId;
          $status = getStatusType($dbh,$participantId);

       		$details["participant_id"] = $participantId;
       		$details["event_id"] = $eventId;
          $details["event_name"] = $eventName;
       		$details["participant_name"] = $participant_name;
          $details["email"] = $email;
          $details["bill_address"] = $contact_address;
       		$details["organization_name"] = $organization_name;
       		$details["org_contact_id"] = $orgId;
       		$details["billing_type"] = 'Company';
       		$details["fee_amount"] = $fee_amount;
       		$details["billingNo"] = $billingNo;
          $details["status"] = $status;

       		$participantDetails[$participantId] = $details;
       
       		$participantPerCompanyBill[$orgId] = $participantDetails;

       		unset($details);
       		unset($participantDetails);
      }

     else{
       $contact = $participants[$participantId];
       $contact_id = $contact["contact_id"];
       $fee_amount = $contact["fee_amount"];
       $contactDetails = getContactDetails($dbh, $contact_id);
       $participant_name = $contactDetails["name"];
       $email = getContactEmail($dbh,$contact_id);
       $contact_address = getContactAddress($dbh,$contact_id);
       $organization_name = $contactDetails["companyName"];
       $orgId = $orgs[$organization_name];
//       $participantBillingType = $billingType[$participant_id];
       $billingId = "";
       $billingNo = $eventTypeName.$billingId.$participantId;
       $status = getStatusType($dbh,$participantId);

       $details["participant_id"] = $participantId;
       $details["event_id"] = $eventId;
       $details["event_name"] = $eventName;
       $details["participant_name"] = $participant_name;
       $details["email"] = $email;
       $details["bill_address"] = $contact_address;
       $details["organization_name"] = $organization_name;
       $details["org_contact_id"] = $orgId;
       $details["billing_type"] = 'Company';
       $details["fee_amount"] = $fee_amount;
       $details["billingNo"] = $billingNo;
       $details["status"] = $status;
       

       $participantDetails[$participantId] = $details;

       	if(array_key_exists($orgId,$participantPerCompanyBill)){
          array_push($participantPerCompanyBill[$orgId],$details);
       	}
      
       	else{
         $participantPerCompanyBill[$orgId] = $participantDetails;
       	}

       unset($details);
       unset($participantDetails);
      
     }
   }//end foreach

 //-------------------------------------FOR COMPANY BILLING-------------------------------------------------------------------------------


  if(isset($_POST["billingType"])){

   
   if($_POST["billingType"] == 'Individual'){
?>
<b>Select action to process:</b>&nbsp;
<form name="process" method="Post" action="billingList.php?eventId=<?=$eventId?>">
  <select name="processType">
    <option value="select">Select ation type</option>
    <option value="Generate Bill">Generate Bill</option>
    <option value="Send Bill">Send Bill</option>
    <option value="Post to Weberp">Post to Weberp</option>
  </select>
  <input type="submit" value="Process Action" name="processAction">
<?

   echo "<br><br><br>";
   echo "<table border='1'>";
   echo "<tr><th colspan='14'>Individual Billing</th></tr>";
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
          //echo "<input type='checkbox' name='participantIds[]' value='$participantId' disabled>";
          //echo "<button type='button'><a href='individualBillingReference.php?billingRef=$billingNo&eventId=$eventId' style='text-decoration:none;' target ='_blank'>View Bill</a></button>";
          echo "<a href='individualBillingReference.php?billingRef=$billingNo&eventId=$eventId' style='text-decoration:none;' target ='_blank'><img src='printer-icon.png' width='50' height='50'>";
          echo "<br>Print</a>";
          echo "</td>";
          //echo "<td align='center'><input type='checkbox' name='sendIds[]' value='$contact_id'>";
          echo "<td align='center'>";
          echo "<a href='sendIndividualBilling.php?billingRef=$billingNo&eventId=$eventId' style='text-decoration:none;' target ='_blank'><img src='email.jpg' width='50' height='50'>";
          echo "<br>Email</a>";
          echo "</td>";
          
          /*$isPost = isPost(PDO $dbh,$participantId,$eventId);
          var_dump($isPost);**/

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
       }
          echo "<tr>";
      
    }    
   
    echo "</table>";
    echo "</form>";
   }

   elseif($_POST["billingType"] == 'Company'){
    
      //$companyTotalBill = array();
      foreach($participantPerCompanyBill as $orgIdKey => $value){

         $updateValue = $value;
         $totalAmount = 0;
         $amountPerParticipant = 0;
         foreach($value as $billingDetails){
            $amountPerParticipant = $amountPerParticipant + $billingDetails["fee_amount"];
         }
         $totalAmount = $amountPerParticipant;
         $companyBillAmounts[$orgIdKey] = $totalAmount; 
      }

      echo "<b>Select action to process:</b>";
      echo "<form name='companyBill' method='Post'>";
      echo "<select name='companyProcessType'>";
      echo "<option value='select'>Select action type</option>";
      echo "<option value='Generate Bill'>Generate Bill</option>";
      echo "<option value='Send Bill'>Send Bill</option>";
      echo "<option value='Post to Weberp'>Post to Weberp</option>";
      echo "</select>";
      echo "<input type='submit' name='process' value='Process Action'>";

      echo "<br><br>";
      echo "<table border='1'>";
      echo "<tr><th colspan='11'>Company Billing</th></tr>";
      echo "<tr>";
      echo "<th>Organization Name</th>";
      echo "<th>Total Billing Amount</th>";
      echo "<th>Subtotal</th>";
      echo "<th>12% VAT</th>";
      echo "<th>Generate Bill</th>";
      echo "<th>Send Bill</th>";
      echo "<th>Post Bill</th>";
      echo "<th>Payment Status</th>";
      echo "<th>Billing Reference No.</th>";
      echo "<th>Billing Date</th>";
      echo "<th>Billing Address</th>";
      echo "</tr>";

      foreach($participantPerCompanyBill as $orgIdKey => $participant){
         
  			$companyId = $orgIdKey;
  			$organization_name = array_search($companyId,$orgs);
        $totalBill = $companyBillAmounts[$companyId];

        if($eventTypeName == 'CON'){
           $subtotal = $totalBill;
           $tax = 0.0;
         }

        else{
           $tax = round($totalBill/9.3333,2);
           $subtotal = round($totalBill - $tax,2);;

        }
        $totalBill = number_format($totalBill, 2, '.', '');
        echo "<tr>";
        echo "<td>$organization_name</td>";
        echo "<td align='center'>$totalBill</td>";
        echo "<td align='center'>$subtotal</td>";
        echo "<td align='center'>$tax</td>";

        $isCompanyBillGenerated = checkCompanyBillGenerated($dbh,$companyId,$eventId);   
        if($isCompanyBillGenerated == 1){
  
          $companyBillingRefNo = getCompanyBillingNo($dbh,$companyId,$eventId);
          $companyBillingDate = getCompanyBillingDate($dbh,$companyId,$eventId);
          $companyBillingAddress = "";

        	//echo "<td align='center'><input type='checkbox' name='companyId[]' value='$companyId'></td>";
          //echo "<td><button type='button'><a href='companyBillingReference.php?companyBillingRef=$companyBillingRefNo&eventId=$eventId&orgId=$companyId' style='text-decoration:none'>View Company Bill</a></button></td>";
          echo "<td align='center'><a href='companyBillingReference.php?companyBillingRef=$companyBillingRefNo&eventId=$eventId&orgId=$companyId' style='text-decoration:none' target='_blank'>";
          echo "<img src='printer-icon.png' height='50' width='50'><br>Print</a></td>";
          echo "<td align='center'><a href='sendIndividualBilling.php?companyBillingRef=$companyBillingRefNo&eventId=$eventId&orgId=$companyId' style='text-decoration:none' target='_blank'>";
          echo "<img src='email.jpg' height='50' width='50'><br>Email</a></td>";
        	//echo "<td align='center'><input type='checkbox' name='companyIds2[]' value='$companyId'></td>";
          echo "<td align='center'><input type='checkbox' name='postIds[]' value='$companyId'>djajfdljalj</td>";
          echo "<td align='center'></td>";
          echo "<td align='center'>$companyBillingRefNo</td>";
          echo "<td align='center'>$companyBillingDate</td>";
          echo "<td align='center'>$companyBillingAddress</td>";
        }

        elseif($isCompanyBillGenerated == 0){
         
        	echo "<td align='center'><input type='checkbox' name='companyIds[]' value='$companyId'></td>";
        	echo "<td align='center'><input type='checkbox' name='companyIds2[]' value='$companyId' disabled></td>";
          echo "<td align='center'><input type='checkbox' name='postIds[]' value='$companyId' disabled></td>";
          echo "<td align='center'></td>";
          echo "<td></td>";
          echo "<td></td>";
        }
  			/**$billedParticipants = $participantPerCompanyBill[$companyId];
  			$sqlMaxBillingId = $dbh->prepare("SELECT MAX(cbid) as prevBillingId FROM billing_company");
  			$sqlMaxBillingId->execute();
  			$maxBillingId = $sqlMaxBillingId->fetch(PDO::FETCH_ASSOC);
  			$companyBillingNo = $maxBillingId["prevBillingId"] + 1;
  			$companyBillingNo = $companyBillingNo.$companyId;
        $totalAmount = $companyTotalBill[$orgIdKey];

  			$sqlInsertCompanyBilling = $dbh->prepare("INSERT INTO billing_company
                                            (event_id,org_contact_id,organization_name,billing_no,total_amount)
                                           VALUES('$eventId','$companyId','$organization_name','$companyBillingNo','$totalAmount')
                                           ");  
  			$sqlInsertCompanyBilling->execute();**/
        echo "</tr>";

      }
      echo "</form>";
      echo "</table>";


      

   }//end if billing Type is Company


   }//endif of billingType



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

   elseif(isset($_POST["companyProcessType"])){
     $companyProcessType = $_POST["companyProcessType"];

      
     if($companyProcessType == 'Generate Bill'){
       $companiesSelected = $_POST["companyIds"];
      
       foreach($companiesSelected as $companyId){
        		$billedParticipants = $participantPerCompanyBill[$companyId];

  			    $organization_name = array_search($companyId,$orgs);
  			    $sqlMaxBillingId = $dbh->prepare("SELECT MAX(cbid) as prevBillingId FROM billing_company");
  			    $sqlMaxBillingId->execute();
  			    $maxBillingId = $sqlMaxBillingId->fetch(PDO::FETCH_ASSOC);
            $eventTypeName = getEventTypeName($dbh,$eventId);
  			    $companyBillingNo = $maxBillingId["prevBillingId"] + 1;
  			    $companyBillingNo = $eventTypeName.$companyBillingNo.$companyId;
            

  			    $sqlInsertCompanyBilling = $dbh->prepare("INSERT INTO billing_company
                                            (event_id,event_name,org_contact_id,organization_name,billing_no)
                                           VALUES('$eventId', '$eventName','$companyId','$organization_name','$companyBillingNo')
                                           ");  
  			    $sqlInsertCompanyBilling->execute();
            $companyBillTotalAmount = 0;
        
   					foreach($billedParticipants as $participant => $billDetails){

           			$participant_id = $billDetails["participant_id"];
                $contactId = getParticipantContactId($dbh,$participant_id,$eventId);
                $email = getContactEmail($dbh,$contactId);
           			$participant_name = $billDetails["participant_name"];
           			$organization_name = $billDetails["organization_name"];
           			$orgId = $companyId;
           			$participantBillingType = $billDetails["billing_type"];
           			$fee_amount = $billDetails["fee_amount"];
           			$billingNo = $companyBillingNo;
                $status = getStatusType($dbh,$participant_id);
   
        				$sql = $dbh->prepare("INSERT INTO billing_details
                         (participant_id,contact_id,event_id,event_type,event_name,participant_name,email,participant_status,organization_name,org_contact_id,billing_type,fee_amount,billing_no)
                        VALUES('$participant_id','$contactId','$eventId','$eventTypeName','$eventName','$participant_name','$email','$status','$organization_name','$orgId','$participantBillingType','$fee_amount','$billingNo')");

        				$sql->execute();

        				$companyBillTotalAmount = $companyBillTotalAmount + $fee_amount;
             }
             
              if($eventTypeName == 'CON'){
                   $subtotal = $totalBill;
                   $tax = 0.0;
              }

              else{
                $tax = round($companyBillTotalAmount/9.3333,2);
                $subtotal = round($companyBillTotalAmount - $tax,2);;

             }

       			$sqlUpdateTotalAmount = $dbh->prepare("UPDATE billing_company
                                         SET total_amount = '$companyBillTotalAmount', subtotal = '$subtotal', vat = '$tax'
                                         WHERE event_id = '$eventId'
                                         AND  billing_no = '$billingNo'
                                         AND org_contact_id = '$orgId'
                                        ");

        		$sqlUpdateTotalAmount->execute();
       }

     }//end if Generate Bill


     elseif($companyProcessType == 'Post to Weberp'){

       $cids = $_POST["postIds"];
       $companyDetails = array();
       foreach($cids as $companyId){
          $orgName = array_search($companyId,$orgs);
          $street = "";
          $city = "";
          $companyDetails["contact_id"] = $companyId;
          $companyDetails["company_name"] = $orgName;
          $companyDetails["street"] = $street;
          $companyDetails["city"] = $city;
          $amount = getCompanyBillingAmount($dbh,$companyId,$eventId);
          insertCompanyCustomer($weberpConn,$companyDetails);
          unset($companyDetails);
          $companyDetails = array();
          myPost($eventTypeName,$eventName,$amount,$orgName);
       }
       


     }//end if Post to Weberp
   }//end elseif company processor type

   //$contactDetails = getContactDetails($dbh, "8677");






   /**Generate Billing**/
   //$participant_id = "3154";

/**---
   foreach($individualBillingTypes as $participant_id){
   $contact = $participants[$participant_id];
   $contact_id = $contact["contact_id"];
   $fee_amount = $contact["fee_amount"];

   $contactDetails = getContactDetails($dbh, $contact_id);
   $participant_name = $contactDetails["name"];
   $organization_name = $contactDetails["companyName"];
   $orgId = $orgs[$organization_name];
   $participantBillingType = $billingType[$participant_id];
   $billingId = "";
   $billingNo = $billingId.$participant_id;**/

   /**INSERT INDIVIDUAL BILLING**/
   
/**
   $sql = $dbh->prepare("INSERT INTO billing_details
                         (participant_id,event_id,participant_name,organization_name,org_contact_id,billing_type,fee_amount,billing_no)
                        VALUES('$participant_id','$eventId','$participant_name','$organization_name','$orgId','$participantBillingType','$fee_amount','$billingNo')");

   $sql->execute();

   }
----end of individual billing**/

  /**INSERT COMPANY BILLING**/

/**

  $companyId = "8781";
  $organization_name = array_search($companyId,$orgs);
  $billedParticipants = $participantPerCompanyBill["8781"];
  $sqlMaxBillingId = $dbh->prepare("SELECT MAX(cbid) as prevBillingId FROM billing_company");
  $sqlMaxBillingId->execute();
  $maxBillingId = $sqlMaxBillingId->fetch(PDO::FETCH_ASSOC);
  $companyBillingNo = $maxBillingId["prevBillingId"] + 1;
  $companyBillingNo = $companyBillingNo.$companyId;

  $sqlInsertCompanyBilling = $dbh->prepare("INSERT INTO billing_company
                                            (event_id,org_contact_id,organization_name,billing_no)
                                           VALUES('$eventId','$companyId','$organization_name','$companyBillingNo')
                                           ");  
  $sqlInsertCompanyBilling->execute();

  
  $companyBillTotalAmount = 0;
  foreach($billedParticipants as $participant => $billDetails){

   $participant_id = $billDetails["participant_id"];
   $participant_name = $billDetails["participant_name"];
   $organization_name = $billDetails["organization_name"];
   $orgId = $companyId;
   $participantBillingType = $billDetails["billing_type"];
   $fee_amount = $billDetails["fee_amount"];
   $billingNo = $companyBillingNo;
   
   
   $sql = $dbh->prepare("INSERT INTO billing_details
                         (participant_id,event_id,participant_name,organization_name,org_contact_id,billing_type,fee_amount,billing_no)
                        VALUES('$participant_id','$eventId','$participant_name','$organization_name','$orgId','$participantBillingType','$fee_amount','$billingNo')");

   $sql->execute();

   $companyBillTotalAmount = $companyBillTotalAmount + $fee_amount;
  }

 $sqlUpdateTotalAmount = $dbh->prepare("UPDATE billing_company
                                         SET total_amount = '$companyBillTotalAmount'
                                         WHERE event_id = '$eventId'
                                         AND  billing_no = '$billingNo'
                                         AND org_contact_id = '$orgId'");

                           
 $sqlUpdateTotalAmount->execute();**/
?>
</body>
</html>
