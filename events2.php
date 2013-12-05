<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Events</title>
  <link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
  <script src="http://code.jquery.com/jquery-1.9.1.js"></script>
  <script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
  <link rel="stylesheet" href="/resources/demos/style.css" />
  <script>
  $(function() {
    $( "#datepickerStart" ).datepicker();
    $( "#datepickerEnd" ).datepicker();
  });
  </script>
  <link rel="stylesheet" type="text/css" href="billingStyle.css">
  <link rel="stylesheet" type="text/css" href="menu.css">
</head>
<body>
<?php
  
  include 'dbcon.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'login_functions.php';
 
  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  echo "<div align='center' style='padding:16px;'>";  
  $logout = logoutDiv();
  echo $logout;
  $header = headerDiv();
  echo $header;
  echo "</div>";
?>
<center>
<div align='center' style='width:50%;'>
<fieldset>
<legend><b>Search Event</b></legend>
<form name="dateSelector" method='post'>
<p>From: <input type="text" id="datepickerStart" name="startDate" required>
   To: <input type="text" id="datepickerEnd" name="endDate" required>
   <input type="submit" value="View Events" name="viewEvents">

</p>
</form>
<form name="events" method="post">
<p>
 <input type="text" name="eventName">
 <input type="submit" value="Search Event Name" name="searchEvent">
  <input type="submit" value="View All Events" name ="viewAll">
</form> 
</p>
<p>
<?php
  $eventTypesFilterForm = eventTypesFilterForm();
  echo $eventTypesFilterForm;
?>
</p>
</fieldset>
</center>
</div><br><br>
<?php

  if(isset($_POST["viewEvents"])){
  $startDate = $_POST["startDate"];
  $endDate = $_POST["endDate"];

  $startDate = date("Y-m-d",strtotime($startDate));
  $endDate = date("Y-m-d",strtotime($endDate));

   $eventIds = getEventByDate($startDate,$endDate);
   $eventsDisplay = displayEvents($eventIds);
   echo $eventsDisplay;
  }

  elseif(isset($_POST["searchEvent"])){

    $eventSearch = $_POST["eventName"];
    $eventIds = searchEventName($eventSearch);
    $eventsDisplay = displayEvents($eventIds);
    echo $eventsDisplay;

  }

  elseif(isset($_POST["searchEventType"])){
   
    $eventTypeId = $_POST["eventType"];
    $eventIds = searchEventType($eventTypeId);
    $eventsDisplay = displayEvents($eventIds);
    echo $eventsDisplay;
  }

  elseif(isset($_POST["viewAll"]) || !isset($_POST["viewEvents"]) || !$_POST["searchEvent"]){
    $allEvents = getAllEvents();
    $eventIds = array_keys($allEvents);

    $eventsDisplay = displayEvents($eventIds);
    echo $eventsDisplay;
  }

  
?>


</body>
</html>

