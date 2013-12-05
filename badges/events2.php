<?php
  include 'badges/dbcon.php';
  include 'badges/badges_functions.php';
  include 'weberp_functions.php';
?>
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
</head>
<body>
<form name="dateSelector" method='post'> 
<p>From: <input type="text" id="datepickerStart" name="startDate"/>
   To: <input type="text" id="datepickerEnd" name="endDate"/>
   <input type="submit" value="View Events" name="viewEvents">

</p>
</form>
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
?>
 
 
</body>
</html>

