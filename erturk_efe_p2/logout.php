<?php
// logout page
session_start();

// reseting session array
$_SESSION = array();
session_destroy();

// Redirect to login page
echo "<script LANGUAGE='JavaScript'>
          window.location.href='index.php';
       </script>";

exit;
?>