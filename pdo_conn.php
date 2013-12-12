<?

  function civicrmConnect(){

    $dbh = new PDO('mysql:host=10.110.215.92;dbname=iiap_civicrm_dev', 'iiap', 'mysqladmin');
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;

  }

  function weberpConnect(){

   $weberpConn = new PDO('mysql:host=10.110.215.92;dbname=IIAP_DEV','iiap','mysqladmin');
   $weberpConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
   return $weberpConn;

  }  

?>
