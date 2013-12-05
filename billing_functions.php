<?php

/*
 *get the created table of a custom field in civicrm
 *get custom group details
 */
function getCustomGroupDetails(PDO $dbh, $groupName){

   $sql = $dbh->prepare("SELECT id, table_name, name FROM civicrm_custom_group
                          WHERE title = ?
                         ");
    $sql->bindParam(1,$groupName,PDO::PARAM_STR,300);
    $sql->execute();
    $customGroupDetails = $sql->fetch(PDO::FETCH_ASSOC);

    return $customGroupDetails;

}

/*
 *get the column name where records of custom field data in civicrm are stored
 */
function getColumnNameStoredValues(PDO $dbh, $customGroupId){

   $sql = $dbh->prepare("SELECT column_name FROM civicrm_custom_field
                         WHERE custom_group_id = ?
                        ");
   $sql->bindParam(1, $customGroupId, PDO::PARAM_INT);
   $sql->execute();
   $columnName = $sql->fetch(PDO::FETCH_ASSOC);

   return $columnName;
}

/*
 *get the billing types of each participant
 */
function getTypeOfBilling(PDO $dbh, $tableName, $columnName){

  $sql = $dbh->prepare("SELECT entity_id, $columnName FROM $tableName");
  $sql->execute();
  $billingType = $sql->fetchAll(PDO::FETCH_ASSOC);

  $participantBillingTypes = array();

  foreach($billingType as $type){

    $participantId = $type["entity_id"];
    $billing = $type[$columnName];

    $participantBillingTypes[$participantId] = $billing;
  }

  return $participantBillingTypes;
}

function getOrganization($dbh){

  $sql = $dbh->prepare("SELECT id as orgId, display_name as companyName
                        FROM civicrm_contact
                        WHERE contact_type = 'Organization'");
  $sql->execute();
  $org = $sql->fetchAll(PDO::FETCH_ASSOC);

  $companies = array();

  foreach($org as $company){
     
     $id = $company["orgId"];
     $companyName = $company["companyName"];
     $companies[$companyName] = $id;
  }

  return $companies;
}

/*
 *get participant id and contact id
 */
function getEventParticipantIds(PDO $dbh, $eventId){

  $sql = $dbh->prepare("SELECT id as participant_id, contact_id, fee_amount 
                        FROM civicrm_participant
                        WHERE event_id = ? ");
  $sql->bindParam(1, $eventId, PDO::PARAM_INT);
  $sql->execute();
  $participantIds = $sql->fetchAll(PDO::FETCH_ASSOC);

  $participants = array();
  $details = array();

  foreach($participantIds as $participant){

    $details["contact_id"] = $participant["contact_id"];
    $details["fee_amount"] = $participant["fee_amount"]; 

    $participant_id = $participant["participant_id"];
    $participants[$participant_id] = $details;
    
    unset($details);
  }

  return $participants;
}

function getContactDetails(PDO $dbh, $contactId){

  $sql = $dbh->prepare("SELECT display_name as name, organization_name as companyName 
                        FROM civicrm_contact
                        WHERE id = ? ");
  $sql->bindParam(1, $contactId, PDO::PARAM_INT);
  $sql->execute();
  $contactDetails = $sql->fetch(PDO::FETCH_ASSOC);

  return $contactDetails;
}

function getParticipantsBillingType($billingType,$participants){

  $individual = array();
  $company = array();

  foreach($participants as $participant => $details){
    $participant_id = $participant;
    
    if($billingType[$participant_id] == 'Individual'){
      $individual[] = $participant_id;
    }

    else{
      $company[] = $participant_id;
    }
  }

  $eventBillingTypes = array();
  $eventBillingTypes["Individual"] = $individual;
  $eventBillingTypes["Company"] = $company;

  return $eventBillingTypes;
}

function checkBillGenerated(PDO $dbh,$participantId,$eventId){

  $sql = $dbh->prepare("SELECT count(*) as exist FROM billing_details
                        WHERE participant_id = '$participantId'
                        AND event_id = '$eventId'
                       ");
  $sql->execute();
  $sqlCount = $sql->fetch(PDO::FETCH_ASSOC);
  
  return $sqlCount["exist"];


}

function getIndividualBillingNo(PDO $dbh, $participantId,$eventId){

  $sql = $dbh->prepare("SELECT billing_no FROM billing_details
                        WHERE participant_id = '$participantId'
                        AND event_id = '$eventId'
                       ");
  $sql->execute();
  $billing = $sql->fetch(PDO::FETCH_ASSOC);

  return $billing["billing_no"];
}

function getIndividualBillingDate(PDO $dbh, $participantId, $eventId){

  $sql = $dbh->prepare("SELECT bill_date FROM billing_details
                        WHERE participant_id = '$participantId'
                        AND event_id = '$eventId'
                       ");

  $sql->execute();
  $billing = $sql->fetch(PDO::FETCH_ASSOC);

  $billingDate = $billing["bill_date"];
  $billingDate = date("Y-m-d",strtotime($billingDate));

  return $billingDate;
}

function getIndividualBillingAddress(PDO $dbh, $participantId, $eventId){

  $sql = $dbh->prepare("SELECT bill_address  FROM billing_details
                        WHERE participant_id = '$participantId'
                        AND event_id = '$eventId'
                       ");
  $sql->execute();
  $billing = $sql->fetch(PDO::FETCH_ASSOC);

  $billingAddress = $billing["bill_address"];
  
  return $billingAddress;
}

function checkCompanyBillGenerated(PDO $dbh,$orgContactId,$eventId){

  $sql = $dbh->prepare("SELECT count(*) as exist FROM billing_company
                        WHERE org_contact_id = '$orgContactId'
                        AND event_id = '$eventId'
                      ");
  $sql->execute();
  $sqlCount = $sql->fetch(PDO::FETCH_ASSOC);

  return $sqlCount["exist"];
}

function getCompanyBillingNo(PDO $dbh,$orgContactId,$eventId){

  $sql = $dbh->prepare("SELECT billing_no FROM billing_company
                        WHERE org_contact_id = '$orgContactId'
                        AND event_id = '$eventId'
                      ");
  $sql->execute();
  $billing = $sql->fetch(PDO::FETCH_ASSOC);
  
  return $billing["billing_no"];
}

function getCompanyBillingDate(PDO $dbh, $orgContactId, $eventId){

  $sql = $dbh->prepare("SELECT bill_date FROM billing_company
                        WHERE org_contact_id = '$orgContactId'
                        AND event_id = '$eventId'
                       ");
  $sql->execute();
  $billing = $sql->fetch(PDO::FETCH_ASSOC);

  $billingDate = $billing["bill_date"];
  $billingDate = date("Y-m-d",strtotime($billingDate));

  return $billingDate;

}

function getEventDetails(PDO $dbh,$eventId){

  $sql = $dbh->prepare("SELECT title,start_date,end_date
                        FROM civicrm_event
                        WHERE id = '$eventId'
                       ");

 $sql->execute();
 $event = $sql->fetch(PDO::FETCH_ASSOC);

 $eventDetails = array();

 $startDate = date("j M Y", strtotime($event["start_date"]));
 $endDate = date("j M Y", strtotime($event["end_date"]));

 $eventDetails["event_name"] = $event["title"];
 $eventDetails["start_date"] = $startDate;
 $eventDetails["end_date"] = $endDate;

 return $eventDetails;
}

function getIndividualBillingDetails(PDO $dbh, $billingNo, $eventId){

  try{

  $sql = $dbh->prepare("SELECT contact_id,participant_id,participant_name, organization_name, fee_amount, billing_no, bill_date, bill_address,subtotal,vat
                        FROM billing_details
                        WHERE billing_no = '$billingNo' AND event_id = '$eventId'
                       ");

  $sql->execute();
  $billingDetails = $sql->fetch(PDO::FETCH_ASSOC);

  }

  catch (Exception $e) {
    echo 'Error: '.$e->getMessage();
  }

  return $billingDetails;

}

function getCompanyBillingDetails(PDO $dbh, $billingNo, $eventId){

  $sql = $dbh->prepare("SELECT event_id, org_contact_id, organization_name, billing_no, total_amount, bill_date, subtotal, vat
                        FROM billing_company
                        WHERE billing_no = '$billingNo' AND event_id = '$eventId'
                       ");
  $sql->execute();
  $companyBillingDetails = $sql->fetch(PDO::FETCH_ASSOC);

  return $companyBillingDetails;
}

function getCompanyBillingParticipants(PDO $dbh, $billingNo, $eventId){

  $sql = $dbh->prepare("SELECT participant_id, event_id, participant_name, organization_name, org_contact_id, billing_type, fee_amount, billing_no, bill_date
                        FROM billing_details
                        WHERE billing_no = '$billingNo'
                        AND event_id = '$eventId'
                        AND billing_type = 'Company'
                       ");
  $sql->execute();
  $billingParticipants = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $billingParticipants;
}

function getParticipantContactId(PDO $dbh, $participantId, $eventId){

  $sql = $dbh->prepare("SELECT contact_id FROM civicrm_participant
                        WHERE id = '$participantId'
                        AND event_id = '$eventId';
                       ");
  $sql->execute();
  $contacts = $sql->fetch(PDO::FETCH_ASSOC);

  $contactId = $contacts["contact_id"];

  return $contactId;
}

/**
get the created table name for custom fields in civicrm
**/
function getTableName(PDO $dbh, $customFieldTitle){

  $sql = $dbh->prepare("SELECT title, name, table_name
                        FROM civicrm_custom_group
                        WHERE title = '$customFieldTitle'
                       ");
  $sql->execute();
  $table = $sql->fetch(PDO::FETCH_ASSOC);

  $tableName = $table["table_name"];

  return $tableName;
}

/**
get the custom group id for the custom field
**/

function getCustomGroupId(PDO $dbh, $customFieldTitle){
  
  $sql = $dbh->prepare("SELECT id FROM civicrm_custom_group
                        WHERE title = '$customFieldTitle'
                       ");
  $sql->execute();
  $customField = $sql->fetch(PDO::FETCH_ASSOC);

  $customGroupId = $customField["id"];

  return $customGroupId;

}

/**
 * return the column name of the created table for the added custom field in civirm
 */

function getColumnName(PDO $dbh,$customGroupId,$customLabel){

  $sql = $dbh->prepare("SELECT column_name
                        FROM civicrm_custom_field
                        WHERE custom_group_id = '$customGroupId'
                        AND label = '$customLabel'
                       ");
  $sql->execute();
  $customField = $sql->fetch(PDO::FETCH_ASSOC);

  $columnName = $customField["column_name"];

  return $columnName;
}

/*
 * return address of individual contact
 * Format : Street, City
 */
function getContactAddress(PDO $dbh,$contactId){

  $tableName = getTableName($dbh,"Business Data");
  $customGroupId = getCustomGroupId($dbh,"Business Data");
  $streetColumnName = getColumnName($dbh, $customGroupId, "Street Address (Company)");
  $cityColumnName = getColumnName($dbh, $customGroupId, "City (Company)");

  $sql = $dbh->prepare("SELECT $streetColumnName, $cityColumnName
                       FROM $tableName WHERE entity_id = '$contactId'
                      ");
  $sql->execute();
  $field = $sql->fetch(PDO::FETCH_ASSOC);

  $streetName = ucfirst($field[$streetColumnName]);
  $columnName = ucfirst($field[$cityColumnName]);

  $address = $streetName." ".$columnName;

  return $address;
  
}

function getEventTypeId(PDO $dbh,$eventId){

  $sql = $dbh->prepare("SELECT event_type_id FROM civicrm_event WHERE id = '$eventId'");
  $sql->execute();
  $event = $sql->fetch(PDO::FETCH_ASSOC);
  $eventTypeId = $event["event_type_id"];

  return $eventTypeId;
}

function getOptionGroupId(PDO $dbh, $optionTitle){

   $sql = $dbh->prepare("SELECT id FROM civicrm_option_group WHERE title = '$optionTitle'");
   $sql->execute();
   $options = $sql->fetch(PDO::FETCH_ASSOC);
   $optionId = $options["id"];

   return $optionId;
}

function getEventTypeName(PDO $dbh,$eventId){

  $eventTypeId = getEventTypeId($dbh,$eventId);
  $eventTypeOptionId = getOptionGroupId($dbh,"Event Type");

  $sql = $dbh->prepare("SELECT label FROM civicrm_option_value
                        WHERE value = '$eventTypeId'
                        AND option_group_id = '$eventTypeOptionId'");
  $sql->execute();
  $eventType = $sql->fetch(PDO::FETCH_ASSOC);
  $eventTypeName = $eventType["label"];

  return $eventTypeName;
  
}

function getContactEmail(PDO $dbh,$contactId){

  $sql = $dbh->prepare("SELECT email FROM civicrm_email WHERE contact_id = '$contactId'");
  $sql->execute();
  $contactEmail = $sql->fetch(PDO::FETCH_ASSOC);
  $email = $contactEmail["email"];

  return $email;
}

function getParticipantIdStatus(PDO $dbh, $participantId){

  $sql = $dbh->prepare("SELECT status_id FROM civicrm_participant WHERE id = '$participantId'");
  $sql->execute();
  $participant = $sql->fetch(PDO::FETCH_ASSOC);
  $statusId = $participant["status_id"];

  return $statusId;
}

function getStatusType(PDO $dbh, $participantId){

  $statusId = getParticipantIdStatus($dbh, $participantId);
  $sql = $dbh->prepare("SELECT name FROM civicrm_participant_status_type
                        WHERE id = '$statusId'
                      ");
  $sql->execute();
  $status = $sql->fetch(PDO::FETCH_ASSOC);
  $statusType = $status["name"];

  return $statusType;
}

function isPost(PDO $dbh,$participantId,$eventId){

  $sql = $dbh->prepare("SELECT post_bill FROM billing_details
                        WHERE participant_id = '$participantId' AND event_id = '$eventId'
                       ");
  $sql->execute();
  $billing = $sql->fetch(PDO::FETCH_ASSOC);
  $post = $billing["post_bill"];

  return $post;
}

function updateBillPosting(PDO $dbh,$contactId,$eventId){

  $sql = $dbh->prepare("UPDATE billing_details
                        SET post_bill = '1'
                        WHERE contact_id = '$contactId'
                        AND event_id = '$eventId'
                       ");
  $sql->execute();
}

function updatePaidBill(PDO $dbh,$contactId,$eventId){

  $sql = $dbh->prepare("UPDATE
                        billing details
                        SET paid_bill = '1'
                        WHERE contact_id = '$contactId'
                        AND event_id = '$eventId'
                       ");
  $sql->execute();
}

function getAddressDetails(PDO $dbh,$contactId){

  $addressDetails = array();

  $tableName = getTableName($dbh,"Business Data");
  $customGroupId = getCustomGroupId($dbh,"Business Data");
  $streetColumnName = getColumnName($dbh, $customGroupId, "Street Address (Company)");
  $cityColumnName = getColumnName($dbh, $customGroupId, "City (Company)");

  $sql = $dbh->prepare("SELECT $streetColumnName, $cityColumnName
                       FROM $tableName WHERE entity_id = '$contactId'
                      ");
  $sql->execute();
  $field = $sql->fetch(PDO::FETCH_ASSOC);

  $addressDetails["street"] = ucfirst($field[$streetColumnName]);
  $addressDetails["city"] = ucfirst($field[$cityColumnName]);

  return $addressDetails;


}

function getCustomerName(PDO $dbh,$contactId,$eventId){

  $sql = $dbh->prepare("SELECT participant_name FROM billing_details WHERE contact_id = '$contactId' AND event_id = '$eventId'");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $name = $result["participant_name"];
  
  return $name;

}

function getMemberId(PDO $dbh,$contactId){

  $tableName = getTableName($dbh,"Member ID");
  $customGroupId = getCustomGroupId($dbh,"Member ID");
  $memberIdColumn = getColumnName($dbh, $customGroupId, "Member ID");

  $sql = $dbh->prepare("SELECT $memberIdColumn FROM $tableName WHERE entity_id = '$contactId'");
  $sql->execute();
  $field = $sql->fetch(PDO::FETCH_ASSOC);

  $memberId = $field[$memberIdColumn];

  return $memberId;
}

function insertCustomer(PDO $weberpConn,array $customerDetails){

  $contactId = $customerDetails["contact_id"];
  $debtorno = "IIAP".$contactId;
  $name = $customerDetails["participant_name"];
  $street = $customerDetails["street"];
  $city = $customerDetails["city"];
  $memberId = $customerDetails["member_id"];
  $email = $customerDetails["email"];
  $dateToday = date("Y-m-d");
 

  $sqlDebtor = $weberpConn->prepare("INSERT INTO debtorsmaster
                               (debtorno,name,address1,address3,address6,currcode,clientsince,holdreason,paymentterms,discount,creditlimit,salestype,invaddrbranch,customerpoline,typeid,memberid)
                               VALUES ('$debtorno','$name','$street','$city','Philippines','PHP','$dateToday','1','CA','0','1000','02','1','0','1','$memberId')
                              ");
  $sqlDebtor->execute();

  $sqlBranch = $weberpConn->prepare("INSERT INTO custbranch
                                     (branchcode,debtorno,brname,braddress1,braddress3,braddress6,lat,lng,estdeliverydays,fwddate,salesman,area,defaultlocation,disabletrans,deliverblind,email)
                                     VALUES('$debtorno','$debtorno','$name','$street','$city','Philippines','0','0','0','0','745','001','MKT','0','1','$email')
                                    ");
 $sqlBranch->execute();

}

function insertCompanyCustomer(PDO $weberpConn, array $customerDetails){

  $contactId = $customerDetails["contact_id"];
  $debtorno = "IIAP".$contactId;
  $name = $customerDetails["company_name"];
  $street = $customerDetails["street"];
  $city = $customerDetails["city"];
  //$memberId = $customerDetails["member_id"];
  //$email = $customerDetails["email"];
  $dateToday = date("Y-m-d");
 

  $sqlDebtor = $weberpConn->prepare("INSERT INTO debtorsmaster
                               (debtorno,name,address1,address3,address6,currcode,clientsince,holdreason,paymentterms,discount,creditlimit,salestype,invaddrbranch,customerpoline,typeid)
                               VALUES ('$debtorno','$name','$street','$city','Philippines','PHP','$dateToday','1','CA','0','1000','02','1','0','1')
                              ");
  $sqlDebtor->execute();

  $sqlBranch = $weberpConn->prepare("INSERT INTO custbranch
                                     (branchcode,debtorno,brname,braddress1,braddress3,braddress6,lat,lng,estdeliverydays,fwddate,salesman,area,defaultlocation,disabletrans,deliverblind)
                                     VALUES('$debtorno','$debtorno','$name','$street','$city','Philippines','0','0','0','0','745','001','MKT','0','1')
                                    ");
 $sqlBranch->execute();

}

function getCustomerBillingAmount($dbh,$contactId,$eventId){

  $sql = $dbh->prepare("SELECT fee_amount FROM billing_details
                        WHERE contact_id = '$contactId'
                        AND event_id = '$eventId'
                      ");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $amount = $details["fee_amount"];

  return $amount;
}

function getCompanyBillingAmount($dbh,$orgId,$eventId){

  $sql = $dbh->prepare("SELECT total_amount FROM billing_company
                        WHERE org_contact_id = '$orgId' AND event_id = '$eventId'
                       ");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $amount = $details["total_amount"];

  return $amount;
}

function getPaymentStatus($dbh,$contactId,$eventId){

  $sql = $dbh->prepare("SELECT paid_bill FROM billing_details
                        WHERE contact_id = '$contactId' AND event_id = '$eventId'
                       ");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $status = $details["paid_bill"];

  if($status == 0){
     return "Pay Later";
  }

  else{
     return "Paid";
 }
}

/*
 *get location details of each event
 */
function getEventLocation($dbh,$eventId){

  $sql = $dbh->prepare("SELECT ca.street_address,ca.supplemental_address_1, ca.supplemental_address_2, ca.supplemental_address_3,
                        ca.city, ca.postal_code, csp.name as province, con.name AS country
                        FROM civicrm_event ce, civicrm_loc_block clb, civicrm_address ca, civicrm_state_province csp, civicrm_country con
                        WHERE ce.id = '$eventId'
                        AND ce.loc_block_id = clb.id
                        AND clb.address_id = ca.id
                        AND ca.state_province_id = csp.id
                        AND ca.country_id = con.id
                       ");
  $sql->execute();
  $location = $sql->fetch(PDO::FETCH_ASSOC);

  return $location;
}


function formatEventLocation($locationDetails){

  $location = "";

  foreach($locationDetails as $key => $value){

    if($value && $key!='country'){
      $location = $location.$value.",&nbsp;";
    }
    
    elseif($key == 'country'){
      $location = $location.$value;
    }
  }

  return $location;
}

/*
 *return a link for lookup of participants under a certain company billing
 */
function participantsLink($billingNo,$eventId){

  $link = "<a href=\"billedParticipants.php?eventId=$eventId&billingNo=$billingNo\""
        . "title='Click to view participants under this billing no.'"
        . "onclick=\"javascript:void window.open('billedParticipants.php?eventId=$eventId&billingNo=$billingNo','1384398816566','width=1000,height=900,toolbar=0,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');"
        . "return false;\">"
        . "<img src='participants.png' height='50' width='50'>"
        . "</a>"; 

  return $link;

}

/*
 *return participants in a company billing no.
 */
function getCompanyBilledParticipants(PDO $dbh,$billingNo,$eventId){

  $sql = $dbh->prepare("SELECT participant_id, participant_name, email, fee_amount
                        FROM billing_details
                        WHERE billing_no = :billingNo AND event_id = :eventId
                       ");

  $sql->execute(array(':billingNo'=>$billingNo,':eventId'=>$eventId));
  $billedParticipants = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $billedParticipants;

}

/*
 *$billedParticipants is the result from getCompanyBilledParticipants() function
 */

function displayBilledParticipants($billedParticipants){

  $html = "<table border='1' width='100%'>"
        . "<tr>"
        . "<th colspan='4'>LIST OF BILLED PARTICIPANTS</th>"
        . "</tr>"
        . "<tr>"
        . "<th>Participant Id</th>"
        . "<th>Participant Name</th>"
        . "<th>Email</th>"
        . "<th>Fee Amount</th>"
        . "</tr>";

  foreach($billedParticipants as $key => $details){
     $participantId = $details["participant_id"];
     $name = $details["participant_name"];
     $email = $details["email"];
     $amount = $details["fee_amount"];
     $amount = number_format($amount,2);

     $html = $html."<tr>"
           . "<td>$participantId</td>"
           . "<td>$name</td>"
           . "<td>$email</td>"
           . "<td>$amount</td>"
           . "</tr>";
  }

  $html = $html."</table>";
 
  return $html;
}

function getContactPhone(PDO $dbh,$contactId){

 $sql = $dbh->prepare("SELECT phone
                       FROM civicrm_phone
                       WHERE contact_id = :contactId
                      ");
 $sql->execute(array(':contactId'=>$contactId));
 $result = $sql->fetch(PDO::FETCH_ASSOC);
 $phone = $result["phone"];

 return $phone;

}
?>
