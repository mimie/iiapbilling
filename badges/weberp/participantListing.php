<html>
<head>
<title>Participant Listing</title>
<script type="text/javascript">
  var checked=false;
  var formname='';

function checkedAll(formname)
{
  var values= document.getElementById(formname);
 
  if (checked==false)
  {
    checked=true;
  }
 
  else
  {
    checked = false;
  }

 for (var i=0; i < values.elements.length; i++)
 {
   values.elements[i].checked=checked;
 }

}
</script>
</head>
<body>
<?php

  include '../dbcon.php';
  include '../badges_functions.php';
  include 'weberp_functions.php';

  $eventId = $_GET["eventId"];

  $displayEventHeader = displayEventHeader($eventId);
  echo $displayEventHeader;

  $filterParticipantForm = filterParticipantForm();
  echo $filterParticipantForm;


  if(isset($_POST["searchNameEmail"])){
    
    $searchNameEmail = $_POST["nameEmailTb"];
    $contactIds = getContactIdSearchName($eventId,$searchNameEmail);
    $searchParticipantByName = searchedParticipantListByName($contactIds,$eventId);
    echo $searchParticipantByName;

  }

  elseif(isset($_POST["searchOrg"])){

    $searchOrg = $_POST["orgTb"];
    $contactIds = getContactIdSearchOrg($eventId,$searchOrg);
    $searchParticipantByName = searchedParticipantListByName($contactIds,$eventId);
    echo $searchParticipantByName;
  }

  elseif(isset($_POST["searchStatus"])){
    $statusId = $_POST["statusTypeSelect"];
    $contactIds = getContactIdSearchStatusId($eventId,$statusId);
    $searchParticipantByName = searchedParticipantListByName($contactIds,$eventId);
    echo $searchParticipantByName;
  }

  elseif(isset($_POST["updateStatus"])){
    $contactIds = $_POST["contactIds"];
    $statusId = $_POST["statusType"];
    
    foreach($contactIds as $contactId){
      updateParticipantStatus($contactId,$eventId,$statusId);
    }

    if($searchParticipantByName){
      echo $searchParticipantByName;
    }

    else{
      
    $displayParticipants = getParticipantByEvent($eventId);
    echo $displayParticipants;
    }

  }

  else{
  
    $displayParticipants = getParticipantByEvent($eventId);
    echo $displayParticipants;
  }
  
?>
</body>
</html>
