<?php

  include 'dbcon.php';
  include 'badges_functions.php';
  
  $contactIds = urldecode($_REQUEST['ids']);
  $contactIds = json_decode($contactIds);

  $eventId = urldecode($_REQUEST['eventId']);
  $eventId = json_decode($eventId);

  $properties = urldecode($_REQUEST['properties']);
  $properties = json_decode($properties);
  
  $properties = (object)$properties;
  $badgeWidth = $properties->bWidth."px";
  $badgeHeight = $properties->bHeight."px";

  $badgeProperties = array();

  $badgeProperties["imgHeight"] = $properties->imgHeight;
  $badgeProperties["imgWidth"] = $properties->imgWidth;

  if($properties->titleSize == 'default'){
    $badgeProperties["titleSize"] = '14pt';
  }else{
    $badgeProperties["titleSize"] = $properties->titleSize;
   }

  if($properties->nameSize == 'default'){
    $badgeProperties["nameSize"] = '18pt';
  }else{
    $badgeProperties["nameSize"] = $properties->nameSize;
  }

  if($properties->orgSize == 'default'){
    $badgeProperties["orgSize"] = '13pt';
  }else{
    $badgeProperties["orgSize"] = $properties->orgSize;
   }

  if($properties->dateSize == 'default'){
    $badgeProperties["dateSize"] = '13pt';
  }else{
    $badgeProperties["dateSize"] = $properties->dateSize;
   }

?>
<html>
<head>
<title>Customize Badge</title>
<style>
<?php

echo "#badge{"
     . "border:1px dashed #BDBDBD;"
     . "padding:2px;"
     . "width:".$badgeWidth.";"
     . "height:".$badgeHeight.";"
     . "}"
     . "table{"
     . "width:".$badgeWidth.";"
     . "height:".$badgeHeight.";"
     . "}";
?>
</style>
</head>
<body>
<?php

  $htmlBadge = "<table>";
  $indicator = 1;

  foreach($contactIds as $id){
    $participantDetails = getParticipantDetails($id);
    $perBadge = htmlCustomizeBadge($eventId,$participantDetails,$badgeProperties);
    //echo $perBadge;

    if($indicator == 1){
      $htmlBadge = $htmlBadge."<tr>"
                 . "<td>$perBadge</td>";
      $indicator = $indicator + 1;
    }

    elseif($indicator == 2){
      $htmlBadge = $htmlBadge."<td>$perBadge</td></tr>";
      $indicator = 1;
    }

  }
  
  $htmlBadge = $htmlBadge."</table>";

  echo $htmlBadge;

?>
</body>
</html>
