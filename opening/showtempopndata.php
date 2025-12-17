<?php
require_once("../config/config.php");
@extract($_GET);
///// get data 
$res_sh=mysqli_query($link1,"select count(prod_code) as qty , prod_code from temp_opn_upload where update_by='".$_SESSION['userid']."' and browserid='".$browserid."' and file_id='".$_REQUEST['file_id']."' and  flag='' group by prod_code");
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
<script language="JavaScript" type="text/JavaScript">
function hideButton(){
	document.getElementById("cancel").style.display='none';
	document.getElementById("upd").style.display='none';
}
</script>
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-upload"></i> Excel Uploaded Data For Opening Stock</h2>
      <h4 align="center" style="color:#060">Step 1 is completed (Excel file is uploaded) .</h4>
      <h4 align="center" style="color:#FF9900">Step 2 Please Go for next process or cancel uploaded data.</h4>
      <h4 align="center" style="color:#FF0000">Do Not Refersh while process is being execute.</h4>
      <form class="form-horizontal table-responsive" role="form" name="frm1" id="frm1">
       <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
              <th width="16%"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th width="34%" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Product</th>
              <th width="29%"><a href="#" name="name" title="asc" ></a>Uploaded Qty</th>
              <!--<th width="21%" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Available Qty</th>-->
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			$flag=1;
			while($row=mysqli_fetch_assoc($res_sh)){
				  $sno=$sno+1;
				  // checking row value of product and qty should not be blank
		          //$getstk=getCurrentStock($loccode,$row['prod_code'],"okqty",$link1);
				  $proddet=explode("~",getProductDetails($row['prod_code'],"productname,productcolor",$link1));
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php if($proddet[0]){ echo $proddet[0]." (".$proddet[1].")"; $flag*=1;}else{ echo "Product does not exist in DB ";$flag*=0;} echo " / ".$row['prod_code'];?></td>
              <td align="right"><?php echo $row['qty'];?></td>
              <?php /*?><td align="right"><?php if($getstk){echo $getstk;}else{ echo "0";}?></td><?php */?>
            </tr>
            <?php }?>
          </tbody>
          </table>
      <!--</div>-->
      </form>
      <form class="form-horizontal" role="form" name="frm2" id="frm2" action="processopnupld.php" method="post" onSubmit="hideButton();">
      <div class="form-group">
            <div class="col-md-12" align="center">
               <input type="submit" class="btn btn-primary" name="upd" id="upd" value="Process" title="Go To Step 2" onClick="this.style.visibility = 'hidden';" <?php if($flag==0){?> disabled <?php }?>>&nbsp;
               <input name="locationcode" id="locationcode" type="hidden" value="<?=$loccode?>"/>
               <input name="opendate" id="opendate" type="hidden" value="<?=$odate?>"/>
               <input name="fileid" id="fileid" type="hidden" value="<?=$file_id?>"/>
               <input name="fname" id="fname" type="hidden" value="<?=$f_name?>"/>
               <input name="remark" id="remark" type="hidden" value="<?=base64_decode($rmk)?>"/>
               <input type="submit" class="btn btn-primary"  name="cancel" title="Cancel Uploaded Data" id="cancel" value="Cancel" onClick="return myConfirm();"/>
            </div>
      </div>
      </form>
    </div>
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>