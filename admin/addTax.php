<?php
////// Function ID ///////
$fun_id = array("a"=>array(43));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
////// final submit form ////
if ($_POST['Submit'] == "Save") {
    if (mysqli_query($link1, "insert into tax_hsn_master set chapter_no='$_POST[chapter]',hsn_description='$_POST[desc]',	hsn_code='$_POST[hsn_code]',sgst='$_POST[sgst]',igst='$_POST[igst]', cgst='$_POST[cgst]',create_date='$datetime',	create_by='$_SESSION[userid]',status='$_POST[status]' ")or die("" . mysqli_error($link1))) {
        ////// insert in activity table////
        dailyActivity($_SESSION['userid'], $_POST['tax_name'], "TAX", "ADD", $ip, $link1,"");

        //return message
        $msg = "You have successfully created a new Tax " . $_POST['tax_name'];
    } else {
        ////// return message
        $msg = "Something went wrong. Please try again.";
    }
    ///// move to parent page
    header("Location:tax_master.php?msg=" . $msg . "" . $pagenav);
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
                                <div class="col-md-6"><label class="col-md-5 control-label">Chapter Number<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <input type="text" name="chapter" class="form-control" id="chapter" value="" required/>                 
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-5 control-label">HSN Code<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <input type="text" name="hsn_code" class="form-control" id="hsn_code" value="" required/>                 
                                    </div>
                                </div>          
                            </div>
                            <div class="form-group">           
                                <div class="col-md-6"><label class="col-md-5 control-label">HSN Description<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <input type="text" name="desc" class="form-control" id="desc" value="" required/>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-5 control-label">CGST %<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <input type="text" name="cgst" class="form-control" id="cgst" value="" required/>
                                    </div>
                                </div> 
                            </div>
                            <div class="form-group">           
                                <div class="col-md-6"><label class="col-md-5 control-label">SGST %<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <input type="text" name="sgst" class="form-control" id="sgst" value="" required/>
                                    </div>
                                </div>
                                <div class="col-md-6"><label class="col-md-5 control-label">IGST %<span class="red_small">*</span></label>
                                    <div class="col-md-5">
                                        <input type="text" name="igst" class="form-control" id="igst" value="" required/>
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
                                    <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href = 'tax_master.php?<?= $pagenav ?>'">
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