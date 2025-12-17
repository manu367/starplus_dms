<?php
require_once("../config/config.php");

////// final submit form ////
@extract($_POST);
if($_POST['Submit']=="Upload"){
mysqli_autocommit($link1, false);
$flag = true;
if ($_FILES["file"]["error"] > 0)
{
$code=$_FILES["file"]["error"];
}
else
{
move_uploaded_file($_FILES["file"]["tmp_name"],
"../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"]);
$filen=$today.$_FILES["file"]["name"];
$file="../ExcelExportAPI/upload/".$today.$_FILES["file"]["name"];
chmod ($file, 0755);
}

$product_cat = $_POST['category'];
$product_sub_cat = $_POST['psubcategory'];
$brand = $_POST['brand'];
$productcode = $_POST['product'];
$filename=$filen;
$filepath=$file;
////////////////////////////////////////////////// code to import file/////////////////////////////////////////////////////////////
error_reporting(E_ALL ^ E_NOTICE);
 $path = '../ExcelExportAPI/Classes/';
        set_include_path(get_include_path() . PATH_SEPARATOR . $path);//we specify the path" using linux"
        function __autoload($classe)
        {
            $var = str_replace
            (
                '_', 
                DIRECTORY_SEPARATOR,
                $classe
            ) . '.php' ;
            require_once($var);
        }   

 $indentityType = PHPExcel_IOFactory::identify($filepath);
                $object = PHPExcel_IOFactory::createReader($indentityType);
                $object->setReadDataOnly(true);
                $objPHPExcel = $object->load($filepath);
         
                $sheet = $objPHPExcel->getSheet(0); //we specify the sheet to use
                $highestRow = $sheet->getHighestRow();//we select all the rows used in the sheet 
                $highestCol = $sheet->getHighestColumn();// we select all the columns used in the sheet
                $indexCol = PHPExcel_Cell::columnIndexFromString($highestCol); //////// count no. of column 
				$highest = $objPHPExcel->setActiveSheetIndex(0)->getHighestRow(); //////////////// count no of rows in excel
				
                //importing files to the database
                for($row =2; $row <= $highest; $row++)
                {
                    $imei1 = $sheet->getCellByColumnAndRow(0,$row)->getValue();
                    $imei2 = $sheet->getCellByColumnAndRow(1,$row)->getValue();
					$year = $sheet->getCellByColumnAndRow(2,$row)->getValue();
					$month = $sheet->getCellByColumnAndRow(3,$row)->getValue();
					$date = $sheet->getCellByColumnAndRow(4,$row)->getValue();
					$operator = $sheet->getCellByColumnAndRow(5,$row)->getValue();
					$state = $sheet->getCellByColumnAndRow(6,$row)->getValue();
					
					$saledate = $year."-".$month."-".$date;
                    //inserting query into data base
					$sql = "INSERT INTO imei_activation (product_cat, product_sub_cat, brand, productcode, imei1, imei2, operator, state,  path, file_name, sale_date, upload_date , update_by ) VALUES ( '".$product_cat."', '".$product_sub_cat."', '".$brand."', '".$productcode."', '".$imei1."', '".$imei2."', '".$operator."', '".$state."', '".$filepath."', '".$filename."', '".$saledate."', '".$today."', '".$_SESSION['userid']."' )";
					$result =	mysqli_query($link1,$sql);
				  //// check if query is not executed
				  if (!$result) {
					   $flag = false;
					   echo "Error details : " . mysqli_error($link1) . ".";
				   }		   	   
			   }////// end of for loop
	   
	   if ($flag) {
        mysqli_commit($link1);
		$cflag = "success";
		$cmsg = "Success";
        $msg = "Successfully Uploaded ";
    } else {
		mysqli_rollback($link1);
		$cflag = "danger";
		$cmsg = "Failed";
		$msg = "Request could not be processed. Please try again.";
	} 
    mysqli_close($link1);
	///// move to parent page
	header("location:imei_activation_upld.php?msg=".$msg."&chkflag=".$cflag."&chkmsg=".$cmsg."".$pagenav);
	exit;
            
}///// end of if condition

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
 
 <script>
	
 </script>
 
 
 <link rel="stylesheet" href="../css/datepicker.css">
 <script src="../js/bootstrap-datepicker.js"></script>
<style>
.red_small{
	color:red;
}
.warning,.warning2 {
    color:#d2232a;
    -webkit-border-radius: 12px; 
    border-radius: 12px;
    background-color:#ffdd97;
    padding:5px;
    width:100%;
    display:none;
}
</style>
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
      <h2 align="center"><i class="fa fa-ticket"></i>&nbsp;<?=$imeitag?>Activation</h2>	
      <div style="display:inline-block;float:right;margin-right:50px;"><a href="../templates/IMEI_ACTIVATE.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"></a></div>
      <br></br> 
      
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      <?php if($_REQUEST['msg']){?><br>
      <div class="alert alert-<?=$_REQUEST['chkflag']?> alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            <strong><?=$_REQUEST['chkmsg']?>!</strong>&nbsp;&nbsp;<?=$_REQUEST['msg']?>.
        </div>
      <?php }?>
        <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post"  enctype="multipart/form-data">
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Product Category<span class="red_small">*</span></label>
              <div class="col-md-4">
                <select name="category"  id= "category" class="form-control required"  onChange="document.frm1.submit();" required >
					<option value="" <?php if($_REQUEST['category'] == "") { echo "selected" ;} ?> >--Please Select--</option>
				  	  <?php
                      $pcat=mysqli_query($link1,"Select catid , cat_name  from product_cat_master where status = '1' ");
                      while($row_pcat=mysqli_fetch_array($pcat)){
                      ?>
                      <option value="<?=$row_pcat['catid']?>" <?php if($_REQUEST['category'] == $row_pcat['catid']) { echo "selected" ;} ?> >
                      <?=$row_pcat['cat_name']?>
                      </option>
                      <?php
                      }
                      ?>
                </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Sub-Category<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="psubcategory"  id= "psubcategory" class="form-control required" required>
				  <option value="" <?php if($_REQUEST['psubcategory']==""){ echo "selected";}?> >--Please Select--</option>
	          	  <?php
                  $pcat=mysqli_query($link1,"Select *  from product_sub_category where status = '1'  and productid = '".$_REQUEST['category']."' ");
				  while($row_pcat=mysqli_fetch_array($pcat)){
				  ?>
                  <option value="<?=$row_pcat['psubcatid']?>" >
                  <?=$row_pcat['prod_sub_cat']?>
                  </option>
                  <?php
				  }
                  ?>
            </select>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Brand<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select name="brand" class="form-control required" required>
				   <option value="" <?php if($_REQUEST['brand']==""){ echo "selected";}?> >--Please Select--</option>
				   <?php 
                   $brand=mysqli_query($link1,"select * from make_master ");
                    while($row=mysqli_fetch_array($brand)){
                   ?>
                  
				  <option value="<?=$row['id']?>" <?php if($_REQUEST['brand']==$row['id']){ echo "selected";}?> >
                  <?=$row['make']?>
                  </option>
                  <?php
				  }
                  ?>
              </select>
              </div>
            </div>
          </div>
         
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Product<span class="red_small">*</span></label>
              <div class="col-md-4">
              <select  name='product' id="product" class='form-control required'  onChange="document.form1.submit();" required >
                  <option value='' <?php if($_REQUEST['product']==""){ echo "selected";}?> >--Please Select-</option>
				  <?php
				$model_query="SELECT * FROM product_master ";
				$check1=mysqli_query($link1,$model_query);
				while($br = mysqli_fetch_array($check1)){
			    ?>
				  <option value="<?php echo $br['productcode'];?>" <?php if($_REQUEST['product']==$br['productcode']){ echo "selected";}?> ><?=$br['productname']." | ".$br['productcolor']." | ".$br['productcode']?></option>
				<?php
                }
				?>
               </select>
              </div>
            </div>
          </div> 
		        
          <div class="form-group">
            <div class="col-md-12"><label class="col-md-4 control-label">Attach File<span class="red_small">*</span></label>
              <div class="col-md-4">
                  <div>
                    
                       <span>
                        <input type="file"  name="file"  required class="form-control"   accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"/ > 
                    </span>
                              
                </div>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-md-12" style="text-align:center;">
             	<span class="red_small">NOTE: Attach only <strong>.xlsx (Excel Workbook)</strong> file</span>
            </div>
          </div>
          
         <div class="form-group">
            <div class="col-md-12" align="center">
              <input type="submit" class="btn<?=$btncolor?>" name="Submit" id="save" value="Upload" title="" <?php if($_POST['Submit']=='Update'){?>disabled<?php }?>>
              &nbsp;&nbsp;&nbsp;
              
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