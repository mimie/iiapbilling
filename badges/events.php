<html>
<head>
<title>Events</title>
<link rel="stylesheet" type="text/css" href="design.css" />
<style>
#header ul {
  list-style: none;
  padding: 0;
  margin: 0;
}
	
#header li {
  display: inline;
  <!--border: 2px solid #0489B1;-->
  border-bottom-width: 0;
  margin: 0 0.5em 0 0;
}
	
#header li a {
  padding: 0 1em;
}
	
#header #selected {
  padding-bottom: 1px;
  background: #0489B1;
}

#header #recent {
  padding-bottom: 1px;
  background-color: #31B404; 
}

</style>
</head>
<body>
<div id="header">
<ul>
   <li id="recent"><a href="events.php" style="text-decoration:none;color:white;"><b>LIST OF EVENTS<b></a></li>
</ul>
</div>
<?php
     
  include 'dbcon.php';
  include 'badges_functions.php';

  echo "<div align='center' style='border: 10px solid #0489B1; padding:15px;'>";

  echo searchEventForm();

  if(!isset($_POST["searchEvent"])){
     echo displayAllEvents();
  }

  else{
     $eventName = $_POST["eventName"];
     $eventMatches = searchEvent($eventName);
     $searchResult = displaySearchEvent($eventMatches);
    
     echo $searchResult;
  }

  echo "</div>";

?>
</body>
</html>

