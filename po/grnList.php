<?php
////// Function ID ///////
$fun_id = array("u"=>array(109)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$_SESSION["messageIdent"]="";
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
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
<title><?=siteTitle?></title>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
       <h2 align="center"><i class="fa fa-ship"></i> GRN List</h2>
      <?php if($_REQUEST['msg']){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST['msg']?></h4>
      <?php }?>
     
      
	            
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr class="<?=$tableheadcolor?>" >
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th>Tally Sync</th>
              <th><a href="#" name="name" title="asc" ></a>GRN From</th>
              <th><a href="#" name="name" title="asc" ></a>GRN To</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>GRN No.</th>
			  <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Invoice/VPO No.</th>
              <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>GRN Date</th>
           
              <th data-hide="phone,tablet">Attahed Invoice/POD</th>
	          <th data-hide="phone,tablet">View</th>
              <th data-hide="phone,tablet">Print</th>
              <th data-hide="phone,tablet">Cancel</th>
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			//// get cancel rights
			$isCnlRight = getCancelRightNew($_SESSION['userid'],"1",$link1);
			///// get access location ///
			$accesslocation=getAccessLocation($_SESSION['userid'],$link1);		
			$sql=mysqli_query($link1,"Select * from billing_master where to_location in (".$accesslocation.")  and type = 'GRN' order by id desc");
			while($row=mysqli_fetch_assoc($sql)){
				  $sno=$sno+1;
				 //// check serial no. is uploaded or not
				  $numrow = 0;
				  $rs12=mysqli_query($link1,"SELECT imei_attach, prod_code FROM billing_model_data WHERE challan_no='".$row['challan_no']."'");
				  $numrow = mysqli_num_rows($rs12);
				  $check=1;
				  while($row12=mysqli_fetch_array($rs12)){
					$get_result12 = explode("~",getAnyDetails($row12['prod_code'],"productcode,is_serialize","productcode" ,"product_master",$link1));
					if($get_result12[1]=='Y'){ if($row12['imei_attach']=="Y"){ $check*=1;}else{ $check*=0;}}else{ $check*=1;}
				  }
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td align="center"><?php if($row['post_in_tally2']=="Y"){?><i class="fa fa-refresh fa-lg text-success" aria-hidden="true" title="sync in tally"></i><?php }else{?><i class="fa fa-ban fa-lg  text-danger" aria-hidden="true" title="not sync in tally"></i><?php }?></td>
              <td><?php echo str_replace("~",",",getVendorDetails($row['from_location'],"name,city,state",$link1));?></td>
              <td><?php echo str_replace("~",",",getLocationDetails($row['to_location'],"name,city,state",$link1));?></td>
              <td><?php echo $row['challan_no'];?><br/><i>(<?php //if($numrow==0){ echo "Pending";}else{ echo $row['status'];}
			  echo $row['status'];?>)</i></td>
	      <td><?php echo $row['inv_ref_no'];?><br/><?php echo $row['ref_no'];?></td>
              <td><?php echo dt_format($row['sale_date']);?></td>  
                  <td align="center"><?php if($row['grn_doc']){?> <a href='<?=$row['grn_doc']?>'  title='view' target='_blank'><i class="fa fa-download fa-lg" title="view/download invoice"></i></a> <?php }?> &nbsp;&nbsp;<?php if($row['pod1']){?> <a href='<?=$row['pod1']?>'  title='view' target='_blank'><i class="fa fa-download fa-lg" title="view/download POD1"></i></a> <?php }?> &nbsp;&nbsp;<?php if($row['pod2']){?> <a href='<?=$row['pod2']?>'  title='view' target='_blank'><i class="fa fa-download fa-lg" title="view/download POD2"></i></a> <?php }?></td>        
			  <td align="center"><a href='statusgrn_view.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='view'><i class="fa fa-eye fa-lg" title="view details"></i></a></td>
           <td align="center">
            <?php  if($check==1 || $row['status']=="Cancelled"){ ?>
           <a href='../print/grn_print.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='view' target='_blank'><i class="fa fa-print fa-lg" title="view details"></i></a><?php if($row['imei_attach'] == 'Y'){?> <a href='../print/grnimei_print.php?id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='view' target='_blank'><i class="fa fa-print fa-lg" title="view details"></i></a><?php }?><?php }else{ echo "<b style='color:#F30'>Please Upload Serial No. for Serialized Product</b>";}?></td>
			   
            <td align="center"><?php
	 if($isCnlRight == 1){  if($row['status']!="Cancelled"){?><a href='cancelgrn.php?op=cancel&id=<?php echo base64_encode($row['challan_no']);?><?=$pagenav?>'  title='Cancel GRN'><i class="fa fa-trash fa-lg" title="Cancel GRN"></i></a><?php  }}?></td>
            </tr>
            <?php }?>
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