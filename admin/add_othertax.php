<?php
require_once("../config/config.php");
$today=date("Y-m-d");
////// final submit form ////
if ($_POST['Submit'] == "Save") {
    if (mysqli_query($link1, "insert into newtax_master set tax_name='".$_POST[tax_name]."', tax_per='".$_POST[tax_per]."',create_date='".$today."',	create_by='".$_SESSION[userid]."',status='".$_POST[status]."' ")or die("" . mysql_error())) {
        ////// insert in activity table////
        dailyActivity($_SESSION['userid'], $_POST['tax_name'], "Other TAX", "ADD", $ip, $link1);

        //return message
        $msg = "You have successfully created a new Tax " . $_POST['tax_name'];
    } else {
        ////// return message
        $msg = "Something went wrong. Please try again.";
    }
    ///// move to parent page
    header("Location:other_tax.php?msg=" . $msg . "" . $pagenav);
    exit;
}
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= siteTitle ?></title>
        <script src="../js/jquery.min.js"></script>
        <link href="../css/font-awesome.min.css" rel="stylesheet">
        <link href="../css/abc.css" rel="stylesheet">
        <script src="../js/bootstrap.min.js"></script>
        <link href="../css/abc2.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
        <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
        <script>
            $(document).ready(function() {
                $("#frm1").validate();
            });
        </script>
        <style>
            .red_small{
                color:red;
            }
        </style>
        <script src="../js/frmvalidate.js"></script>
        <script type="text/javascript" src="../js/jquery.validate.js"></script>
        <script type="text/javascript" src="../js/common_js.js"></script>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row content">
                <?php
                include("../includes/leftnav2.php");
                ?>
                <div class="col-sm-9">
                    <h2 align="center"><i class="fa fa-book"></i>&nbsp;&nbsp;Add New Tax</h2><br/><br/>      
                    <div class="form-group"  id="page-wrap" style="margin-left:10px;" >
                        <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post">
                            <div class="form-group">
                                <div class="col-md-6"><label class="col-md-5 control-label">Tax Name<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <input type="text" name="tax_name" class="form-control" id="tax_name" value="" required/>                 
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-5 control-label">Tax %<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <input type="text" name="tax_per" class="form-control" id="tax_per" value="" required/>
                                    </div>
                                </div> 
                            </div> 
                            <div class="form-group">
                                <div class="col-md-6"><label class="col-md-5 control-label">Status<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <select name='status' id='status' class="form-control" required/>
                                        <option value="">--Please Select--</option>
                                        <option value="Active">Active</option>
                                        <option value="Deactive">Deactive</option>
                                        </select>
                                    </div>
                                </div>            
                            </div>
                            <div class="form-group">
                                <div class="col-md-12" align="center">              
                                    <input type="submit" class="btn btn-primary" name="Submit" id="" value="Save" title="Add Tax">            
                                    <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href = 'other_tax.php?<?= $pagenav ?>'">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>    
            </div>
        </div>
        <?php
        include("../includes/footer.php");
        include("../includes/connection_close.php");
        ?>
    </body>
</html>