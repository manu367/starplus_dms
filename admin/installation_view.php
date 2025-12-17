<?php

//tadaApprovalPage.php?id=RVhQLzIwMjUwOTAzL1NVVVNSMDYxLzAwMDE=&pid=51&hid=FN10
require_once("../config/config.php");

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Request");
}

$id = intval($_GET['id']); // 🔒 basic security
$sql = "SELECT ind.*, au.name FROM installation_data ind LEFT JOIN admin_users au ON ind.userid = au.username WHERE ind.id = $id LIMIT 1";
$res = mysqli_query($link1, $sql);
$recordFound = true;
if (mysqli_num_rows($res) == 0) {
    $recordFound = false;
}else{
    $row = mysqli_fetch_assoc($res);
}

?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['actiontaken'])) {
        die("Action missing");
    }

    $actiontaken = $_POST['actiontaken'];

    // 🔒 Validate action
//    var_dump($actiontaken);exit();
    if ($actiontaken !== "1" && $actiontaken !== "2") {
        die("Invalid action value");
    }

    // 🎯 Map status
    $action = ($actiontaken === "1") ? "Approved" : "Rejected";

    // 🔥 UPDATE QUERY
    $sqlupdate = "
        UPDATE installation_data 
        SET status = '$action' 
        WHERE id = $id 
        LIMIT 1
    ";

    if (mysqli_query($link1, $sqlupdate)) {

        if (mysqli_affected_rows($link1) > 0) {
            header("Location: installations_register.php?pid=154&hid=FN10&msg=Status updated successfully");
            exit();
        } else {
            header("Location: installations_register.php?pid=154&hid=FN10&msg=No change detected");
            exit();
        }

    } else {
        die("Update failed: " . mysqli_error($link1));
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=siteTitle?></title>
    <script src="../js/jquery.min.js"></script>
    <link href="../css/font-awesome.min.css" rel="stylesheet">
    <link href="../css/abc.css" rel="stylesheet">
    <script src="../js/bootstrap.min.js"></script>
    <link href="../css/abc2.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/bootstrap.min.css">

    <style>
        #loadingOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.85);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loading-box {
            text-align: center;
            color: #2c3e50;
            font-size: 16px;
            font-weight: 600;
        }

        .loading-box i {
            margin-bottom: 10px;
        }

        /* The Modal (background) */
        .modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 50px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background-color: rgb(0,0,0); /* Fallback color */
            background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            height: 50%;
            margin-top: 20px;
        }

        /* The Close Button */
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
    </style>

    <script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>

<div class="container-fluid">
    <div class="row content">
        <?php
        include("../includes/leftnav2.php");
        ?>
        <?php if(!$recordFound): ?>
            <div class="col-sm-9">
                <div class="row" style="margin-top:60px;">
                    <div class="col-xs-12 col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

                        <div class="panel panel-default text-center" style="border-top:4px solid #d9534f;">
                            <div class="panel-body" style="padding:40px 20px;">

                                <div style="font-size:60px;color:#d9534f;margin-bottom:15px;">
                                    <i class="fa fa-file-text-o"></i>
                                </div>

                                <h3 style="margin-top:0;font-weight:600;color:#333;">
                                    Installation Record Not Found
                                </h3>

                                <p style="color:#777;font-size:15px;max-width:420px;margin:15px auto;">
                                    We couldn’t find the installation record you’re looking for.
                                    It may have been deleted, moved, or the link might be incorrect.
                                </p>

                                <hr style="max-width:120px;margin:25px auto;">

                                <a href="installations_register.php?pid=154&hid=FN10"
                                   class="btn btn-primary btn-sm">
                                    <i class="fa fa-arrow-left"></i> Back to Installations
                                </a>

                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <?php return; endif; ?>
        <div class="col-sm-9">
            <h3 class="text-center">
                <i class="fa fa-check-circle"></i> Installation Approval
            </h3>

            <h5 class="text-center">Document No: <?= htmlspecialchars($row['document_no']) ?></h5>
            <p class="text-center">
                Entry: <?= $row['entry_date'] ?> <?= $row['entry_time'] ?>
            </p>

            <!-- ================= ENGINEER INFO ================= -->
            <div class="panel panel-info">
                <div class="panel-heading">Engineer / Customer Info</div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tr><td>Engineer</td><td><?= $row['name'] ?></td></tr>
                        <tr><td>Customer</td><td><?= $row['customer_Name'] ?></td></tr>
                        <tr><td>Mobile</td><td><?= $row['mobile_no'] ?></td></tr>
                        <tr><td>Email</td><td><?= $row['email'] ?></td></tr>
                        <tr>
                            <td>Address</td>
                            <td>
                                <?= $row['address'] ?>,
                                <?= $row['city'] ?>,
                                <?= $row['state'] ?> - <?= $row['pincode'] ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ================= INSTALLATION INFO ================= -->
            <div class="panel panel-info">
                <div class="panel-heading">Installation Details</div>
                <div class="panel-body">
                    <table class="table table-bordered">
                        <tr><td>Product Code</td><td><?= $row['product_code'] ?></td></tr>
                        <tr><td>Serial No</td><td><?= $row['serial_no'] ?></td></tr>
                        <tr><td>Invoice No</td><td><?= $row['invoice_no'] ?></td></tr>
                        <tr><td>Installation Date</td><td><?= $row['installation_date'] ?></td></tr>
                        <tr>
                            <td>Status</td>
                            <td><strong><?= $row['status'] ?></strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- ================= ACTION ================= -->
            <?php if ($row['status'] === 'Pending'): ?>
                <form method="post" id="approvalForm" action="">
                    <div class="panel panel-warning">
                        <div class="panel-heading">Approval Action</div>
                        <div class="panel-body">
                            <table class="table">
                                <tr>
                                    <td width="40%">Action <span class="text-danger">*</span></td>
                                    <td>
                                        <select name="actiontaken" class="form-control" required>
                                            <option value="0">-- Select Action --</option>
                                            <option value="1">Approved</option>
                                            <option value="2">Rejected</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" class="text-center">
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <i class="fa fa-check"></i> Update
                                        </button>
                                        <a href="installations_register.php?pid=154&hid=FN10"
                                           class="btn btn-default">
                                            Back
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <a href="installations_register.php?pid=154&hid=FN10"
                   class="btn btn-primary">
                    Back
                </a>
            <?php endif; ?>

        </div>
        <!--close col-sm-9-->
    </div><!--close row content-->
</div><!--close container-fluid-->
<!-- The Modal -->
<div id="myModal" class="modal">
    <!-- Modal content -->
    <div class="modal-content">
        <span class="close">&times;</span>
        <p id="img" style="text-align: center;"></p>
    </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
<script>
    // Get the modal
    var modal = document.getElementById('myModal');

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks the button, open the modal
    function getThisValue(i) {
        var img = $("#image"+i).attr('src');
        $("#img").html('<img src="'+img+'" width="550px"/>');
        modal.style.display = "block";
    }
    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }

    //        $(document).on('click',"#myBtn1",function(){
    //
    //        });
</script>
</body>
</html>