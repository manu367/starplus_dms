<?php
require_once("../config/config.php");
$date=date("Y-m-d");
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
 <script type="text/javascript">
$(document).ready(function(){
    $('#myTable').dataTable();
	
});
$(document).ready(function(){
    $("#frm2").validate();
});
$(document).ready(function () {
	$('#from_date').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
$(document).ready(function () {
	$('#to_date').datepicker({
		format: "yyyy-mm-dd",
		autoclose: true
	});
});
</script>

 <link rel="stylesheet" href="../css/datepicker.css">
<script src="../js/bootstrap-datepicker.js"></script>
</head>
<body>
<div class="container-fluid">
 <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
   <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-shopping-basket"></i>&nbsp;Tertiary Report </h2><br/>
                         
   <div class="panel-group">
   <form id="frm1" name="frm1" class="form-horizontal" action="" method="get">
    <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">From Date</label>
             
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="from_date"  id="from_date" style="width:160px;" value="<?php if($_REQUEST['from_date']!='') {echo $_REQUEST['from_date'];} else{echo $date;}?>" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">To Date</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><input type="text" class="form-control span2" name="to_date"  id="to_date" value="<?php if($_REQUEST['to_date']!='') {echo $_REQUEST['to_date'];} else{echo $date;}?>"style="width:160px;" required></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
           
          </div>
        </div>
        
        <div class="form-group">
          <div class="col-md-10">
              <label class="col-md-3 control-label">Circle</label>
             
              <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><select name="state" id="state" class="form-control" style="width:150px;">
    <option value='' selected="selected">All</option>
        <?php
		$circlequery="select distinct(sale_location) from tertiary_imei_sale_import order by sale_location";
				$circleresult=mysqli_query($link1, $circlequery) or die(mysql_error());
				while($circlearr=mysqli_fetch_array($circleresult)){
				if(empty($circlearr[0]))
				continue;
				echo "<option value=\"".$circlearr[0]."\"";
				if($_REQUEST['state']==$circlearr[0]) echo " selected";
				echo ">".ucwords($circlearr[0])."</option>";
				}
			?>
            <?php /*?><option value="OTHER"<?php if ($_REQUEST['state']=='OTHER') { echo "selected"; }?>>OTHER</option><?php */?>
  </select></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
                 
              
              <label class="col-md-3 control-label">Operator</label>
              
             <div class="col-md-3 input-append date">
  					<div style="display:inline-block;float:left;"><select name="operator" id="operator" class="form-control" style="width:150px;">
<option value='' selected="selected">All</option>
        <?php
		$circlequery="select distinct(operator) from tertiary_imei_sale_import order by operator";
				$circleresult=mysqli_query($link1,$circlequery);
				while($circlearr=mysqli_fetch_array($circleresult)){
				if(empty($circlearr[0]))
				continue;
				echo "<option value=\"".$circlearr[0]."\"";
				if($_REQUEST['operator']==$circlearr[0]) echo " selected";
				echo ">".ucwords($circlearr[0])."</option>";
				}
			?>
</select></div><div style="display:inline-block;float:left;">&nbsp;<!--<i class="fa fa-calendar fa-lg"></i>--></div>
			   </div>
           
          </div>
        </div>
		
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-3 control-label">Model</label>
              <div class="col-md-9">
                  <?php $rs_model=mysqli_query($link1,"select distinct(model) from tertiary_imei_sale_import where model!='' order by model") ?>
    <select name="model" id="model" class="form-control" style="width:150px;">
      <option value="" <?php if ($_REQUEST['status']=='') { echo "selected"; }?>>All</option>
      <?php while($model=mysqli_fetch_array($rs_model)){ ?>
      <option value="<?=$model[model]?>" <?php if($_REQUEST[model]==$model[model]){ echo "selected";} ?>><?=$model[model];?></option>
	  <?php } ?>
    </select>
              </div>
            </div>
             <label class="col-md-1 control-label"></label>
            <div class="col-md-1">
              <input name="Submit" id="Submit" type="submit" class="Table" style="width:30px;" value="Go!" />
               <input name="pid" id="pid" type="hidden" value="<?=$_REQUEST[pid]?>"/>
               <input name="hid" id="hid" type="hidden" value="<?=$_REQUEST[hid]?>"/>
            </div>
          </div>
         
        <!--close form group-->
         </form>
<?php if($_REQUEST[Submit]=='Go!'){
///////////state////////////
if($_REQUEST['state']==''){
$state="";
}else{
$state="and sale_location like '$_REQUEST[state]'";
}
///////////////////////////
//////////operator/////////
if($_REQUEST['operator']==''){
$opr="";
}else{
$opr="and operator LIKE '$_REQUEST[operator]'";
}
///////////////////////////
//////////model/////////
if($_REQUEST['model']==''){
$mod="";
}else{
$mod="and model LIKE '$_REQUEST[model]'";
}
///////////////////////////
///////////keyword///////////
if($_REQUEST['srch']==''){
$srch="";
}else{
$srch=" and (imei1 like '%$_REQUEST[srch]%' or imei2 like '%$_REQUEST[srch]%' or mobile_no like '%$_REQUEST[srch]%')";
}
///////////////////////////
$sql_stat="(sale_date BETWEEN '$_REQUEST[from_date]' AND '$_REQUEST[to_date]') $state $opr $srch $mod";

	?>		
		 <div class="row">
        <div class="col-sm-12 table-responsive">
        <div style="float:right">
            <?php
			    //// get excel process id ////
				//echo $processid=getExlCnclProcessid("Inventory",$link1);
			    ////// check this user have right to export the excel report
			    //if(getExcelRight($_SESSION['userid'],$processid,$link1)==1){
			   ?>
        <strong>Excel Export</strong>&nbsp;&nbsp;&nbsp;&nbsp; <a href="excelexport.php?rname=<?=base64_encode("activation_report")?>&srch=<?=$_REQUEST[srch]?>&amp;state=<?=$_REQUEST[state]?>&amp;from=<?=$_REQUEST[from_date]?>&amp;to=<?=$_REQUEST[to_date]?>&amp;operator=<?=$_REQUEST[operator]?>&amp;model=<?=$_REQUEST[model]?>" title="Export  details in excel" style="float:right"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a><br/><br/>
        <?php
				//}
				?>
                </div>
       <table  width="99%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
              <th><a href="#" name="entity_id" title="asc" ></a>S.No</th>
              <th data-class="expand"><a href="#" name="name" title="asc" ></a>Model Name</th>
              <th><a href="#" name="name" title="asc" ></a>IMEI1</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>IMEI2</th>
              <th data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a>Activation Date</th>
              <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Location</th>
              <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Mobile No.</th>
               <th data-hide="phone,tablet"><a href="#" name="number" title="asc" class="not-sort"></a>Operator</th>
              </tr>
          </thead>
          <tbody>
            <?php 
  $sql_srch="select * from tertiary_imei_sale_import where $sql_stat order by sale_date desc";
	$res_srch=mysqli_query($link1,$sql_srch);

$count=0;
	if (mysqli_num_rows($res_srch)>0){
	while($result_srch=mysqli_fetch_array($res_srch))
	{
	$count+=1;
	
	
	?>
            <tr class="even pointer">
               <td><?=$count;?></td>
            <td><?=$result_srch['model']?></td>
            <td><?=$result_srch['imei1']?></td>
            <td><?=$result_srch['imei2']?></td>			
            <td><?=$result_srch['sale_date']?></td>
			<td><?=$result_srch['sale_location']?></td>
			<td><?=$result_srch['mobile_no']?></td>
            <td><?=$result_srch['operator']?></td>
              </tr>
            <?php } }?>
          </tbody>
          </table>
			<?php } ?> 
         </div>
      </div>
  
       
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