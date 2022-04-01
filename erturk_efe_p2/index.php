<?php
// initial page, login
include("config.php");
session_start();

$usr = "";
$pw = ""; // id


if($_SERVER["REQUEST_METHOD"] == "POST") {
    $usr = mysqli_real_escape_string($db,$_POST['username']);
    $pw = mysqli_real_escape_string($db,$_POST['password']);

    //sql query for checking inputs and finding corresponding student
    $query = "SELECT sname, sid FROM student WHERE sname = ? and sid = ?";
    if($stmt = mysqli_prepare($db, $query)){
        // Bind variables to the prepared statement as parameters
        mysqli_stmt_bind_param($stmt, "ss", $entered_usr, $entered_pw);

        $entered_usr = $usr;
        $entered_pw = $pw;

        if(mysqli_stmt_execute($stmt)){
            mysqli_stmt_store_result($stmt);
            //checking if sid and sname is true
            if(mysqli_stmt_num_rows($stmt) == 1){
                mysqli_stmt_bind_result($stmt, $usr, $response_pw);

                if(mysqli_stmt_fetch($stmt)){
                    if($response_pw == $pw){ //inputs are correct
                        session_start();
                        // start the session of the user
                        $_SESSION['sname'] = $usr;
                        $_SESSION['sid'] = $pw;
                        header("location: welcome.php");
                    }
                }
            }else{ //wrong credentials
                echo "<script type='text/javascript'>
                window.alert('Invalid Login Credentials');
                </script>";
            }
        }
    }

    // Close statement
    mysqli_stmt_close($stmt);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; }
        #mainwrapper { text-align: center; padding: 100px; }
        #maindiv { display: flex; justify-content: space-evenly; flex-direction: column; align-items: center; 
            align-content:center; border-style: solid; border-width: 1px; border-color: black; }
    </style>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-inverse bg-primary navbar-fixed-top">
        <div class="container-fluid">
            <div class="navbar-header">
                <h4 class="navbar-text">Student Internship App</h4>
            </div>
        </div>
    </nav>
    <div id="mainwrapper">
        <div id="maindiv">
            <h2>Login to Internship System</h2>
            <p>Please enter your username and password</p>
            <form id="loginForm" method="post">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" class="form-control" id="username">

                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" id="password">

                </div>
                <div class="form-group">
                    <input onclick="check()" class="btn btn-primary" value="Login">
                </div>
            </form>
        </div>
    </div>
</div>


<script type="text/javascript">
    function check() {
        var usernameValue = document.getElementById("username").value;
        var passwordValue = document.getElementById("password").value;
        if (usernameValue === "" || passwordValue === "") {
            alert("Please fill in username and password fields");
        }
        else {
            var form = document.getElementById("loginForm").submit();
        }
    }
</script>
</body>
</html>