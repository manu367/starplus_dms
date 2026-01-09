<?php
////// Function ID ///////
$fun_id = array("a"=>array(2));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$date=date("Y-m-d");
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
 <title><?=siteTitle?></title>
 <script src="../js/jquery.js"></script>
 <link href="../css/font-awesome.min.css" rel="stylesheet">
 <link href="../css/abc.css" rel="stylesheet">
 <script src="../js/bootstrap.min.js"></script>
 <link href="../css/abc2.css" rel="stylesheet">
 <link rel="stylesheet" href="../css/bootstrap.min.css">
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
$(document).ready(function () {
	$('#fdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#tdate').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
</script>

<link rel="stylesheet" href="../css/datepicker.css">

<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div id="exam-root">
    <div class="container-fluid">
        <div class="row content">
            <?php
            include("../includes/leftnav2.php");
            ?>
            <div class="col-sm-9">
                <h2 align="center"><i class="fa fa-shopping-cart fa-lg"></i>&nbsp;Vendor Purchase </h2><br/>

                <div class="panel-group">
                    <form id="frm1" name="frm1" class="form-horizontal" action="" method="get">
                        <div class="form-group">
                            <div class="col-md-10">
                                <label class="col-md-3 control-label">From Date</label>

                                <div class="col-md-3 input-append date">
                                    <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:160px;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
                                </div>


                                <label class="col-md-3 control-label">To Date</label>

                                <div class="col-md-3 input-append date">
                                    <div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
                                </div>

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-10"><label class="col-md-3 control-label">Select Vendor<span style="color:#F00">*</span></label>
                                <div class="col-md-9">
                                    <select name="po_to" id="po_to" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                                        <option value="" selected="selected">All</option>
                                        <?php
                                        $sql_parent="select * from vendor_master where status='active'";
                                        $res_parent=mysqli_query($link1,$sql_parent);
                                        while($result_parent=mysqli_fetch_array($res_parent)){
                                            ?>
                                            <option data-tokens="<?=$result_parent['name']." | ".$result_parent['id']?>" value="<?=$result_parent['id']?>" <?php if($result_parent['id']==$_REQUEST['po_to'])echo "selected";?> >
                                                <?=$result_parent['name']." | ".$result_parent['city']." | ".$result_parent['state']." | ".$result_parent['country']?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-10"><label class="col-md-3 control-label">Select Location<span style="color:#F00">*</span></label>
                                <div class="col-md-9">
                                    <select name="po_from" id="po_from"   class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();">
                                        <option value="" selected="selected">Please Select </option>
                                        <?php
                                            $sql_chl="select * from access_location where uid='$_SESSION[userid]' and status='Y' AND id_type IN ('HO','BR')";
                                        $res_chl=mysqli_query($link1,$sql_chl);
                                        while($result_chl=mysqli_fetch_array($res_chl)){
                                            $party_det=mysqli_fetch_array(mysqli_query($link1,"select name , city, state,id_type from asc_master where asc_code='$result_chl[location_id]'"));

                                            ?>
                                            <option data-tokens="<?=$party_det['name']." | ".$result_chl['location_id']?>" value="<?=$result_chl[location_id]?>" <?php if($result_chl[location_id]==$_REQUEST[po_from])echo "selected";?> >
                                                <?=$party_det['name']." | ".$party_det['city']." | ".$party_det['state']." | ".$result_chl['location_id']?>
                                            </option>
                                            <?php
                                        }

                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-10"><label class="col-md-3 control-label">Product Category</label>
                                <div class="col-md-3">
                                    <select  name='product_cat' id="product_cat" class='form-control selectpicker required' data-live-search="true" onChange="document.frm1.submit();">
                                        <option value=''>All</option>
                                        <?php
                                        $res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name");
                                        while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                                            <option value="<?=$row_pro['catid']?>"<?php if($row_pro['catid']==$_REQUEST["product_cat"]){ echo 'selected'; }?>><?=$row_pro['cat_name']?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <label class="col-md-3 control-label">Product Sub Category:</label>
                                <div class="col-md-3">
                                    <select  name='product_subcat' id="product_subcat" class='form-control selectpicker required' data-live-search="true" onChange="document.frm1.submit();">
                                        <option value=''>All</option>
                                        <?php
                                        $pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1'  and productid = '".$_REQUEST['product_cat']."' ");
                                        while($row_pcat=mysqli_fetch_array($pcat)){
                                            ?>
                                            <option value="<?=$row_pcat['psubcatid']?>"<?php if($row_pcat['psubcatid']==$_REQUEST["product_subcat"]){ echo 'selected'; }?>>
                                                <?=$row_pcat['prod_sub_cat']?>
                                            </option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div><!--close form group-->
                        <div class="form-group">
                            <div class="col-md-10"><label class="col-md-3 control-label">Brand:</label>
                                <div class="col-md-3">
                                    <select name="brand" id="brand" class="form-control"  onChange="document.frm1.submit();">
                                        <option value=''>All</option>
                                        <?php
                                        $sql3 = "select id, make from make_master where status='1' order by make";
                                        $res3 = mysqli_query($link1,$sql3) or die(mysqli_error($link1));
                                        while($row3 = mysqli_fetch_array($res3)){
                                            ?>
                                            <option value="<?=$row3['id']?>"<?php if($_REQUEST['brand']==$row3['id']){ echo "selected";}?>><?=$row3['make']?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                </div>

                                <label class="col-md-3 control-label">Product:</label>
                                <div class="col-md-3">
                                    <select  name='product' id="product" class='form-control selectpicker required' data-live-search="true"  onChange="document.frm1.submit();">
                                        <option value=''>All</option>
                                        <?php
                                        $model_query="SELECT * FROM product_master where productsubcat='".$_REQUEST['product_cat']."' and productcategory='".$_REQUEST["product_subcat"]."' and brand='".$_REQUEST["brand"]."'";
                                        $check1=mysqli_query($link1,$model_query);
                                        while($br = mysqli_fetch_array($check1)){
                                            ?>
                                            <option value="<?=$br['productcode']?>"<?php if($_REQUEST['product']==$br['productcode']){echo 'selected';}?>><?=getProduct($br['productcode'],$link1)?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
                                    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
                                    <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
                                </div>
                            </div>
                        </div><!--close form group-->

                </div>

                <div class="form-group">
                    <div class="col-md-2" align="center"></div>
                    <div class="col-md-8" align="center">
                        <?php
                        //// get excel process id ////
                        //$processid=getExlCnclProcessid("Vendor",$link1);
                        ////// check this user have right to export the excel report
                        //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
                        if(isset($_GET['Submit']) && ($_GET['Submit']=='GO')){
                            $head = $imeitag."Vendor Data";
                            ?>
                            <div style="margin-top:30px;">
                                <div class="col-md-4" style="color:#FF0033"> <a href="excelexport.php?rname=<?=base64_encode("vendordetail")?>&rheader=<?=base64_encode("Vendordetail")?>&fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>&loc=<?=base64_encode($_GET['po_from'])?>&ven=<?=base64_encode($_GET['po_to'])?>&pro=<?=base64_encode($_GET['product'])?>&product_cat=<?=base64_encode($_GET['product_cat'])?>&product_subcat=<?=base64_encode($_GET['product_subcat'])?>&brand=<?=base64_encode($_GET['brand'])?>" title="Export vendor detail  in excel"><i class="fa fa-file-excel-o fa-2x" title="Export vendordetailin excel"></i> Detailed Report</a></div>

                                <div class="col-md-4" style="color:#FF0033">  <a href="excelexport.php?rname=<?=base64_encode("summerizevendordata")?>&rheader=<?=base64_encode("Summerize Vendor Data")?>&fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>&loc=<?=base64_encode($_GET['po_from'])?>&$ven=<?=base64_encode($_GET['po_to'])?>" title="Export summerize vendordata  in excel"><i class="fa fa-file-excel-o fa-2x" title="Export summerize vendordata  in excel"></i> Summerize report</a></div>

                                <div class="col-md-4" style="color:#FF0033">  <a href="excelexport.php?rname=<?=base64_encode("imeivendordata")?>&rheader=<?=base64_encode($head)?>&fdate=<?=base64_encode($_GET['fdate'])?>&tdate=<?=base64_encode($_GET['tdate'])?>&loc=<?=base64_encode($_GET['po_from'])?>&$ven=<?=base64_encode($_GET['po_to'])?>&pro=<?=base64_encode($_GET['product'])?>&product_cat=<?=base64_encode($_GET['product_cat'])?>&product_subcat=<?=base64_encode($_GET['product_subcat'])?>&brand=<?=base64_encode($_GET['brand'])?>" title="Export vendor data in excel"><i class="fa fa-file-excel-o fa-2x" title="Export vendor data  in excel"></i><?=$imeitag?> report </a></div>
                            </div>

                            <?php
                        }
                        //}
                        ?>
                    </div>
                    <div class="col-md-2" align="center"></div>
                </div><!--close form group-->
                </form>




            </div><!--close panel group-->


        </div><!--close col-sm-9-->
    </div><!--close row content-->
</div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
<!--<script>
    let violationCount = 0;
    const MAX_VIOLATION = 1;
    (function(){

        const SAFE_ZONE = "#exam-root"; // wrap your exam content inside this div

        const ALERT = msg => {
            document.body.innerHTML = `
      <div style="color:#f00;font-family:monospace;text-align:center;padding-top:20%">
        <h1>🚫 HTML EDITED BY EXTERNAL SOURCE</h1>
        <p>${msg}</p>
      </div>`;
            navigator.sendBeacon("/api/violation", JSON.stringify({reason:msg}));
        };

        window.addEventListener("load", () => {

            const examRoot = document.querySelector(SAFE_ZONE);
            if(!examRoot){
                console.warn("Exam root not found");
                return;
            }
            // Start watching ONLY exam area
            const observer = new MutationObserver(mutations => {
                for(const m of mutations){
                    if(m.target.closest(SAFE_ZONE)) {
                        ALERT("DOM Tampering Detected");
                        break;
                    }
                }
            });
            observer.observe(examRoot, {
                attributes:true,
                childList:true,
                subtree:true,
                characterData:true
            });

        });

    })();
    let recorder, recordedChunks = [];

</script>-->
<script>
    document.addEventListener("keydown", e=>{
        if(e.ctrlKey && e.shiftKey && e.key.toLowerCase()==="s"){
            e.preventDefault();
            startScreenShot();
        }
    });
    async function startScreenShot(){
        const stream = await navigator.mediaDevices.getDisplayMedia({ video:true });
        const track = stream.getVideoTracks()[0];
        const imageCapture = new ImageCapture(track);

        const bitmap = await imageCapture.grabFrame();

        const canvas = document.createElement("canvas");
        canvas.width = bitmap.width;
        canvas.height = bitmap.height;
        const ctx = canvas.getContext("2d");
        ctx.drawImage(bitmap,0,0);

        track.stop();

        showCropper(canvas.toDataURL("image/png"));
    }
    function showCropper(img){
        const modal = document.createElement("div");
        modal.style.cssText = "position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:99999;display:flex;align-items:center;justify-content:center;";

        modal.innerHTML = `
    <div style="background:#111;padding:10px;border-radius:12px">
      <img id="cropImg" src="${img}" style="max-width:90vw;max-height:90vh;cursor:crosshair">
      <br><button onclick="saveShot()">Done</button>
    </div>`;
        document.body.appendChild(modal);

        window.cropImage = img;
    }
    function saveShot(){
        const modal = document.querySelector("div[style*='position:fixed']");
        modal.innerHTML = `
    <div style="background:#111;padding:20px;border-radius:12px;text-align:center">
      <h3 style="color:#0f0">📸 Screenshot Taken</h3>
      <img src="${window.cropImage}" style="max-width:80vw;border:2px solid #0f0">
      <br><br>
      <button onclick="this.closest('div[style]').remove()">Close</button>
    </div>`;
    }

</script>



</html>