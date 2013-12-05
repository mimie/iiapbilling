<?php
   
   include 'dbcon.php';
   include 'badges_functions.php';

   $event1 = searchEvent("e");
   $event2 = searchEvent("Meeting");
   $event3 = searchEvent("karen");

   /*var_dump($event1);
   var_dump($event2);
   var_dump($event3);*/

   echo displaySearchEvent($event1);
   echo displaySearchEvent($event2);
   echo displaySearchEvent($event3);

   //var_dump(getAllEmails());

   //$participants = searchParticipantPerEvent("43","mhy");
   //$participants = searchParticipantPerEvent("43","a");
   $participants = searchParticipantPerEvent("43","michael");
   echo "<pre>";
   echo var_dump($participants);
   echo "</pre>";

   $result = displaySearchParticipant($participants,"43");
   echo $result;

   $searchParticipantForm = searchParticipantForm();
   echo $searchParticipantForm;

   $participant = getParticipantDetails('2983');
   var_dump($participant);

   $properties = array();
   
   $participant1 = getParticipantDetails('3306');
   $participant2 = getParticipantDetails('3276');
   $properties["bHeight"] = '205px';
   $properties["bWidth"] = '329px'; 
   $properties["imgHeight"] = '77';
   $properties["imgWidth"] = '73';
   $properties["titleSize"] = '4';
   $properties["nameSize"] = '5';
   $properties["orgSize"] = '4';
   $properties["dateSize"] = '4';

   $htmlBadge = htmlBadge('139',$participant,$properties);
   var_dump($htmlBadge);

   $htmlBadge2 = htmlBadge('139',$participant2,$properties);
   var_dump($htmlBadge2);

   $speakerContactId = getSpeakerContactId(71);
   var_dump($speakerContactId);

   $speakerName = getParticipantName($speakerContactId);
   var_dump($speakerName);

   $certification = getCertification(933);
   var_dump($certification);

   $certification = identifyCertification("CIACPA");
   var_dump($certification);

   $certification2 = identifyCertification("CIA");
   var_dump($certification2);

   $certification3 = identifyCertification("CPA");
   var_dump($certification3);

   $certification4 = identifyCertification("CRMACIA");
   var_dump($certification4);
   
?>
