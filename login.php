<html>
<head>
<title>Login</title>
</head>
<body>
<?php
   include 'pdo_conn.php';
   include 'login_functions.php';
   echo "<div align='center' style='padding:16px;'>";  
   $header = headerDiv();
   echo $header;
   echo "</div>";
?>
<div align='center'>
  <table border = '3' style='border-collapse:collapse;border-color:#0174DF;border-style:ridge;'>
   <tr>
    <td style='padding:60px;'>
      <form name="login" action="login.php" method="post">
      <table>
        <tr>
          <td align='right'><b>Username:</b></td>
          <td align='left'><input type="text" name="username" placeholder="username" required /></td>
        </tr>
        <tr>
         <td align='right'><b>Password:</b></td>
         <td align='left'><input type="password" name="password" placeholder="password" required/></td>
        </tr>
        <tr>
         <td></td>
         <td align='right'><input type="submit" value="Login" name="login"/></td>
        </tr>
      </table>
      </form>
    </td>
   </tr>
  </table>
</div>
<?php
  $dbh =  civicrmConnect();
  if(isset($_POST["login"])){
     session_start();
     $username = $_POST["username"];
     $password = $_POST["password"];

     $userDetails = getUserDetails($dbh,$username);
     $hash = hash('sha256', $userDetails['salt'] . hash('sha256', $password));

     if(count($userDetails) < 1){
         echo "User does not exist.";
     }

     elseif($hash != $userDetails["password"]){
         echo "Incorrect password.";
     }

      else{
        validateUser();
        $userId = getUserId($dbh,$username);
        header("Location: events2.php?user=$userId");
      }
  }
 

?>

</body>
</html>
