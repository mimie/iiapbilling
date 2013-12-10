<?php

function createSalt(){

  $string = md5(uniqid(rand(), true));
  return substr($string, 0, 3);
}

function insertUser(PDO $dbh,array $registration){

   $username = $registration["username"];
   $hash = $registration["hash"];
   $salt = $registration["salt"];
   $firstname = $registration["firstname"];
   $middlename = $registration["middlename"];
   $lastname = $registration["lastname"];
   $designation = $registration["designation"];

   $sql = $dbh->prepare("INSERT INTO billing_users (username,password,salt,firstname,middlename,lastname,designation)
                         VALUES('$username','$hash','$salt','$firstname','$middlename','$lastname','$designation')
                        ");
  $sql->execute();
}

function getUserDetails(PDO $dbh,$username){
   $sql = $dbh->prepare("SELECT password, salt FROM billing_users
                         WHERE username = '$username'
                        ");
   $sql->execute();
   $userDetails = $sql->fetch(PDO::FETCH_ASSOC);

   return $userDetails;
}

function getUserId(PDO $dbh,$username){

  $sql = $dbh->prepare("SELECT id FROM billing_users
                        WHERE username = '$username'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $userId = $result["id"];

  return $userId;
}

function getUsername(PDO $dbh,$userId){

  $sql = $dbh->prepare("SELECT username FROM billing_users
                        WHERE id = '$userId'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $username = $result["username"];

  return $username;
}

function getUserFullName(PDO $dbh,$userId){

  $sql = $dbh->prepare("SELECT firstname,middlename,lastname FROM billing_users
                        WHERE id = '$userId'
                       ");
  $sql->execute();
  $result = $sql->fetch(PDO::FETCH_ASSOC);
  $firstname = $result["firstname"];
  $middlename = $result["middlename"];
  $lastname = $result["lastname"];

  $middleInitial = $middlename[0];

  $fullName = $firstname." ".$middleInitial.". ".$lastname;
  $fullName = strtoupper($fullName);
  
  return $fullName;
}

function validateUser()
{
    session_regenerate_id (); //this is a security measure
    $_SESSION['valid'] = 1;
    $_SESSION['userid'] = $userid;
}

function isLoggedIn()
{
    if(isset($_SESSION['valid']) && $_SESSION['valid'])
        return true;
    return false;
}

function logout()
{
    $_SESSION = array(); //destroy all of the session variables
    session_destroy();
}

function headerDiv(){

  $html = "<img src='header.jpg' width='100%' height='100px'>";

  return $html;

}

function logoutDiv($userId){
/**  $html = "<div align='right' width='100%' height='10px' style='background-color:black;padding:6px;'>"
        . "<a href='logout.php'>Logout</a>"
        . "</div>";**/

     

     $html = "<div width='100%' style='background-color:black; padding:1px;'>"
           . "<ul>"
           . "<li><a href='events2.php?user=$userId'>Events</a></li>"
           . "<li><a href='#'>Membership</a>"
           . "<ul><li><a href='membershipBilling.php?user=$userId'>Membership Billing</a></li></ul>"
           . "</li>"
           . "<li><a href='#'>CIA Review</a></li>"
           . "<li><a href='#'><img src='images/settings.png' width='20' height='20' style='float:left;'>&nbsp;Settings</a>"
           . "<ul>"
           . "<li><a href='account.php?user=$userId'><img src='images/my_account.png' width='20' height='20' style='float:left;'>&nbsp;My Account</a>"
           . "</li>"
           . "<li><a href='register.php?user=$userId'><img src='images/register_account.png' width='20' height='20' style='float:left;'>&nbsp;Register New Account</a>"
           . "</li>"
           . "</ul>"
           . "</li>"
           . "<li><a href='logout.php'>Logout</a></li>"
           . "</ul><br><br>"
           . "</div>";


  return $html;
}
?>
