<html>
<head>
<title>Membership Billing</title>
<style>
#main{
  border: 3px solid #0000FF;
  width: 739.28px;
  height: 959.62px;
  background-image:url('images/watermark.png');
  background-repeat:no-repeat;
  background-position:center;
  background-size:350px 300px;

}

#header{
  width: 699.21px;
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
  width: 699.21px;
  height: 57.83px;
  font-family: Arial;
  margin: margin 0 auto;
  padding: 3px 1px 1px 1px;
  
}
#billedTo{
  width: 699.21px;
  font-family: Arial;
  margin: margin 0 auto;
  
}
</style>
</head>
<body>
<?php

  $dbh = new PDO('mysql:host=localhost;dbname=webapp_civicrm', 'root', 'mysqladmin');
  /**include 'dbcon.php';
  include 'badges_functions.php';**/
  include 'weberp_functions.php';
  include 'billing_functions.php';
  include 'send_functions.php';
  include 'membership_functions.php';
  include 'login_functions.php';
 
  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  @$billingId = $_GET["billingId"];
  $billingDetails = getMemberBillingDetails($dbh,$billingId);

  $userId = $_GET["user"];
  $generator = getUserFullName($dbh,$userId);

  $memberYear = $billingDetails["year"];
  $memberName = $billingDetails["member_name"];
  $orgName = $billingDetails["organization_name"];
  $street = $billingDetails["street"];
  $city = $billingDetails["city"];
  $feeAmount = $billingDetails["fee_amount"];
  $currencyFormat = number_format($feeAmount,2);
  $billingNo = $billingDetails["billing_no"];

  $contactId = $billingDetails["contact_id"];  
  $phone = getContactPhone($dbh,$contactId);
  $memberId = getMemberId($dbh,$contactId);

  $billDate = $billingDetails["bill_date"];
  $billDate = date("F j Y",strtotime($billDate));

?>
<div id="main">
  <div style="width:896.5px;height:7.93px;"></div>
   <center>
    <div id="header">
     <table id="header">
       <tr>
        <td rowspan="2"><img id="logo" src="iiap_logo.png"></td>
        <td>Institute of Internal Auditors Philippines, Inc.</td>
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
         <td width="50%"><font style="font-size:12px"><b>TIN No. 001-772-403-000 : (+632) 940-9551 /940-9554 : Fax (+632) 325-0414</b></font></td>
         <td width="50%" align="right" rowspan="2"><font style="font-size:48px;color:#0B3861;"><i>BILLING</i></font></td>
       </tr>
       <tr><td><br></td></tr>
     </table>
    </div>
    <!--billed to-->
    <div id="billedTo">
     <table align="left">
      <tr>
         <td width="101.67px"><font style="font-size:16px"><b>BILLED TO:</b></font></td>
         <td width="329.95px"><font style="font-size:13px"><b><?=$memberName?></b></font></td>
         <td width="135.31px" style="border-right:2px solid black;"><font style="font-size:13px"><b>DATE:</b></font></td>
         <td width="132.28px"><font style="font-size:13px"><?=$billDate?></font></td>
      </tr>
      <tr>
         <td></td>
         <td><font style="font-size:13px"><?=$orgName?></font></td>
         <td style="border-right:2px solid black;"><font style="font-size:15px"><b>Billing No.:</b></font></td>
         <td><font style="font-size:13px"><b><?=$billingNo?></b></font></td>
      </tr>
      <tr>
         <td></td>
         <td><font style="font-size:13px"><?=$street?><br><?=$city?></font></td>
         <td style="border-right:2px solid black;"><font style="font-size:13px"><b>Member ID:</b></font></td>
         <td><font style="font-size:13px"><?=$memberId?></font></td>
      </tr>
      <tr>
         <td></td>
         <td colspan="2" style="border-right:2px solid black;"><font style="font-size:13px"><?=$phone?></font></td>
         <!--<td style="border-right:2px solid black;">Something</td>-->
         <td></td>
      </tr>
      <tr>
       <td colspan="3" height="37.8px" style="border-right:2px solid black;"><br></td>
       <td></td>
      </tr>
     </table>
     <!--end of billed to-->
    </div>
    <div id="billedTo">
     <!--particulars-->
     <table align="left" style="border-collapse:collapse;" border="1px">
      <tr>
        <td colspan="2" width="566.93px" align="center" bgcolor="#0B0B3B" style="border:2px solid #BCF5A9"><font style="font-size:13px;color:white;"><b>PARTICULARS</b></font></td>
        <td width="132.28px" align="center" bgcolor="#0B0B3B" style="border:2px solid #BCF5A9"><font style="font-size:13px;color:white;"><b>AMOUNT</b></font></td>
      </tr>   
      <tr>
        <td colspan="2" height="350px" style="border:2px solid #BCF5A9; vertical-align:top;">
          Institute of Internal Auditors Philippines, Inc.<br>
          Membership Annual Registration&nbsp;<?=$memberYear?><br> 
        </td>
        <td style="border:2px solid #BCF5A9; vertical-align:top;" align="center"><br><?=$currencyFormat?></td> 
      </tr>
      <tr>
        <td height="20px" width="320.5px" align="center" style="border-bottom-width:2px"><b><i><font style="font-size:13px">VAT-EXEMPT TRANSACTION</font></i></b></td>
        <td width="246.43px" align="right"><b><font style="font-size:13px">Total Amount Due</font></b></td>
        <td width="132.28px" align="center"><font style="font-size:13px"><?=$currencyFormat?>&nbsp;PHP</font></td>
      </tr>
     </table>
     </div>

     <div id = "billedTo">
     <table align="left">
      <tr>
       <td width="279.61px" style="vertical-align:top">
          <br><font style="font-size:13px;font-family:Arial"><b>DIRECT ALL INQUIRIES TO:</b></font><br>
          <b><i><font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<?=$generator?></i></b><br>
          <font style="font-size:13px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(+632) 940-9554</font><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;email: ar_finance@iia-p.org</font>
       </td>
       <td width="419.61px">
          <br><font style="font-size:13px;font-family:Arial"><b>PAYMENT INSTRUCTION:</b></font><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&Oslash;&nbsp;If by check, <font color="red"><b><u>should be</u></b></font> made payable to:</font><br>
          <font style="font-size:15px"><b><i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Institute of Internal Auditors Philippines, Inc.</i></b></font><br><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&Oslash;&nbsp;If thru bank telegraphic transfer, include <b><u>P250 /$ 6.50,</b></u> in your 
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;payment to cover for bank charges.</font><br><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&Oslash;&nbsp;By direct deposit to:</font><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Institute of Intenal Auditors Philippines, Inc.</b></font><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Acct No. 691-002-1745</b></font><br>
          <font style="font-size:13px;font-family:Arial">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>Banco de Oro - Rufino Branch</b></font><br><br>
         <font style="font-size:13px;font-family:Arial">
           Please indicate the BDO branch where you are transacting and<br>
           present your <b>ORIGINAL COPY</b> of the bank validated deposit slip<br>
           to claim your OFFICIAL RECEIPT. 
         </font>
       </td>
      </tr>
     </table>
    </div>
  </center>
</div>
</body>
</html>
