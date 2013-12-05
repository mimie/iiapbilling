<?php


function updateSendBill(PDO $dbh,$contactId,$eventId){

  $sql = $dbh->prepare("UPDATE billing_details 
                        SET send_bill = '1'
                        WHERE contact_id = '$contactId' AND event_id = '$eventId'");
  $sql->execute();
  
}

function getDetailsForSending(PDO $dbh, array $contactIds, $eventId){

  $sentDetails = array();
  $eachDetail = array();
  foreach($contactIds as $id){
     $email = getContactEmail($dbh,$contactId);
     $eachDetail["email"] = $email;
     $eachDetail["contact_id"] = $id;
     $eachDetail["event_id"] = $eventId;

     $sentDetails[] = $eachDetail;
     unset($eachDetail);
     $eachDetail = array();
  }

  return $sentDetails;
   
}

function generateBillingPDF($html,$billingNo){

   require_once("dompdf/dompdf_config.inc.php");
 
   $fileName = "billing_".$billingNo.".pdf";
   $fileLocation = "pdf/".$fileName;
 
   $dompdf = new DOMPDF();
   $dompdf->load_html($html);
   $dompdf->set_paper('Letter','portrait');
 
   $dompdf->render();
   file_put_contents($fileLocation, $dompdf->output( array("compress" => 0) ));
}

function getEmailIndividualBilling(PDO $dbh, $contactId,$eventId){

  $sql = $dbh->prepare("SELECT email FROM billing_details 
                         WHERE contact_id = '$contactId' AND event_id = '$eventId'");
  $sql->execute();
  $details = $sql->fetch(PDO::FETCH_ASSOC);
  $email = $details["email"];

  return $email;

  
}



?>
