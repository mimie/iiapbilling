<html>
<head>
<title>Participant Listing</title>
 <link rel="stylesheet" type="text/css" href="billingStyle.css">
 <link rel="stylesheet" type="text/css" href="menu.css">
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
  $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin'); 

  include 'dbcon.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'billing_functions.php';
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

  $eventId = $_GET["eventId"];

  $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin');
   
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
   echo "<table border = '1' align='center'>";
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


/*  $displayEventHeader = displayEventHeader($eventId);
  echo $displayEventHeader;*/

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
