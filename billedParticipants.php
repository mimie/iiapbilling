<html>
<head>
<title>Billed Participants</title>
<link rel="stylesheet" type="text/css" href="billingStyle.css">
<link rel="stylesheet" type="text/css" href="menu.css">
<style type="text/css">
body{overflow:hidden;}
</style>
</head>
<body>
<?php

  include 'billing_functions.php';
  include 'login_functions.php';
  $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin'); 
 
  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }
  
  echo "<div id='eventDetails'>";
  $logout = logoutDiv();
  echo $logout;

  @$billingNo = $_GET["billingNo"];
  @$eventId = $_GET["eventId"];

   $eventDetails = getEventDetails($dbh,$eventId);
   $eventName = $eventDetails["event_name"];
   $eventStartDate = $eventDetails["start_date"];
   $eventEndDate = $eventDetails["end_date"];
   $eventTypeName = getEventTypeName($dbh,$eventId);
   $locationDetails = getEventLocation($dbh,$eventId);
   $eventLocation = formatEventLocation($locationDetails);

   $billingDetails = getCompanyBillingDetails($dbh,$billingNo,$eventId);
   $orgName = $billingDetails["organization_name"];
   $amount = $billingDetails["total_amount"];
   $amount = number_format($amount,2); 
   $subtotal = $billingDetails["subtotal"];
   $subtotal = number_format($subtotal,2);
   $vat = $billingDetails["vat"];
   $vat = number_format($vat,2);
   $billDate = $billingDetails["bill_date"];
   $billDate = date("F j Y",strtotime($billDate));

   echo "<table border = '1' width='100%'>";
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
   echo "<tr>";
   echo "<th>Organization Name</th><td><i>$orgName</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Billing No.</th><td><i>$billingNo</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Billing Date</th><td><i>$billDate</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Total Amount</th><td><i>$amount&nbsp;PHP</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>Subtotal</th><td><i>$subtotal&nbsp;PHP</i></td>";
   echo "</tr>";
   echo "<tr>";
   echo "<th>VAT</th><td><i>$vat&nbsp;PHP</i></td>";
   echo "</tr>";
   echo "</table><br><br>";

   $billedParticipants = getCompanyBilledParticipants($dbh,$billingNo,$eventId);
   $display = displayBilledParticipants($billedParticipants);
   echo $display;
   echo "</div>";
?>
</body>
</html>
