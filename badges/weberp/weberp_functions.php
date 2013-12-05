<?php

function getEventByDate($startDate,$endDate){

  $sql = "SELECT id FROM civicrm_event\n"
       . "WHERE start_date BETWEEN '$startDate 00:00:00' AND '$endDate 23:59:59' ORDER BY start_date DESC";
  $result = mysql_query($sql) or die(mysql_error());

  $eventIds = array();

  while($row = mysql_fetch_assoc($result)){
    $eventIds[] = $row['id'];
  }

  return $eventIds;
}

function displayEvents($eventIds){

  $allEvents = getAllEvents();
  $html = "<table align='center' border='1'>"
        . "<tr>"
        . "<th>Event Title</th>"
        . "<th>Event Date</th>"
        . "<th>Participant List</th>"
        . "</tr>";

  foreach($eventIds as $id){

    $eventInfo = $allEvents["$id"];
    $title = $eventInfo["title"];
    $date = $eventInfo["start_date"];

    $html = $html."<tr>"
          ."<td>$title</td>"
          ."<td>".formatDate($date)."</td>"
          ."<td align='center'><a href='participantListing.php?eventId=".$id."'>Participants</a></td>"
          ."</tr>";

  }

  $html = $html."</table>";
  return $html;
}

function getStatusId($eventId,$contactId){

  $sql = "SELECT status_id FROM civicrm_participant WHERE event_id = '$eventId' AND contact_id='$contactId'";
  $result = mysql_query($sql) or die(mysql_error());

  $row = mysql_fetch_assoc($result);
  $statusId = $row["status_id"];

  return $statusId;
}


function searchEventName($eventName){

  $allEvents = getAllEvents();
  $eventIdMatches = array();

  $patternEvent = "/\b\w*".$eventName."\w*\b/";

  foreach($allEvents as $eventId => $details){
    $title = $details["title"];
    $result = preg_match($patternEvent,$title);

    if($result == 1){
      $eventIdMatches[] = $eventId;

    }
  }

  return $eventIdMatches;
}

function getParticipantByEvent($eventId){

 $allContacts = getAllContacts();
 $contactIds = getEventParticipantId($eventId);
 $allEmails = getAllEmails();
 $status = getParticipantStatusType();
 $statusSelector = participantStatusSelector();
 //$filterParticipantForm = filterParticipantForm();

 //$html = $filterParticipantForm;
 $html = "<form id='participants' method='post'>"
       . "<div align='center' style='padding:6px;'>$statusSelector</div>";
 
 $html = $html."<table border='1' align='center'>"
       . "<tr>"
       . "<th>Participant Name</th>"
       . "<th>Organization Name</th>"
       . "<th>Email Address</th>"
       . "<th>Participant Status</th>"
       . "<th>Change Participant Status</th>"
       . "<th>Fee Amount</th>"
       . "<th>Post</th>"
       . "<tr>";

 foreach($contactIds as $id){

  $details = $allContacts[$id];
  $name = $details["name"];
  $org = $details["org"];
  $email = $allEmails[$id];

  $feeAmount = getParticipantFeeAmount($id,$eventId);
  
  $statusId = getParticipantStatusId($id,$eventId);
  $statusName = $status[$statusId];
  //$statusTypeSelectForm = statusTypeSelectForm($statusId);

  $html = $html."<tr>"
        . "<td>$name</td>"
        . "<td>$org</td>"
        . "<td>$email</td>"
        . "<td align='center'>$statusName</td>"
        . "<td align='center'><input type='checkbox' name='contactIds[]' value='$id'></td>"
        . "<td align='center'>$feeAmount</td>"
        . "<td align='center'><input type='submit' value='Post'></td>"
        . "</tr>";
  }

  $html = $html."</form></table>";

  return $html;

 
}

/*
 *display searched participant by name or email
 */
function searchedParticipantListByName(array $contactIds,$eventId){

 $allContacts = getAllContacts();
 $allEmails = getAllEmails();
 $status = getParticipantStatusType();
 $statusSelector = participantStatusSelector();

 $html = "<form id='participants' method='post'>"
       . "<div align='center' style='padding:6px;'>$statusSelector</div>";
 
 $html = $html."<table border='1' align='center'>"
       . "<tr>"
       . "<th>Participant Name</th>"
       . "<th>Organization Name</th>"
       . "<th>Email Address</th>"
       . "<th>Participant Status</th>"
       . "<th>Change Participant Status</th>"
       . "<th>Fee Amount</th>"
       . "<th>Post</th>"
       . "<tr>";

 foreach($contactIds as $id){

  $details = $allContacts[$id];
  $name = $details["name"];
  $org = $details["org"];
  $email = $allEmails[$id];

  $feeAmount = getParticipantFeeAmount($id,$eventId);
  
  $statusId = getParticipantStatusId($id,$eventId);
  $statusName = $status[$statusId];
  //$statusTypeSelectForm = statusTypeSelectForm($statusId);

  $html = $html."<tr>"
        . "<td>$name</td>"
        . "<td>$org</td>"
        . "<td>$email</td>"
        . "<td align='center'>$statusName</td>"
        . "<td align='center'><input type='checkbox' name='contactIds[]' value='$id'></td>"
        . "<td align='center'>$feeAmount</td>"
        . "<td align='center'><input type='submit' value='Post'></td>"
        . "</tr>";
  }

  $html = $html."</form></table>";

  return $html;

}

function getContactIdSearchName($eventId,$searchCriteria){

  $searchCriteria = mysql_real_escape_string($searchCriteria);

  $sql = "SELECT cc.id as contactId FROM civicrm_participant cp, civicrm_email cem, civicrm_contact cc\n"
      . "WHERE cp.event_id='$eventId'\n"
      . "AND cp.contact_id = cc.id\n"
      . "AND cem.contact_id = cc.id\n"
      . "AND (cc.display_name LIKE '%{$searchCriteria}%' OR cem.email LIKE '%{$searchCriteria}%')";

  $result = mysql_query($sql) or die(mysql_error());

  $contactIds = array();

  while($row = mysql_fetch_assoc($result)){

    $contactIds[] = $row["contactId"];
  }

  return $contactIds;
}

function getContactIdSearchOrg($eventId,$orgName){

  $orgName = mysql_real_escape_string($orgName);
  $sql = "SELECT organization_name, cc.id AS contactId\n"
       . "FROM civicrm_participant cp, civicrm_contact cc\n"
       . "WHERE cp.event_id = '$eventId'\n"
       . "AND cp.contact_id = cc.id\n"
       . "AND cc.organization_name LIKE '%{$orgName}%'";
  $result = mysql_query($sql) or die(mysql_error());

  $contactIds = array();

  while($row = mysql_fetch_assoc($result)){

    $contactIds[] = $row["contactId"];
  }

  return $contactIds;

}

function getContactIdSearchStatusId($eventId,$statusId){

   $sql = "SELECT contact_id,status_id FROM civicrm_participant\n"                                       
        . "WHERE status_id = '$statusId'\n"
        . "AND event_id ='$eventId'";

   $result = mysql_query($sql) or die(mysql_error());

   $contactIds = array();

   while($row = mysql_fetch_assoc($result)){
     $contactIds[] = $row["contact_id"];
   }

  return $contactIds;
}

function getParticipantStatusId($contactId,$eventId){

 $contactId = mysql_real_escape_string($contactId);
 $eventId = mysql_real_escape_string($eventId);
 $sql = "SELECT status_id FROM civicrm_participant\n" 
      . "WHERE contact_id = '{$contactId}'"
      . "AND event_id ='{$eventId}'";
 $result = mysql_query($sql) or die(mysql_error());

 $row = mysql_fetch_assoc($result);
 $statusId = $row["status_id"];

 return $statusId;

}

/*
 *On waitlist, Awaiting Approval, Pending from waitlist,
 *Pending from Approval, Rejected are disabled status type
 */
function getParticipantStatusType(){
 
  $status = array();
  
  $sql = "SELECT id,label FROM civicrm_participant_status_type\n"
       . "WHERE id NOT IN(5,6,7,8,9,10,11,12,13)";
  $result = mysql_query($sql) or die(mysql_error());
 
  while($row = mysql_fetch_assoc($result)){
     
     $statusId = $row["id"];
     $statusName = $row["label"];
  
     $status[$statusId] = $statusName;
  }
  
  return $status;
 }

function statusTypeSelectForm($statusId){

  $status = getParticipantStatusType();

  $html = "<SELECT name='statusType'>";

  foreach($status as $id => $statusName){

    $selected = $statusId == $id ? 'selected' : '';
    var_dump($selected);
    $html = $html."<option value='$id' $selected>$statusName</option>";
  }

  $html = $html."</SELECT>";

  return $html;
}

function displayEventHeader($eventId){

  $eventName = getEventName($eventId);
  $html = "<div align='center'>"
        . "<h3>List of Participants for $eventName</h3>"
        . "</div>";

  return $html;
}

function getParticipantFeeAmount($contactId,$eventId){

  $contactId = mysql_real_escape_string($contactId);
  $eventId = mysql_real_escape_string($eventId);

  $sql = "SELECT fee_amount FROM civicrm_participant\n"
       . "WHERE contact_id ='{$contactId}' AND event_id = '{$eventId}'";
  $result = mysql_query($sql) or die(mysql_error());

  $row = mysql_fetch_assoc($result);
  $feeAmount = $row["fee_amount"];

  return $feeAmount;
}

function participantStatusSelector(){

  $status = getParticipantStatusType();

  $html = "Change Participant Status: "
        . "<SELECT name='statusType'>"
        . "<option value='select'>Select status</option>"
        . "<option disabled>-------------</option>";
  foreach($status as $id => $statusName){
   $html = $html."<option value='$id'>$statusName</option>";
  }
  $html = $html."</SELECT>";
  $html = $html."Check All<input type='checkbox' name='checkall' onclick='checkedAll(\"participants\");'>";
  $html = $html."<input type='submit' value='UPDATE' name='updateStatus'>";

  return $html;
}

/*
 *form for searching the participant
 */
function filterParticipantForm(){

 $status = getParticipantStatusType();

 $html = "<center>"
       . "<div align='center' style='width:50%;'>"
       . "<fieldset>"
       . "<legend><b>Search Participant</b></legend>"
       . "<form method='post' name='searchFilter'>"
       . "<table>"
       . "<tr>"
       . "<td><b>Name or Email:</b></td><td><input type='textbox' name='nameEmailTb'><input type='submit' value='SEARCH' name='searchNameEmail'></td>"
       . "</tr>"
       . "<tr>"
       . "<td><b>Organization Name:</b></td><td><input type='textbox' name='orgTb'><input type='submit' value='SEARCH' name='searchOrg'></td>"
       . "</tr>";

 $html = $html."<tr><td><b>Participant Status:</b></td>"
       . "<td><SELECT name='statusTypeSelect'>"
       . "<option value='select'>Select status</option>"
       . "<option disabled>-------------</option>";

  foreach($status as $id => $statusName){
    $html = $html."<option value='$id'>$statusName</option>";
  }

  $html = $html."</SELECT>"
        . "<input type='submit' value='SEARCH' name='searchStatus'></td>"
        . "</tr>"
        . "</table>"
        . "</form>"
        . "</fieldset>"
        . "</div>"
        . "</center>";

  return $html;
}

/*
 *option_group_id = 14 FROM civicrm_option_group table
 */
function getAllEventTypes(){

 $sql = "SELECT label,value FROM civicrm_option_value\n"
      . "WHERE option_group_id='14'";
 $result = mysql_query($sql) or die(mysql_error());

 $eventTypes = array();

 while($row = mysql_fetch_assoc($result)){
    $eventTypeId = $row["value"];
    $eventTypeName = $row["label"];

    $eventTypes[$eventTypeId] = $eventTypeName;
 }

 return $eventTypes;
}

function eventTypesFilterForm(){

  $allEventTypes = getAllEventTypes();

  $html = "<form name='eventTypes' method='post'>"
        . "<SELECT name='eventType'>"
        . "<option value='select'>Select event type</option>"
        . "<option value='' disabled>-----------------</option>";

  foreach($allEventTypes as $eventTypeId => $eventTypeName){

    $html = $html."<option value='$eventTypeId'>$eventTypeName</option>";
  }

  $html = $html."</SELECT>";
  $html = $html."<input type='submit' value='Search Event Type' name='searchEventType'>";
  $html = $html."</form>";

  return $html;

}

function searchEventType($eventTypeId){

  $sql = "SELECT id FROM civicrm_event\n"
       . "WHERE event_type_id='$eventTypeId'";
  $result = mysql_query($sql) or die(mysql_error());

  $eventIds = array();

  while($row = mysql_fetch_assoc($result)){
    $eventIds[] = $row["id"];
  }

  return $eventIds;
}

function updateParticipantStatus($contactId,$eventId,$statusId){

  $contactId = mysql_real_escape_string($contactId);
  $eventId = mysql_real_escape_string($eventId);
  $statusId = mysql_real_escape_string($statusId);

  $sql = "UPDATE civicrm_participant\n"
       . "SET status_id = '{$statusId}'\n"
       . "WHERE event_id = '{$eventId}'\n"
       . "AND contact_id = '{$contactId}'";

  $result = mysql_query($sql) or die(mysql_error());
}
?>

