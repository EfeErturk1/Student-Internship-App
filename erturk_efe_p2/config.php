<?php
// used to connect to the database
   if(!defined("server"))
        define('server', "localhost"); // localhost
   if(!defined("username"))
        define('username', "root");// root
   if(!defined("password"))
        define('password', ""); // ""
   if(!defined("database"))
        define('database', "cs353_hw4"); // cs353_hw4
   $db = mysqli_connect(server,username,password,database);

if (!$db) {
    die("Connection CANNOT be established " . mysqli_connect_error());
}
//echo "Connected successfully";
?>