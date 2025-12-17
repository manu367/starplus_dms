<?php
require_once("../config/config.php");
@extract($_GET);
////// filters value/////
if(!empty($_REQUEST['from_location'])){$selfloc=$_REQUEST['from_location']; $q_floc = "from_location='".$_REQUEST['from_location']."'";}else{$selfloc=""; $q_floc = "1";}
if(!empty($_REQUEST['to_location'])){$seltloc=$_REQUEST['to_location']; $q_tloc = "to_location='".$_REQUEST['to_location']."'";}else{$seltloc=""; $q_tloc = "1";}
if(!empty($_REQUEST['prod_cat'])){$selpc=$_REQUEST['prod_cat']; $q_prodcat = "productcategory = '".$_REQUEST['prod_cat']."'";}else{$selpc=""; $q_prodcat = "1";}
//////End filters value/////

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
  <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center"><i class="fa fa-car"></i>&nbsp;Product Delivery </h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
	  <form class="form-horizontal" role="form" name="form1" action="" method="get">
	   <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> From Location:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="from_location" id="from_location" required class="form-control selectpicker" data-live-search="true">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_ch1="select city,state from district_master where status='A' group by city,state order by city,state";
					$res_ch1=mysqli_query($link1,$sql_ch1);
					while($result_ch1=mysqli_fetch_array($res_ch1)){
                          ?>
                    <option data-tokens="<?=$result_ch1['city']." | ".$result_ch1['state']?>" value="<?=$result_ch1['city']?>" <?php if($result_ch1['city']==$_REQUEST['from_location'])echo "selected";?> >
                       <?=$result_ch1['city']." | ".$result_ch1['state']?>
                    </option>
                    <?php
					}
                    ?>
                 </select>
             </div>
          </div> 
          <div class="col-md-6"><label class="col-md-5 control-label">To Location</label>	  
			<div class="col-md-5" align="left">
			    <select name="to_location" id="to_location" required class="form-control selectpicker" data-live-search="true">
                    <option value="" selected="selected">Please Select </option>
                    <?php 
					$sql_ch1="select city,state from district_master where status='A' group by city,state order by city,state";
					$res_ch1=mysqli_query($link1,$sql_ch1);
					while($result_ch1=mysqli_fetch_array($res_ch1)){
                          ?>
                    <option data-tokens="<?=$result_ch1['city']." | ".$result_ch1['state']?>" value="<?=$result_ch1['city']?>" <?php if($result_ch1['city']==$_REQUEST['to_location'])echo "selected";?> >
                       <?=$result_ch1['city']." | ".$result_ch1['state']?>
                    </option>
                    <?php
					}
                    ?>
                 </select>
            </div>
          </div>
	    </div><!--close form group-->
	    <div class="form-group">
          <div class="col-sm-6 col-md-6 col-lg-6"><label class="col-sm-5 col-md-5 col-lg-5 control-label"> Product Category:</label>
             <div class="col-sm-5 col-md-5 col-lg-5">
                <select name="prod_cat" id="prod_cat" class="form-control custom-select">
                  <option value=""<?php if($selpc==''){ echo "selected";}?>>All</option>
                  <?php
                	$res_pro = mysqli_query($link1,"select catid,cat_name from product_cat_master order by cat_name"); 
                	while($row_pro = mysqli_fetch_assoc($res_pro)){?>
                  <option value="<?=$row_pro['catid']?>"<?php if($row_pro['catid']==$selpc){ echo 'selected'; }?>><?=$row_pro['cat_name']?></option>
                  <?php } ?>
                </select>
             </div>
          </div> 
          <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5" align="left">
			    <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               	<input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
               	<input name="Submit" type="submit" class="btn <?=$btncolor?>" value="GO"  title="Go!">
            </div>
          </div>
	    </div><!--close form group-->
	  </form>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			
        <button title="Upload delivery matrix" type="button" class="btn <?=$btncolor?>" style="float:right;" onClick="window.location.href='upload_product_delivery.php?op=add<?=$pagenav?>'"><span>Upload Delivery Matrix</span></button>&nbsp;&nbsp; 
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>">
              <th style="text-align:center;" ><a href="#" name="entity_id" title="asc" ></a>#</th>
              <th style="text-align:center;" ><a href="#" name="name" title="asc" class="not-sort"></a>From Location</th>
              <th style="text-align:center;" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>To Location</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Product Category</th>
              <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Delivery Days</th>
			  <th style="text-align:center;" data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>View/Edit</th>
            </tr>
          </thead>
          <tbody>
             <?php $i=1;
		$sql1 = "SELECT * FROM product_delivery_matrix where ".$q_floc." and ".$q_tloc." and ".$q_prodcat." order by productcategory";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) { ?>
	    <tr class="even pointer">
			<td style="text-align:center;" ><?php echo $i ;?></td>
			<td><?php echo $row1['from_location']?></td>
          	<td><?php echo $row1['to_location']?></td>
          	<td align="left"><?php echo getAnyDetails($row1['productcategory'],"cat_name","catid" ,"product_cat_master"  ,$link1);?></td>
          	<td align="center"><?php echo $row1['delivery_days']?></td>
          	<td align="center"><a href='edit_product_delivery.php?op=edit&id=<?php echo base64_encode($row1['id']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
       </tr>
	   <?php 
	  $i++;
	   }?>
	   
	  
          </tbody>
          </table>
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