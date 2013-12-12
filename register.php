<html>
<head>
<meta charset="utf-8">
<title>Register</title>
  <link rel="stylesheet" type="text/css" href="menu.css">
<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css">
<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
</head>
<body>
<?php
  include 'login_functions.php';
  include 'pdo_conn.php';
 
  $dbh =  civicrmConnect();

  session_start();
  //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  @$userId = $_GET["user"];

  echo "<div align='center' style='padding:6px;'>";  
  $logout = logoutDiv($dbh,$userId);
  echo $logout;
  echo "</div>";

  echo "<div align='center' style='padding:6px;'>";
  $header = headerDiv();
  echo $header;
  echo "</div>";
?>
  <div align = 'center' style='padding:10px;'>
  <table border = '3' style='border-collapse:collapse;border-color:#0174DF;border-style:ridge;'>
   <tr>
    <td>
      <form id="registration" name="register" method="post">
      <table style='padding:10px;'>
        <tr>
         <td align='right'><b>Firstname</b></td>
         <td align='left'><input type="text" name="firstname" placeholder="firstname" required /></td>
        </tr>
        <tr>
         <td align='right'><b>Middlename</b></td>
         <td align='left'><input type="text" name="middlename" placeholder="middlename" required /></td>
        </tr>
        <tr>
         <td align='right'><b>Lastname</b></td>
         <td align='left'><input type="text" name="lastname" placeholder="lastname" required /></td>
        </tr>
        <tr>
         <td align='right'><b>Designation</b></td>
         <td align='left'><input type="text" name="designation" placeholder="designation" required /></td>
        </tr>
        <tr>
          <td align='right'><b>Username:</b></td>
          <td align='left'><input type="text" name="username" placeholder="username" required /></td>
        </tr>
        <tr>
         <td align='right'><b>Password:</b></td>
         <td align='left'><input type="password" name="pass1" placeholder="password" required /></td>
        </tr>
        <tr>
         <td align='right'><b>Password again:</b></td>
         <td align='left'><input type="password" name="pass2" placeholder="password" required /></td>
        </tr>
        <tr>
         <td></td>
         <td align='right'><input id="register" type="submit" value="Register" name='register' /></td>
        </tr>
      </form>
      </table>
    </td>
   </tr>
  </table>
  </div>
<?php

  if(isset($_POST["register"])){
  
    $username = $_POST['username'];
    $pass1 = $_POST['pass1'];
    $pass2 = $_POST['pass2'];
    $firstname = $_POST['firstname'];
    $middlename = $_POST['middlename'];
    $lastname = $_POST['lastname'];
    $designation = $_POST['designation'];
  
    if($pass1 != $pass2)
       header('Location: register.php');
    if(strlen($username) > 30)
       header('Location: register.php');

    $hash = hash('sha256', $pass1);
    $salt = createSalt();
    $hash = hash('sha256', $salt . $hash);


    $registration = array(
                    "username" => "$username",
                    "hash" => "$hash",
                    "salt" => "$salt",
                    "firstname" => "$firstname",
                    "middlename" => "$middlename",
                    "lastname" => "$lastname",
                    "designation" => "$designation",
    );

    insertUser($dbh,$registration);
    //header('Location: login.php'); 
   
  }

?>

</body>
<script>
  $("#registration").submit(function(){
     
    var username = $('input:text[name=username]').val();
    alert("You have succcessfully registered an account for username "+username+".");
  });

</script>
</html>
