<?php

/**
 *function to get membershipDetails
 *membership_type_id = 'General' from civicrm_membership_type
 */

function getMembersToExpire(PDO $dbh,$endDate){

  $sql = $dbh->prepare("SELECT id,contact_id,end_date,status_id,membership_type_id,join_date,start_date,end_date
                        FROM civicrm_membership
                        WHERE membership_type_id = '1'
                        AND end_date = ?
                        LIMIT 20
                       ");
  $sql->bindParam(1,$endDate,PDO::PARAM_STR,10);
  $sql->execute();
  $details = $sql->fetchAll(PDO::FETCH_ASSOC);

  return $details;
}

function getMembershipStatus($dbh,$statusId){

  $sql = $dbh->prepare("SELECT id, name FROM civicrm_membership_status
                        WHERE id = '$statusId'
                       ");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $status = $details["name"];

  return $status;
  
}

function getMemberFeeAmount($dbh,$typeId){

  $sql = $dbh->prepare("SELECT minimum_fee FROM civicrm_membership_type
                        WHERE id = '$typeId'");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $feeAmount = $details["minimum_fee"];

  return $feeAmount;
}

function getMemberType($dbh,$typeId){

  $sql = $dbh->prepare("SELECT name FROM civicrm_membership_type
                        WHERE id = '$typeId'");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $memberType = $details["name"];

  return $memberType;
}

function checkMembershipBilling($dbh,$membershipId,$memberBillingYear){

  $year = $memberBillingYear;
  $sql = $dbh->prepare("SELECT COUNT(*) as exist,billing_no,bill_date FROM billing_membership 
                        WHERE membership_id = '$membershipId'
                        AND year = '$year'
                       ");
  $sql->execute();
  $sqlDetails = $sql->fetch(PDO::FETCH_ASSOC);
 
  return $sqlDetails;
 
}



function displayMemberBilling($dbh,array $members,$expiredDate){
  
  $expiredYear = date("Y",strtotime($expiredDate));
  $memberBillingYear = intval($expiredYear) + 1;

  //$nextYear = date('Y', strtotime('+1 year'));
  $html = "<table width='100%'>"
        . "<tr>"
        . "<th colspan='15'>Membership Billing</th>"
        . "</tr>"
        . "<tr>"
        . "<th>Select Members</th>"
        . "<th>Member Name</th>"
        . "<th>Email</th>"
        . "<th>Membership Status</th>"
        . "<th>Organization Name</th>"
        . "<th>Member Fee Amount</th>"
        . "<th>Print Bill</th>"
        . "<th>Send Bill</th>"
        . "<th>Payment Status</th>"
        . "<th>Billing Reference No.</th>"
        . "<th>Billing Date</th>"
        . "<th>Billing Address</th>"
        . "<th>Membership Information</th>"
        . "<th>Billing PDF Download</th>"
        . "</tr>";

  foreach($members as $membershipId => $details){

    $name = mb_convert_encoding($details["name"], "UTF-8");
    $email = $details["email"];
    $status = $details["status"];
    $company = $details["company"];
    $amount = $details["fee_amount"];
    $address = mb_convert_encoding($details["address"], "UTF-8");
    $membersLinkInfo = membersLink($membershipId);
    $infoBilling = checkMembershipBilling($dbh,$membershipId,$memberBillingYear);
    $checkBillExist = $infoBilling["exist"];
    $billingNo = $infoBilling["billing_no"];
    $billingDate = $infoBilling["bill_date"];
    $billingDate = date("Y-m-d",strtotime($billingDate));
  
    $disabled = $checkBillExist == 1 ? 'disabled' : '';
    $checkbox = $checkBillExist == 1 ? '' : 'class=checkbox';
   $html = $html."<tr>"
          . "<td><input type='checkbox' name='membershipIds[]' value='$membershipId' $checkbox $disabled></td>"
          . "<td>$name</td>"
          . "<td>$email</td>"
          . "<td>$status</td>"
          . "<td>$company</td>"
          . "<td>$amount</td>";

    if($checkBillExist == 1){

          $year = $memberBillingYear;
          $billingId = getBillingId($dbh,$membershipId,$year);
          $html = $html . "<td>"
                . "<a href='memberBillingReference.php?billingId=$billingId' target='_blank' title='Click to print membership bill' style='text-decoration: none;'>"
                . "<img src='images/printer-icon.png' width='40' height='40'><br>Print"
                . "</a>"
                . "</td>"
                . "<td><a href='emails/membershipBilling/sendMemberBilling.php?billingId=$billingId' style='text-decoration:none;'><img src='images/email.jpg' width='40' height='40'><br>Send</a></td>"
                . "<td>Pay Later</td>"
                . "<td>$billingNo</td>"
                . "<td>$billingDate</td>"
                . "<td>$address</td>"
                . "<td>$membersLinkInfo</td>";

          $pdfFile = "pdf/membershipBilling/".$billingNo.".pdf";
          if(file_exists($pdfFile)) {
             $html = $html . "<td><a href='pdf/membershipBilling/".$billingNo.".pdf' download='IIAP_MembershipBilling_".$billingNo."' title='Click to download pdf file'><img src='images/pdf_download.jpg' width='40' height='40'></td>"
                    . "</tr>";
          }

          else{
             $html = $html . "<td><a href='pdf/membershipBilling/generatePDFMemberBilling.php?billingId=$billingId' title='Click to generate pdf'><img src='images/pdf_me.png' width='50' height='50'> </a></td>"
                   . "</tr>";
          }
      
    }

    else{
       $html = $html."<td><img src='images/not_available.png' width='25' height='25'><br></td>"
             . "<td><img src='images/not_available.png' width='25' height='25'></td>"
             . "<td></td>"
             . "<td></td>"
             . "<td></td>"
             . "<td>$address</td>"
             . "<td>$membersLinkInfo</td>"
             . "<td><img src='images/not_available_download.png' width='40' height='40'></td>"
             . "</tr>";
    }
    
  }

  $html = $html."</table>";

  return $html;
}

function membersLink($membershipId){


  $link = "<a href=\"membership_info.php?id=$membershipId\""
        . "title='Click to view membership information'"
        . "onclick=\"javascript:void window.open('membership_info.php?id=$membershipId','1384398816566','width=600,height=400,toolbar=1,menubar=0,location=0,status=1,scrollbars=1,resizable=1,left=0,top=0');"
        . "return false;\">"
        . "<img src='view_member.png'>"
        . "</a>"; 

  return $link;


}

function displayMemberInfo(PDO $dbh,array $memberInfo){

   $name = mb_convert_encoding($memberInfo["name"],"UTF-8");
   $email = $memberInfo["email"];
   $status = $memberInfo["status"];
   $company = mb_convert_encoding($memberInfo["company"],"UTF-8");
   $address = mb_convert_encoding($memberInfo["address"],"UTF-8");
   $joinDate = $memberInfo["join_date"];
   $startDate = $memberInfo["start_date"];
   $endDate = $memberInfo["end_date"];
   $contactId = $memberInfo["contact_id"];
   $memberId = getMemberId($dbh,$contactId);
   $memberType = $memberInfo["member_type"];

   $joinDate = date("F j Y",strtotime($joinDate));
   $startDate = date("F j Y",strtotime($startDate));
   $endDate = date("F j Y",strtotime($endDate));

   $html = "<table>"
         . "<tr><th>Member Name</th><td><b>".strtoupper($name)."</b></td></tr>"
         . "<tr><th>Member ID</th><td>$memberId</td></tr>"
         . "<tr><th>Membership Type</th><td>$memberType</td></tr>"
         . "<tr><th>Join Date</th><td>$joinDate</td></tr>"
         . "<tr><th>Start Date</th><td>$startDate</td></tr>"
         . "<tr><th>End Date</th><td>$endDate</td></tr>"
         . "<tr><th>Email</th><td>$email</td></tr>"
         . "<tr><th>Membership Status</th><td>$status</td></tr>"
         . "<tr><th>Organization Name</th><td>$company</td></tr>"
         . "<tr><th>Organization Address</th><td>$address</td></tr>"
         . "</table>";

   return $html;

}

function getOrgId($dbh,$organization){

   $sql = $dbh->prepare("SELECT id FROM civicrm_contact
                         WHERE contact_type = 'Organization'
                         AND display_name = '$organization'
                        ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);

  $orgId = $result["id"];
   
  return $orgId;

}

function insertMemberBilling($dbh,array $memberInfo,$membershipYear){

  $membership_id = $memberInfo["membership_id"];
  $contact_id = $memberInfo["contact_id"];
  $membership_type = $memberInfo["member_type"];
  $member_name = $memberInfo["name"];
  $email = $memberInfo["email"];
  $street = $memberInfo["street"];
  $city = $memberInfo["city"];
  $bill_address = $memberInfo["address"];
  $organization_name = $memberInfo["company"];
  $org_contact_id = $memberInfo["org_contact_id"];
  $fee_amount = $memberInfo["fee_amount"];
  $subtotal = $fee_amount;
  $vat = 0.0;

  $sqlMaxBillingId = $dbh->prepare("SELECT MAX(id) as prevBillingId FROM billing_membership");
  $sqlMaxBillingId->execute();
  $maxBillingId = $sqlMaxBillingId->fetch(PDO::FETCH_ASSOC);
  $maxBillingId = $maxBillingId["prevBillingId"] + 1;
  $billing_no = "MEM".$maxBillingId;
  $year = $membershipYear;
  

  $sql = $dbh->prepare("INSERT INTO billing_membership
                        (membership_id,contact_id,membership_type,member_name,email,street,city,bill_address,organization_name,org_contact_id,fee_amount,subtotal,vat,billing_no,year)
                        VALUES ('$membership_id','$contact_id','$membership_type','$member_name','$email','$street','$city','$bill_address','$organization_name','$org_contact_id','$fee_amount','$subtotal','$vat','$billing_no','$year')
                       ");
  var_dump($sql);
  $sql->execute();
}

function getBillingId($dbh,$membershipId,$year){

  $sql = $dbh->prepare("SELECT id FROM billing_membership
                        WHERE membership_id = '$membershipId'
                        AND year = '$year'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $id = $result["id"];

  return $id;

}

function getMemberBillingDetails($dbh,$billingId){

  $sql = $dbh->prepare("SELECT member_name, organization_name,contact_id,bill_date, billing_no, street,city, fee_amount, year
                        FROM billing_membership
                        WHERE id = '$billingId'
                       ");
  $sql->execute();
  $billingDetails = $sql->fetch(PDO::FETCH_ASSOC);

  return $billingDetails;
}


function generatePDFMemberBilling($html,$billingNo){
  
//   $date = time();
   $fileName = $billingNo.".pdf";

   $dompdf = new DOMPDF();
   $dompdf->load_html($html);
   $dompdf->set_paper('Letter','portrait');
 
   $dompdf->render();

   file_put_contents($fileName, $dompdf->output( array("compress" => 0) ));
   header('Location: ../../membershipBilling.php');
}

function sendMail($email,$billingNo,$body){

         require "Mail.php";

         $pdfFilename = $billingNo.".pdf";
         //File
         $file = fopen('../../pdf/membershipBilling/'.$pdfFilename, 'rb');
         $data = fread($file,filesize('../../pdf/membershipBilling/'.$pdfFilename));
         fclose($file);
 
         $semi_rand = md5(time());
         $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";
         $fileatttype = "application/pdf";
 
 
         $from = "Institute of Internal Auditors Philippines <iia-p.org>";
         $subject = "Sample Test IIAP Membership Annual Registration Billing";
 
         $host = "ssl://imperium.mail.pairserver.com";
         $port = "465";
         $to = $email;
         $username = "outbound@imperium.ph";
         $password = "imperiummail";

         /**$host = "smtp.mandrillapp.com";
         $port = "587 ";
         $to = $email;
         $username = "mayet.cardenas@iia-p.org";
         $password = "ZUwm_S2vRHiKW0IRgJXybg";**/

         $headers = array ('From' => $from,
           'To' => $to,
           'Subject' => $subject,
             'Content-type' => 'multipart/mixed; boundary = "'.$mime_boundary.'"',
             'MIME-Version' => 1.0);
         $smtp = Mail::factory('smtp',
           array ('host' => $host,
             'port' => $port,
             'auth' => true,
             'username' => $username,
             'password' => $password,));


         $message = " \n\n" .
                    "--{$mime_boundary}\n" .
                    "Content-Type: text/html;charset=\"ISO-8859-1\n" .
                    "Content-Transfer-Encoding: 7bit\n\n" .
                    "\n\n".
                    "$body";
 
        $data = chunk_split(base64_encode($data));
        $message .= "--{$mime_boundary}\n" .
                    "Content-Type: {$fileatttype};\n" .
                    " name=\"{$pdfFilename}\"\n" .
                    "Content-Disposition: attachment;\n" .
                    " filename=\"{$pdfFilename}\"\n" .
                    "Content-Transfer-Encoding: base64\n\n" .
                    $data . "\n\n" .
                    "-{$mime_boundary}-\n";
 
        $mail = $smtp->send($to, $headers, $message);
        echo "Sending mail to: $to". "<br>";
        if (PEAR::isError($mail)) {
          echo ("<p>" . $mail->getMessage() . "</p>");
         } else {
          echo ("<p>Message successfully sent!</p>");
         }

}
?>
