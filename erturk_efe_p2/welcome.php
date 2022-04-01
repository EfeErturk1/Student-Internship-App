<?php
// welcome page, directed after being logged in, can see the applications
include("config.php");
session_start();

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $given_cid = $_POST['given_cid'];
    $sid = $_SESSION['sid'];

    // cancelling application query
    $deletion_query = "DELETE FROM apply WHERE sid ='$sid' AND cid='$given_cid'";
    $result1 = mysqli_query($db,$deletion_query);

    // quota of the company should be increased after deleting application
    $quota_update_query = "UPDATE company SET quota = quota + 1 WHERE cid='$given_cid'";
    $result2 = mysqli_query($db,$quota_update_query);

    if (!$result1 && !$result2) {
        printf("An error occured: %s\n", mysqli_error($db));
        exit();
    }else{
        echo "<script LANGUAGE='JavaScript'>
            window.alert('Your application is successfully cancelled');
            window.location.href = 'welcome.php'; 
        </script>";
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
          integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
        th, td { text-align: left; padding: 5px;  }
        p { margin-bottom: 12px; }
    </style>
</head>
<body>
<div class="container">
    <nav class="navbar navbar-expand-md navbar-dark bg-dark">
        <h5 class="navbar-text">Student Internship App</h5>
        <div class="navbar-collapse collapse w-100 order-3 dual-collapse2">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>
        </div>

    </nav>
    <div class="panel container-fluid" style={padding:20px}>
        <h3 class="page-header" style="font-weight: bold; margin: 20px">Applied Internships</h3>
        <?php
        $query = "SELECT * FROM student NATURAL JOIN apply NATURAL JOIN company WHERE sid = " .$_SESSION['sid'];

        echo "<p><b>Student ID:</b> " . $_SESSION['sid'] . "</p>";

        $result = mysqli_query($db, $query);

        if (!$result) {
            printf("An error occured: %s\n", mysqli_error($db));
            exit();
        }

        echo "<table class=\"table table-lg table-striped\">
            <tr>
            <th>Company ID</th>
            <th>Company Name</th>
            <th>Quota</th>
            <th>GPA Threshold</th>
            <th>Application</th>
            </tr>";

        while($row = mysqli_fetch_array($result)) {
            echo "<tr>";
            echo "<td>" . $row['cid'] . "</td>";
            echo "<td>" . $row['cname'] . "</td>";
            echo "<td>" . $row['quota'] . "</td>";
            echo "<td>" . $row['gpathreshold'] . "</td>";
            echo "<td> <form action=\"\" METHOD=\"POST\">
                    <button type=\"submit\" name = \"given_cid\"class=\"btn btn-danger btn-sm\" value =".$row['cid'] .">Cancel</button>
                    </form>
                  </td>";
            echo "</tr>";
        }
        echo "</table>";
        ?>
    </div>
    <p><a href="apply.php" class="btn btn-success">Apply for Internship</a></p>
</div>
</body>
</html>