<?php
require_once("../config/config.php");
$date=date("Y-m-d");
@extract($_POST);
////// final submit form ////

$folder="imei";
$allowedExts = array("xlsx");

if($_POST['Submit']=='Upload'){
	
	
	
   if($_FILES['attach']['name']!='' )
	{
		include("excel_reader/reader.php");
	  $temp = explode(".", $_FILES["attach"]["name"]);
	 $extension = end($temp);
	 $f_size=$_FILES["attach"]["size"];
	  
	  if(!in_array($extension, $allowedExts))
	 {
		 $msgg=".".$extension." ". "not allowed";
		 header("Location:imei_upload.php?msg=$msgg&sts=fail");
	 }
	 
	
	 
	 
	 if ($_FILES["attach"]["size"]>2097152)
	 {
		 $msgg="File size should be less than or equal to 2 mb";
		 header("Location:imei_upload.php?msg=$msgg&sts=fail");
	 }
	 
	 
	else
	{ 
$xlsx = new SimpleXLSX( $_FILES['attach']['tmp_name'] );	
	//echo '<h2>Result: File has been Uploaded </h2>'.$k ;
	list($cols,) = $xlsx->dimension();
    $file_name = $_FILES['attach']['name'];
	$file_tmp =$_FILES['attach']['tmp_name'];
	$up=move_uploaded_file($file_tmp,"../".$folder."/".time().$file_name);
    $path1="../".$folder."/".time().$file_name;	
	$img_name1=time().$file_name;
foreach( $xlsx->rows() as $k => $r) {
	
if ($k == 0) continue; // skip first row 
		$count=1;
		
		
			if(($r[0])!='' ){
				
		    $bill=mysqli_query($link1,"Select * from billing_imei_data where imei1='".$r[0]."' or imei2='".$r[0]."'");
			
			if(mysqli_num_rows($bill)>0)
			{
			//echo $r[0];
	$sql=mysqli_query($link1,"insert into imeiupload_master set scheme_no='".$scheme."',attach_imei='".$path1."',entry_date='$date',entry_by='$_SESSION[userid]' ")or die("ER4".mysqli_error($link1)); 
   $instid=mysqli_insert_id($link1);
   if(($instid)>0)
   {
	$query=mysqli_query($link1,"insert into imeiupload_data set id='".$instid."',scheme_no='".$scheme."',imei_no='".$r[0]."',entry_date='$date',entry_by='$_SESSION[userid]' ")or die("ER4".mysqli_error($link1));    
   }
		}
  else{
			$msg="You have  uploded wrong IMEI ";
			header("Location:imei.php?msg=".$msg."".$pagenav);
			exit;
		}
			}
			
}

    ////// insert in activity table////
	dailyActivity($_SESSION['userid'],$scheme,"IMEI","UPLOAD",$ip,$link1);
    //return message
	$msg="You have successfully uploded IMEI ";
	///// move to parent page
   header("Location:imei.php?msg=".$msg."".$pagenav);
	exit;
 }	
	
 	
  	

		
	
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
 <link rel="stylesheet" href="../css/jquery.dataTables.min.css">
 <script type="text/javascript" src="../js/jquery.dataTables.min.js"></script>
 <script type="text/javascript" src="../js/ajax.js"></script>
 <script>
$(document).ready(function(){
    $('#myTable').dataTable();
});
</script>
<script>
	$(document).ready(function(){
        $("#frm1").validate();
    });
 </script>
 <script>
var _validFileExtensions = [".xlsx"];    
function Validate(oForm) {
    var arrInputs = oForm.getElementsByTagName("input");
    for (var i = 0; i < arrInputs.length; i++) {
        var oInput = arrInputs[i];
        if (oInput.type == "file") {
            var sFileName = oInput.value;
            if (sFileName.length > 0) {
                var blnValid = false;
                for (var j = 0; j < _validFileExtensions.length; j++) {
                    var sCurExtension = _validFileExtensions[j];
                    if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                        blnValid = true;
                        break;
                    }
                }
                
                if (!blnValid) {
                    alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
                    return false;
                }
            }
        }
    }
  
    return true;
}
</script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9 tab-pane fade in active" id="home">
      <h2 align="center">IMEI Upload</h2>
      <?php if($_REQUEST[msg]){?><br>
      <h4 align="center" style="color:#FF0000"><?=$_REQUEST[msg]?></h4>
      <?php }?>
	  <form  name="frm1"  id="frm1"  class="form-horizontal" action="" method="post" enctype="multipart/form-data" onsubmit="return Validate(this);">
          
          <div class="form-group">
           <div class="col-md-6"><label class="col-md-6 control-label">Select Scheme No.<span class="red_small">*</span></label>
              <div class="col-md-6">
                                 <select name="scheme" class="form-control" id="scheme" required>
                                                  <option value="">--Please Select --</option>
                                                  <?php $dept=mysqli_query($link1,"select * from scheme_master ");while($dres=mysqli_fetch_assoc($dept)){?>
                            					<option value="<?php echo $dres['scheme_no'];?>" ><?php echo ucwords($dres['scheme_no']); ?></option>
                                                <?php } ?>
                   								 </select>            
              </div>
            </div>
			
			 
          </div>
          
         <div class="form-group">
           
            <div class="col-md-6"><label class="col-md-6 control-label"> Attach Excel File <span class="red_small">*</span> </label>
			      <div class="col-md-6">
                 <input type="file" name="attach" id="attach" required />
				 </div>
               </div>
			           
          <div class="col-md-6"> <strong>Download Format</strong> <a href="excel_xlsx.php" ><i class="fa fa-file-excel-o fa-2x" title="Export imei details in excel"></i></a></div>
		  </div>
		  
          <div class="form-group">
            <div class="col-md-12" align="center">
              
              <input type="submit" class="btn btn-primary" name="Submit" id="" value="Upload" title="submit">
             
              
             <!-- <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='scheme_master.php?<?=$pagenav?>'"> -->
            </div>
          </div>
    </form>
      <form class="form-horizontal" role="form">
	   <div class="col-md-12"><label class="col-md-5 control-label"></label>	  
			

      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
       <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
              <th><a href="#" name="entity_id" title="asc" ></a>IMEI No.</th>
              <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a>Scheme No.</th>
              <th><a href="#" name="name" title="asc" ></a>Status</th>
              <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Remarks</th>
              
             
			  
              
            </tr>
          </thead>
          <tbody>
             <?php 
			 
			 
			$sql1 = "SELECT * FROM imeiupload_data where entry_by='".$_SESSION['userid']."' ";
       $rs1 = mysqli_query($link1,$sql1) or die(mysqli_error($link1));
	   while($row1=mysqli_fetch_assoc($rs1)) {
		 //  echo "select* from billing_imei_data where owner_code='".$row1['entry_by']."' and  (imei1 ='".$row1['imei_no']."' or  imei2='".$row1['imei_no']."' )";
        $query=mysqli_query($link1,"select* from billing_imei_data where owner_code='".$row1['entry_by']."' and  (imei1 ='".$row1['imei_no']."' or  imei2='".$row1['imei_no']."' ) ");
		$query1=(mysqli_fetch_assoc($query));
        if(mysqli_num_rows($query)>0)
			{
	   ?>
	    <tr class="even pointer">
		<td><?php echo $row1['imei_no']?><div align="left"></div></td>
		<td><div align="left">&nbsp;<?php echo $row1['scheme_no']?></div></td>
          <td><div align="left">&nbsp;<?php echo "Approved" ;?></div></td>
		  <td><div align="left">&nbsp;<?php echo "Distributor Code  Matched" ;?></div></td>
         
		               </tr>
          <?php } else{?>
		   <tr class="even pointer">
		<td><?php echo $row1['imei_no']?><div align="left"></div></td>
		<td><div align="left">&nbsp;<?php echo $row1['scheme_no']?></div></td>
          <td><div align="left">&nbsp;<?php echo "Rejected" ;?></div></td>
		  <td><div align="left">&nbsp;<?php echo "Distributor Code Not Matched" ;?></div></td>
                       </tr>
	   <?php 
	 
			 }} ?>
	   
	  
          </tbody>
          </table>
      </div>
      </form>
    </div>
    
  </div>
</div>
</div>
<?php
include("../includes/footer.php");
?>
</body>
</html>