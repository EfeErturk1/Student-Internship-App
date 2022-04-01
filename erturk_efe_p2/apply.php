<?php
// apply page, can apply to companies in here
include("config.php");
session_start();

$no_of_application_query = "SELECT COUNT(*) AS cnt FROM apply WHERE sid =" . $_SESSION['sid'];
$result = mysqli_query($db, $no_of_application_query);

if (!$result) {
    printf("An error occured: %s\n", mysqli_error($db));
    exit();
}
$row = mysqli_fetch_array($result);
$input_success = true;
$num_of_application = $row['cnt'];
// checing if the number of application of the student is more than 3 or not
if($num_of_application >= 3){
    $input_success = false;
    echo "<script LANGUAGE='JavaScript'>
          window.alert('You already have applied for 3 internships');
          window.location.href='welcome.php';
       </script>";
}

if($_SERVER["REQUEST_METHOD"] == "POST") {
    $input_success = true;
    $given_cid = $_POST['cid'];
    $student_id = $_SESSION['sid'];

    $wrong_cid = "SELECT COUNT(*) AS cnt  FROM company WHERE cid='$given_cid'";

    $result = mysqli_query($db, $wrong_cid);
    if (!$result) {
        printf("An error occured: %s\n", mysqli_error($db));
        exit();
    }
    // checking whether the company exists
    $count = mysqli_fetch_array($result)['cnt'];
    if($count == 0){
        $input_success = false;
        echo "<script LANGUAGE='JavaScript'>
            window.alert('No such company exists. Please enter a valid company id');
            window.location.href = 'apply.php'; 
        </script>";
    }


    //checking whether student tries to apply to the company which is already in his applications
    $already_applied_query = "SELECT COUNT(*) as cnt FROM apply WHERE sid IN (SELECT sid FROM apply WHERE cid ='$given_cid' AND sid ='$student_id')";
    $result = mysqli_query($db,$already_applied_query);
    if (!$result) {
        printf("An error occured: %s\n", mysqli_error($db));
        exit();
    }
    $row = mysqli_fetch_array($result);
    $application_count = $row['cnt'];
    if($application_count >= 1){
        $input_success = false;
        echo "<script LANGUAGE='JavaScript'>
            window.alert('You have already applied for this company.');
            window.location.href = 'apply.php'; 
        </script>";
    }


    // checking whether quota is available for given company
    $quota_query = "SELECT quota FROM company WHERE cid='$given_cid'";
    $result = mysqli_query($db,$quota_query);
    if (!$result) {
        printf("An error occured: %s\n", mysqli_error($db));
        exit();
    }
    $row = mysqli_fetch_array($result);
    $quota_count = $row['quota'];

    if($quota_count == 0){
        $input_success = false;
        echo "<script LANGUAGE='JavaScript'>
            window.alert('Sorry, application quota is full for this company.');
            window.location.href = 'apply.php'; 
        </script>";
    }

    // checking whether students gpa is more than the threshold of the company
    $gpathreshold_query = "SELECT gpathreshold FROM company WHERE cid='$given_cid'";
    $result = mysqli_query($db,$gpathreshold_query);
    if (!$result) {
        printf("An error occured: %s\n", mysqli_error($db));
        exit();
    }
    $row = mysqli_fetch_array($result);
    $gpathreshold = $row['gpathreshold'];

    $gpa_query = "SELECT gpa FROM student WHERE sid ='$student_id'";
    $result = mysqli_query($db,$gpa_query);
    if (!$result) {
        printf("An error occured: %s\n", mysqli_error($db));
        exit();
    }
    $row = mysqli_fetch_array($result);
    $gpa = $row['gpa'];

    if($gpathreshold > $gpa){
        $input_success = false;
        echo "<script LANGUAGE='JavaScript'>
            window.alert('Sorry, you do not meet the GPA threshold for this company.');
            window.location.href = 'apply.php'; 
        </script>";
    }

    if($input_success == true){
        // if all the checks are passed, then perform the application and add the application to the database
        $update_quota_of_company = "UPDATE company SET quota = quota -1 WHERE cid = '$given_cid'";
        $result = mysqli_query($db,$update_quota_of_company);
        if (!$result) {
            printf("An error occured: %s\n", mysqli_error($db));
            exit();
        }

        $insert_into_apply_table_query= "INSERT INTO apply VALUES ('$student_id','$given_cid')";
        $result = mysqli_query($db,$insert_into_apply_table_query);
        if (!$result) {
            printf("An error occured: %s\n", mysqli_error($db));
            exit();
        }else{
            echo "<script LANGUAGE='JavaScript'>
            window.alert('You have succesfully applied for this company.');
            window.location.href = 'welcome.php'; 
        </script>";
        }
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply</title>
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
                    <a class="nav-link" href="welcome.php">Go Back</a>
                </li>
            </ul>
        </div>

    </nav>
    <div class="panel container-fluid">
        <h3 class="page-header" style="font-weight: bold; padding: 20px">Apply For a Company</h3>
        <?php
        echo "<table class=\"table table-lg table-striped\">
        <tr>
            <th>Company ID</th>
            <th>Company Name</th>
            <th>GPA Threshold</th>
            <th>Quota</th>
        </tr>";

        $query ="SELECT * FROM company as c,student as s WHERE quota > 0 AND s.gpa > c.gpathreshold AND sid =" . $_SESSION['sid']." AND       
        NOT EXISTS (SELECT * FROM apply WHERE c.cid = cid AND sid =" . $_SESSION['sid'].")";

        if (!$query) {
            printf("An error occured: %s\n", mysqli_error($db));
            exit();
        }

        $result = mysqli_query($db, $query);

        while($row = mysqli_fetch_array($result)){
            echo "<tr>";
            echo "<td>" . $row['cid'] . "</td>";
            echo "<td>" . $row['cname'] . "</td>";
            echo "<td>" . $row['gpathreshold'] . "</td>";
            echo "<td>" . $row['quota'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        ?>
    </div>

    <form METHOD="POST">
        <div class = "form-row" style = "display: flex; justify-content: center;">
            <input type="text"  class="form-control col-md-4" name="cid" placeholder="Enter Company ID">
            <button type="submit" class="btn btn-success btn-sm">Submit</button>
        </div>
    </form>
</div>