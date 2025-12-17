<?php
require_once("../config/config.php");
////// final submit form ////
@extract($_POST);

if($_POST['submit']=="Save"){

	 if ($_FILES["attchfile"]["name"]) {
	require_once "../includes/simplexlsx.class.php";
	$xlsx = new SimpleXLSX( $_FILES['attchfile']['tmp_name'] );	
	move_uploaded_file($_FILES["attchfile"]["tmp_name"],"../upload/imei_check/".$now.$_FILES["attchfile"]["name"]);
	$f_name=$now.$_FILES["attchfile"]["name"];
	
	
	list($cols) = $xlsx->dimension();	
	foreach( $xlsx->rows() as $k => $r) {
	 if ($k == 0 ) continue; // skip first row 
	  for( $i = 0; $i < count($k); $i++)
	  {
		  /// check excel row data
	      if($r[0]==''){
			  
		  }
		  else if($r[0]=="EOF"){
		       $eof="1";
		  }else{
	      ////Make Variable for each element of excel//////
		  
		  $imei1="".$r[0];
		 
		 if($var=='')
		 {
	      $var.=$imei1;
		 }
		 else
		 {
			 $var.=','.$imei1;
		 }
		  }
	  }
	}//Close For loop
	//// check excel file is completely uploaded///
	
	
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
    <div class="col-sm-9 tab-pane fade in active table-responsive" id="home">
      <h2 align="center"><i class="fa fa-tablet"></i> Check<?=$imeitag?></h2>
      <div style="display:inline-block;float:right"><a href="../templates/check_imei.xlsx" title="Download Excel Template"><img src="../img/template.png" title="Download Excel Template"/></a></div>
      <form name="check_imei" id="check_imei" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
      
    
          <div class="form-group">
                                                <div class="col-md-12" >
                                             
                                           <label class="col-md-4 control-label">Upload<?=$imeitag?></label> 
                                           
                                           <div class="col-md-6">
                                         <input type="file" name="attchfile" id="attchfile" value="" class="form-control"/>    </div>    
                                              </div>  </div>
          
        
                                                
                                                 <div class="form-actions" align="center">
												<div class="row">
													<div class="col-md-12">
														<button class="btn btn-primary" type="submit" name="submit" value="Save">
                                                      
															<!--<i class="fa fa-save"></i>-->
															Show
														</button>
                                                       
													</div>
												</div>
											</div>
                                            
                                            
				
										</form>
                                        
                                        
           <?php if($var!='')
		   {?>                              
      <form class="form-horizontal" role="form">
       
       
      <div class="form-group"  id="page-wrap" style="margin-left:10px;"><br/><br/>
      <div style="float:right">
        <strong>Excel Export</strong>&nbsp;&nbsp;&nbsp;&nbsp; <a href="excelexport.php?rname=<?=base64_encode("imei_check")?>&imei=<?php echo $var;?>" title="Export details in excel" style="float:right"><i class="fa fa-file-excel-o fa-2x" title="Export details in excel"></i></a><br/><br/> </div>
        
               
       <table  width="98%" id="myTable" class="table-striped table-bordered table-hover" align="center">
          <thead>
            <tr>
             <th width="5%" data-class="expand"><a href="#" name="entity_id" title="asc" ></a>S.No</th>
        	  <th width="15%" data-hide="phone,tablet"><a href="#" name="name" title="asc" class="not-sort"></a><?=$imeitag?>1</th>
              <th width="10%" data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a><?=$imeitag?>2</th>
              <th width="17%"><a href="#" name="name" title="asc" ></a>Invoice No</th>
              <th width="17%"><a href="#" name="name" title="asc" ></a>Sale Date</th>
               <th width="17%"><a href="#" name="name" title="asc" ></a>Owner</th>
              <th width="17%" data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>Model</th>
              
            </tr>
          </thead>
          <tbody>
            <?php
			$sno=0;
			
			$ex=explode(',', $var);
			for($i=0; $i<count($ex); $i++)
			{
			$sql=mysqli_query($link1,"Select a.*, b.sale_date, b.type from billing_imei_data a, billing_master b where a.imei1='".$ex[$i]."' or a.imei2='".$ex[$i]."' and a.doc_no=b.challan_no"); 
			 $sno=$sno+1;
			
				  /// bill to party
			if(mysqli_num_rows($sql)>0)
			{	
			$row=mysqli_fetch_assoc($sql);
			
			 $chek_owner=mysqli_fetch_assoc(mysqli_query($link1,"select a.owner_code,a.doc_no, a.prod_code, b.sale_date from billing_imei_data a, billing_master b where (a.imei1='".$row['imei1']."' or a.imei2='".$row['imei2']."') and a.doc_no=b.challan_no order by a.id desc limit 0,1"));
				 
				  $chek_rcvin=mysqli_fetch_assoc(mysqli_query($link1,"select status from billing_master where challan_no='".$chek_owner['doc_no']."'"));
				  if($chek_rcvin['status']==""){
				  $chek_rcvin2=mysqli_fetch_assoc(mysqli_query($link1,"select status from opening_stock_master where doc_no='".$chek_owner['doc_no']."'"));
					  $checkstatus=$chek_rcvin2['status'];
				  }else{
					 $checkstatus=$chek_rcvin['status'];
				  }
			      if($chek_owner['owner_code']==$row['owner_code'] && $checkstatus=="Received" &&  $row['type']!='RETAIL' ){
					  $locdet=explode("~",getLocationDetails($chek_owner['owner_code'],"name,city,state,id_type",$link1));
	              	  $proddet=str_replace("~",",",getProductDetails($chek_owner['prod_code'],"productname,productcolor",$link1));
				  }
				  else
				  {
					  $locdet=explode("~",getLocationDetails($chek_owner['owner_code'],"name,city,state,id_type",$link1));
	              	  $proddet=str_replace("~",",",getProductDetails($chek_owner['prod_code'],"productname,productcolor",$link1));
				  }
				  
			?>
            <tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $row['imei1'];?></td>
              <td><?php echo $row['imei2'];?></td>
              <td><?php echo $chek_owner['doc_no'];?></td>
              <td><?php echo dt_format($chek_owner['sale_date']);?></td>
               <td><?php echo  $locdet[0];?></td>
              <td><?php echo str_replace("~",",",getProductDetails($chek_owner['prod_code'],"productname,productcolor",$link1));?></td>
             </tr>
            <?php }else{?>
			<tr class="even pointer">
              <td><?php echo $sno;?></td>
              <td><?php echo $ex[$i];?></td>
               <td></td>
                <td></td>
                 <td></td>
               <td align="center"></td>
               <td align="center"><span style="color:#FF0000;"><?php echo "Record Not Found";?></span></td> 
             </tr>
			<?php  }}?>
          </tbody>
          </table>
      </div>
      </form>
      <?php } ?>
    </div>
    
  </div>
</div>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>