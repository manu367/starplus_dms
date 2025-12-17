<?php
////// Function ID ///////
$fun_id = array("a"=>array(6));
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
$date=date("Y-m-d");
///// get access location ///
$accesslocation=getAccessLocation($_SESSION['userid'],$link1);
///// get access state
$accessstate = getAccessState($_SESSION['userid'],$link1);
$f_state = "";
if($_SESSION["userid"]=="admin"){                              
	if($_REQUEST['from_state']){ 
		$pst_state = explode("~",$_REQUEST['from_state']); 
		$stat = " AND state='".$pst_state[0]."'";
		$f_state = $pst_state[0];
		$f_statec = $pst_state[1];
	}else{ 
		$stat = "";
		$f_state = "";
		$f_statec = "";
	}
}else{
	if($_REQUEST['from_state']){ 
		$pst_state = explode("~",$_REQUEST['from_state']); 
		$stat = " AND state='".$pst_state[0]."'";
		$f_state = $pst_state[0];
		$f_statec = $pst_state[1];
	}else{ 
		$stat = " AND state IN (".$accessstate.")";
		$f_state = "";
		$f_statec = "";
	}
}
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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
 <script src="../js/bootstrap-select.min.js"></script>
 <script type="text/javascript">
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

<script src="../js/bootstrap-datepicker.js"></script>
 <link rel="stylesheet" href="../css/datepicker.css">
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-shopping-basket"></i>&nbsp;Purchase Report </h2><br/>
                         
   <div class="panel-group">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="post">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
             
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="fdate"  id="fdate" style="width:;" value="<?php if($_REQUEST['fdate']!='') {echo $_REQUEST['fdate'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-2 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="tdate"  id="tdate" value="<?php if($_REQUEST['tdate']!='') {echo $_REQUEST['tdate'];} else{echo $date;}?>" style="width:;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
           
          </div>
        </div>
        <div class="form-group">
		  <div class="col-md-10">
		  <label class="col-md-3 control-label">From Location State</label>
            <div class="col-md-3">
                <select name="from_state" id="from_state" class="form-control" onChange="document.frm1.submit();" >
                        <option value="" selected="selected">All</option>
                        <?php
						if($_SESSION["userid"]=="admin"){                              
                        	$sql_state = "SELECT state,code FROM state_master WHERE 1 ORDER BY state";
						}else{
							$sql_state = "SELECT state,code FROM state_master WHERE 1 AND state IN (".$accessstate.") ORDER BY state";
						}
                        $res_state = mysqli_query($link1,$sql_state);
                        while($row_state = mysqli_fetch_array($res_state)){
                        ?>
                        <option value="<?=$row_state['state']."~".$row_state['code']?>" <?php if($row_state['state']."~".$row_state['code']==$_REQUEST['from_state'])echo "selected";?>><?=$row_state['state']?></option>
                        <?php
                        }
                        ?>
                    </select>
            </div>
         
         <label class="col-md-2 control-label">RSM</label>
            <div class="col-md-3">
				<?php
				 $sql = mysqli_query($link1, "SELECT username,name,oth_empid FROM admin_users WHERE 1 AND designationid='10' ORDER BY name");
				  ?>
                  <select name="rsm_id" id="rsm_id"  data-live-search="true" class="form-control selectpicker">
                      <option value="" <?php if($_REQUEST['rsm_id']==""){ echo "selected"; } ?> >All</option>
					  <?php while($row = mysqli_fetch_assoc($sql)){ ?>
					  <option value="<?=$row['username']?>" <?php if($_REQUEST['rsm_id']==$row['username']){ echo "selected"; } ?> ><?php echo $row['username']." | ".$row['name']." | ".$row['oth_empid']; ?></option>
					  <?php } ?>
                  </select>
            </div>
			</div>
         
	    </div><!--close form group-->
		<div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Purchase Order From</label>
              <div class="col-md-8">
                 <select name="po_from" id="po_from" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">All</option>
                    <?php
						if($_SESSION["userid"]=="admin"){                              
                        	$sql_chl="SELECT asc_code, name, city, state, id_type from asc_master WHERE 1 ".$stat." ORDER BY name";
						}else{
							$sql_chl="SELECT asc_code, name, city, state, id_type from asc_master WHERE asc_code IN (".$accesslocation.") ".$stat." ORDER BY name";
						}
                        $res_chl=mysqli_query($link1,$sql_chl);
                        while($result_chl=mysqli_fetch_array($res_chl)){
                        ?>
                        <option value="<?=$result_chl['asc_code']?>" <?php if($result_chl['asc_code']==$_REQUEST['po_from'])echo "selected";?>><?=$result_chl['name']." | ".$result_chl['city']." | ".$result_chl['state']." | ".$result_chl['asc_code']?></option>
                        <?php
                        }
                        ?>
                    </select>
              </div>
            </div>
          </div>
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Purchase Order From</label>
              <div class="col-md-8">
                 <select name="po_to" id="po_to" class="form-control selectpicker" data-live-search="true" onChange="document.frm1.submit();">
                    <option value="" selected="selected">All</option>
                        <?php                                 
                        $smfm_sql = "SELECT a.asc_code, a.name, a.city, a.state, a.id_type FROM asc_master a, purchase_order_master b WHERE a.asc_code=b.po_to AND b.po_from='".$_REQUEST['po_from']."' GROUP BY b.po_to";
                        $smfm_res = mysqli_query($link1,$smfm_sql);
                        while($smfm_row = mysqli_fetch_array($smfm_res)){
                        ?>
                        <option value="<?=$smfm_row['asc_code']?>" <?php if($smfm_row['asc_code']==$_REQUEST['po_to'])echo "selected";?>><?=$smfm_row['name']." | ".$smfm_row['city']." | ".$smfm_row['state']." | ".$smfm_row['asc_code']?></option>
                        <?php
                        }
                        ?>
                 </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
		  <div class="col-md-10">
		  <label class="col-md-3 control-label">Product Sub Cat</label>
            <div class="col-md-3">
                <select  name='product_subcat' id="product_subcat" class='form-control selectpicker' data-live-search="true"  onChange="document.frm1.submit();">
                  <option value=''>All</option>
				  <?php
				$sql_psc = "SELECT psubcatid, prod_sub_cat FROM product_sub_category ";
				$res_psc = mysqli_query($link1,$sql_psc);
				while($row_psc = mysqli_fetch_array($res_psc)){
			    ?>
				  <option value="<?=$row_psc['psubcatid']?>"<?php if($_REQUEST['product_subcat']==$row_psc['psubcatid']){echo 'selected';}?>><?=$row_psc['prod_sub_cat']?></option>
				<?php
                }
				?>
               </select>
            </div>
         
         <label class="col-md-2 control-label">PO Type</label>
            <div class="col-md-3">
                <select  name='po_type' id="po_type" class='form-control' onChange="document.frm1.submit();">
                  <option value=''>All</option>
	           <option value="PRIMARY"<?php if($_REQUEST["po_type"]=="PRIMARY"){ echo "selected";}?>>PRIMARY</option>
	           <option value="SECONDARY"<?php if($_REQUEST["po_type"]=="SECONDARY"){ echo "selected";}?>>SECONDARY</option>
               </select>
            </div>
			</div>
         
	    </div><!--close form group-->
		 

           <div class="form-group">
		  <div class="col-md-10">
		  <label class="col-md-3 control-label">Product:</label>
            <div class="col-md-3">
                <select  name='product' id="product" class='form-control selectpicker' data-live-search="true"  onChange="document.frm1.submit();">
                  <option value=''>Please Select</option>
				  <?php
				  if($_REQUEST['product_subcat']){ $pscstr = " AND productsubcat='".$_REQUEST['product_subcat']."'";}else{ $pscstr = "";}
				$model_query="SELECT * FROM product_master WHERE 1 ".$pscstr;
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?=$br['productcode']?>"<?php if($_REQUEST['product']==$br['productcode']){echo 'selected';}?>><?=getProduct($br['productcode'],$link1)?></option>
				<?php
                }
				?>
               </select>
            </div>
         
         <label class="col-md-2 control-label">Status</label>
            <div class="col-md-3">
                <select  name='status' id="status" class='form-control selectpicker required' data-live-search="true"  onChange="document.frm1.submit();">
                  <option value=''>Please Select</option>
	           <option value="Processed"<?php if($_REQUEST['status']=="Processed"){ echo "selected";}?>>Processed</option>
	           <option value="Pending"<?php if($_REQUEST['status']=="Pending"){ echo "selected";}?>>Pending</option>
			   <option value="Dispatch"<?php if($_REQUEST['status']=="Dispatch"){ echo "selected";}?>>Dispatch</option>
               <option value="Approved"<?php if($_REQUEST['status']=="Approved"){ echo "selected";}?>>Approved</option>
               <option value="Rejected"<?php if($_REQUEST['status']=="Rejected"){ echo "selected";}?>>Rejected</option>
               <option value="PFA"<?php if($_REQUEST['status']=="PFA"){ echo "selected";}?>>PFA</option>
               <option value="Cancelled"<?php if($_REQUEST['status']=="Cancelled"){ echo "selected";}?>>Cancelled</option>
               </select>
            </div>
			 <label class="col-md-1 control-label"></label>
            <div class="col-md-1">
               <input name="Submit" type="submit" class="btn btn-primary" value="GO"  title="Go!">
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST['pid']?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST['hid']?>"/>
            </div>
         
	    </div><!--close form group-->
          </div>
        <div class="form-group">
		  <div class="col-md-6"><label class="col-md-5 control-label"></label>	  
			<div class="col-md-5">
               <?php
			    //// get excel process id ////
				//$processid=getExlCnclProcessid("PO",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
					if(isset($_REQUEST['Submit']) && ($_REQUEST['Submit']=='GO')){
			   ?>
              <div class="col-md-6" style="color:#FF0033"> <a href="excelexport.php?rname=<?=base64_encode("podetail")?>&rheader=<?=base64_encode("Purchase Order Detail")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&from_stat=<?=base64_encode($f_state)?>&from_statcod=<?=base64_encode($f_statec)?>&po_from=<?=base64_encode($_REQUEST['po_from'])?>&po_to=<?=base64_encode($_REQUEST['po_to'])?>&pro=<?=base64_encode($_REQUEST['product'])?>&sta=<?=base64_encode($_REQUEST['status'])?>&product_subcat=<?=base64_encode($_REQUEST['product_subcat'])?>&po_type=<?=base64_encode($_REQUEST['po_type'])?>&rsm_id=<?=base64_encode($_REQUEST['rsm_id'])?>" title="Export po detail  in excel"><i class="fa fa-file-excel-o fa-2x" title="Export po in excel"></i> Po Detail</a></div>
               
			 <div class="col-md-6" style="color:#FF0033">  <a href="excelexport.php?rname=<?=base64_encode("summerizepo")?>&rheader=<?=base64_encode("Summerize PO")?>&fdate=<?=base64_encode($_REQUEST['fdate'])?>&tdate=<?=base64_encode($_REQUEST['tdate'])?>&from_stat=<?=base64_encode($f_state)?>&from_statcod=<?=base64_encode($f_statec)?>&po_from=<?=base64_encode($_REQUEST['po_from'])?>&po_to=<?=base64_encode($_REQUEST['po_to'])?>&pro=<?=base64_encode($_REQUEST['product'])?>&sta=<?=base64_encode($_REQUEST['status'])?>&product_subcat=<?=base64_encode($_REQUEST['product_subcat'])?>&po_type=<?=base64_encode($_REQUEST['po_type'])?>&rsm_id=<?=base64_encode($_REQUEST['rsm_id'])?>" title="Export summerize podata  in excel"><i class="fa fa-file-excel-o fa-2x" title="Export summerize podata  in excel"></i> Summerize Po Data </a></div>
             
			   <?php
					}
				//}
				?>
            </div>
          </div>
	    </div><!--close form group-->
         </form>
		
		 
  
       
  </div><!--close panel group-->
  
   
 </div><!--close col-sm-9-->
</div><!--close row content-->
</div><!--close container-fluid-->
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>