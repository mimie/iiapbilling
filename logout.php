<?php

  include 'login_functions.php';

  session_start();
   //if the user has not logged in
  if(!isLoggedIn())
  {
    header('Location: login.php');
    die();
  }

  logout();
  header('Location: login.php');

?>
