<html>
<head>
<title>Billing With Vat</title>
<style>
#main{
  border: 3px solid #0000FF;
  width: 896.5px;
  height: 878.36px;
  background-image:url('images/watermark.png');
  background-repeat:no-repeat;
  background-position:center;
  background-size:350px 300px;

}

#header{
  width: 856.44px;
  height: 49.51px;
  background-color: #08088A;
  font-family: Arial;
  color: white;
  font-weight: bold;
}

#logo{
  width:48.38px;
  height:43.09px;
  padding: 2px 2px 2px 2px;
  margin-left: auto;
  margin-right: auto;
}

#tin{
  width: 856.44px;
  height: 31.75px;
  font-family: Arial;
  padding: 4px 4px 2px 2px;
  margin: margin 0 auto;
  
}
#billedTo{
  width: 856.44px;
  font-family: Arial;
  margin: margin 0 auto;
  
}
</style>
</head>
<body>
<?php

  include 'dbcon.php';
  include 'pdo_conn.php';
  include 'badges_functions.php';
  include 'weberp_functions.php';
  include 'billing_functions.php';
  include 'send_functions.php';
  include 'login_functions.php';

  $dbh = civicrmConnect();
 
  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  @$billingNo = $_GET["billingRef"];
  @$eventId = $_GET["eventId"];
  @$userId = $_GET["user"];
  $generator = getUserFullName($dbh,$userId);
  //$billingNo = '3154';
  //$eventId = '233';
  $billingDetails = getIndividualBillingDetails($dbh,$billingNo,$eventId);

  $eventType = getEventTypeName($dbh,$eventId);
  
  $participantId = $billingDetails["participant_id"];
  $participantName = $billingDetails["participant_name"];
  $orgName = $billingDetails["organization_name"];
  $billAddress = $billingDetails["bill_address"];
  $feeAmount = $billingDetails["fee_amount"];
  $currencyFormat = number_format($feeAmount,2);

  $billDate = $billingDetails["bill_date"];
  $billDate = date("F j Y",strtotime($billDate));
  
  $eventDetails = getEventDetails($dbh,$eventId);
  $eventName = $eventDetails["event_name"];
  $dueDate = $eventDetails["start_date"];
  $dueDate = date("F j Y", strtotime($dueDate));

  $eventEndDate = $eventDetails["end_date"];
  $eventEndDate = date("F j Y", strtotime($eventEndDate));
  $locationDetails = getEventLocation($dbh,$eventId);
  $eventLocation = formatEventLocation($locationDetails);

  $contactId = getParticipantContactId($dbh, $participantId, $eventId);

  //$tax = round($feeAmount/9.3333,2);
  //$netVat = round($feeAmount - $tax,2);
  $tax = number_format($billingDetails["vat"],2);
  $netVat = number_format($billingDetails["subtotal"],2);

?>
<div id="main">
  <div style="width:896.5px;height:7.93px;"></div>
   <center>
    <div id="header">
     <table id="header">
       <tr>
        <td rowspan="2"><img id="logo" src="iiap_logo.png"></td>
        <td>Institute of Internal Auditors Philippines, Inc.</td>
        <td rowspan="2" align="center"><font style="font-size:35px">BILLING</font></td>
       </tr>
      <tr>
        <td><font size="2">Unit 702 Corporate Center, 139 Valero St., Salcedo Village, Makati City 1227</font></td>
      </tr>
    </table>
   </div>
  </center>
  <center>
    <div id="tin">
      <table align="left">
       <tr>
         <td><font style="font-size:12px"><b>TIN No. 001-772-403-000 : (+632) 940-9551 /940-9554 : Fax (+632) 325-0414</b></font></td>
       </tr>
     </table>
    </div>
    <!--billed to-->
    <div id="billedTo">
     <table align="left">
      <tr>
         <td colspan="4"><b><font style="font-size:16px">BILLED TO:</font></b></td>
      </tr>
      <tr>
         <td width="42.71px"></td>
         <td width="485px"><font style="font-size:13px"><?=$participantName?></font></td>
         <td align="right" width="165px" style="border-right:2px solid black"><font style="font-size:13px"><b>BILLING NUMBER</b></font></td>
         <td width="195.78px"><font style="font-size:19px"><b><?=$billingNo?></b></font></td>
      </tr>
      <tr>
         <td></td>
         <!--This line for the organizatio name  and address-->
         <td width="485px"><font style="font-size:13px"><?=$orgName?></font></td>
         <td align="right" width="165px" style="border-right:2px solid black"><font style="font-size:13px"><b>BILLING DATE</b></font></td>
         <td width="195.78px"><font style="font-size:13px"><?=$billDate?></font></td>
      </tr>
      <tr>
         <td></td>
         <td width="485px"><font style="font-size:13px"><?=$billAddress?></font></td>
         <td align="right" width="165px" style="border-right:2px solid black"><font style="font-size:13px"><b>DUE DATE</b></font></td>
         <td width="195.78px"><font style="font-size:13px"><?=$dueDate?></font></td>
      </tr>
      <tr>
       <td colspan="4"><br></td>
      </tr>
     </table>
     <!--end of billed to-->
    </div>
    <div id="billedTo">
     <!--particulars-->
     <table align="left" style="border-collapse:collapse;">
      <tr>
        <td colspan="2" width="692.71px" align="center" bgcolor="#D8D8D8" style="border:2px solid black;"><font style="font-size:13px"><b>PARTICULARS</b></font></td>
        <td width="195.78px" align="center" bgcolor="#D8D8D8" style="border:2px solid black"><font style="font-size:13px"><b>AMOUNT</b></font></td>
      </tr>   
      <tr>
        <td colspan="2" height="350px" style="border:2px solid black; vertical-align:top;" align="left"><?=$eventName?>
               <br>On&nbsp;<?=$dueDate?>&nbsp;to&nbsp;<?=$eventEndDate?>
               <br>At&nbsp;<?=$eventLocation?></td>
        <td style="border:2px solid black; vertical-align:top;" align="center"><br><?=$currencyFormat?></td> 
      </tr>
      <tr>
        <td width="527.71px" style="border:2px solid black;" rowspan="2" align="center">
         <font style="font-size:19px"><b><i>THANK YOU FOR YOUR BUSINESS!</b></i></font><br>
         <font style="font-size:13px"><b>(NOT VALID FOR INPUT TAX CLAIM)</b></font>
        </td>
        <td width="165px" style="border:2px solid black;" align="right" rowspan="2">SUBTOTAL<br>VAT - 12%</td>
        <td width="195.78px" height="26.84px" style="border:2px solid black;" align="center"><?=$netVat?></td>
      </tr>
      <tr>
        <td width="195.78px" height="26.84px" style="border:2px solid black;" align="center"><?=$tax?></td>
      </tr>
      <tr>
        <td colspan="2" height="15px"></td>
        <td width="195.78px" style="border:2px solid black;" rowspan="2" align="center"><?=$currencyFormat?>&nbsp;PHP</td>
      </tr>
      <tr>
        <td colspan="2"><b><font style="font-size:13px;font-family:Arial">DIRECT ALL INQUIRIES TO:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            PAYMENT INSTRUCTION:</font></b>
        </td>
      </tr>
      <tr>
        <td colspan="2"><b><i><font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$generator?></i></b>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         &Oslash;&nbsp;If by check, <font color="red"><b><u>should be</u></b></font> made payable to:</font>
        </td>
        <td align="center" width="195.78px" style="border:2px solid black;" bgcolor="#D8D8D8"><font style="font-size:11px"><b>TOTAL AMOUNT DUE</b></font></td>
      </tr>
      <tr>
       <td colspan="3"><font style="font-size:13px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(+632) 940-9554</font></td>
      </tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">email: ar_finance@iia-p.org</font>
         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
         <font style="font-size:22px"><b><i>Institute of Internal Auditors Philippines, Inc.</i></b></font>
       </td>
      </tr>
      <tr><td><br></td></tr>
      <tr>
       <td colspan="3"><font style="font-size:13px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;
          &Oslash;&nbsp;If thru bank telegraphic transfer, include <b><u>P250 /$ 6.50,</b></u> in your payment to cover for bank charges.
        </font>
       </td>
      </tr>
      <tr><td></td></tr>
       <tr>
        <td colspan="3"><font style="font-size:12px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &Oslash;&nbsp;If by SM Department Store Bills Payment Center,
       </font>
       </td>
      </tr>
      <tr>
       <td colspan="3"><font style="font-size:12px;font-family:Arial">
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;Please indicate the SM Department Store branch where you are transacting and present your<br>

          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;<b>ORIGINAL COPY</b> of the receipt of the payment.
          </font>
       </td>
      <tr>
     </table>
    </div>
  </center>
</div>
<script>
//  window.print();
</script>
</body>
</html>
