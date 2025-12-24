<?php
////// Function ID ///////
$fun_id = array("u"=>array(43)); // User:
require_once("../config/config.php");
////// Access check //////
if(!access_check_v3($link1, $fun_id, $_SESSION["userid"], $_SESSION["utype"])){exit;}
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
        $("#frm1").validate();
		$('#myTable').dataTable({
    		paging: false,
			searching: false,
			ordering:  false,
			info: false
		});
    });
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
<!--    Auth ->Updated By Many Pathak-->
 <script>
        $(document).ready(function () {

            $("#serial_no").keyup(function () {
                let value = $(this).val().trim();

                if (value.length < 1) {
                    $("#serial_suggest_box").hide();
                    return;
                }

                $.ajax({
                    url: "../pagination/ajax_serial_suggest.php",
                    method: "POST",
                    data: { keyword: value },
                    success: function (data) {
                        $("#serial_suggest_box").html(data).show();
                    }
                });
            });

            // click suggestion
            $(document).on("click", ".serial-item", function () {
                $("#serial_no").val($(this).text());
                $("#serial_suggest_box").hide();
            });

        });
    </script>

</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
	<div class="col-sm-9">
    	<h2 align="center"><i class="fa fa-barcode fa-lg"></i> <?=$imeitag?>Track</h2><br/><br/>
   	  <div class="form-group"  id="page-wrap" style="margin-left:10px;">
          <form  name="frm1"  id="frm1" class="form-horizontal" action="" method="post" onSubmit="return validate() ">
          <div class="form-group">
            <div class="col-md-10"><label class="col-md-4 control-label">Enter Serial Number<span class="red_small">*</span></label>
              <div class="col-md-6">
                <input type="search"
                       class="form-control alphanumeric required"
                       placeholder=""
                       id="serial_no"
                       autocomplete="off"
                       name="serial_no" value="<?=$_REQUEST["serial_no"]?>">
                  <div id="serial_suggest_box" class="list-group" style="position:absolute;z-index:999;"></div>
              </div>
              <div class="col-md-2">
           		<input type="submit" class="btn <?=$btncolor?>" name="SHOW" id="" value="SHOW">       
           	  </div>
            </div>
          </div>
        </form>
		  <?php
        	if($_POST['SHOW']){
		  		$sql = mysqli_query($link1,"SELECT * FROM billing_imei_data WHERE imei1 ='".$_POST['serial_no']."' or imei2 ='".$_POST['serial_no']."'");
		  		$num_rows = mysqli_num_rows($sql);
		  		if($num_rows > 0){
			?>
            <div class="alert alert-success alert-dismissible" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                  </button>
                <strong>Success <i class="fa fa-check-circle fa-lg"></i></strong>&nbsp;&nbsp;Entered serial number details are showing below.
            </div>
		  <table  width="100%" id="myTable" class="table-striped table-bordered table-hover" align="center">
       		  <thead>
           		  <tr class="<?=$tableheadcolor?>" >  
                    <th><a href="#" name="entity_id" title="asc" ></a>From Location</th>
                    <th data-hide="phone"><a href="#" name="name" title="asc" class="not-sort"></a>To Location</th>
					  <th data-hide="phone">Product Code</th>
                    <th><a href="#" name="name" title="asc" ></a><?=$imeitag?></th>
                    <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a>Ref. / Invoice No.</th>
                    <th data-hide="phone,tablet"><a href="#" name="date" title="asc" class="not-sort"></a>Ref. / Invoice Date</th>
           		    <th data-hide="phone,tablet">Ref. Type</th>
           		    <th data-hide="phone,tablet">Stock Type</th>
           		  </tr>
       		  </thead>
       		  <tbody>
				  <?php
                    while($row = mysqli_fetch_assoc($sql)){
						////get document details
						$doc_date = "";
						$doc_type = "";
						$doc1 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date,type FROM billing_master where challan_no='".$row['doc_no']."'"));
						$doc_date = $doc1['entry_date'];
						if($doc1['type']=="CORPORATE"){ $doc_type="Sale Invoice Against PO";}else if($doc1['type']=="RETAIL"){ $doc_type="Sale Invoice";}else if($doc1['type']=="GRN"){ $doc_type="GRN";}else if($doc1['type']=="LP"){ $doc_type="Local Purchase";}else if($doc1['type']=="STN"){ $doc_type="STN";}else if($doc1['type']=="STN Distribution"){ $doc_type="STN";}else{$doc_type="";}
						if($doc1['entry_date']==""){
							$doc2 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM vendor_order_master where po_no='".$row['doc_no']."'"));
							$doc_date = $doc2['entry_date'];
							$doc_type="GRN";
							if($doc2['entry_date']==""){
								$doc3 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM opening_stock_master where doc_no='".$row['doc_no']."'"));
								$doc_date = $doc3['entry_date'];
								$doc_type="Opening Stock";
								if($doc3['entry_date']==""){
									$doc4 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM stockconvert_master where doc_no='".$row['doc_no']."'"));
									$doc_date = $doc4['entry_date'];
									$doc_type="Stock Convert";
								}
							}
						}
						
                    ?>
                      <tr class="even pointer">
                          <td><?php $fromLocation = str_replace("~",",",getLocationDetails($row['from_location'],"name,city,state",$link1)); if($fromLocation==""){ $fromLocation = str_replace("~",",",getVendorDetails($row['from_location'],"name,city,state",$link1));} echo $fromLocation;?></td>
                          <td><?php  $billto=getLocationDetails($row['to_location'],"name,city,state",$link1);
              $explodeval=explode("~",$billto);
              if($explodeval[0]){ $toparty=$billto; }else{ $toparty=getCustomerDetails($row['to_location'],"customername,city,state",$link1);}
              echo str_replace("~",",",$toparty);
			  ?></td>
                          <td><?php echo $row['prod_code'];?></td>
						  <td><?php echo $row['imei1'];?></td>
                          <td><?php echo $row['doc_no'];?></td>
                          <td><?php echo $doc_date; //if($doc2['entry_date']){ echo $doc2['entry_date'];}else{ echo $doc1['entry_date'];}?></td>
                          <td><?=$doc_type?></td>
                          <td><?php echo $row['stock_type'];?></td>
                      </tr>
                  <?php 
                    }
					?>
                  </tbody>  
			  </table>
              
<!------------ warranty status panel start ------------------> 
             <div class="form-group" style="margin-top:30px;">
              <div class="col-md-4">
                  <div class="panel-group">
                     <div class="panel panel-default table-responsive">
                      <div class="panel-heading heading1">Warranty Status</div>
                      <div class="panel-body" style="text-align:;">
                      	  <?php 
						  $sql2 = mysqli_query($link1,"SELECT doc_no, prod_code FROM billing_imei_data WHERE imei1 ='".$_POST['serial_no']."' or imei2 ='".$_POST['serial_no']."' ORDER BY `billing_imei_data`.`id` DESC ");
						  $result2 = mysqli_fetch_array($sql2);
						  $tier2 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM billing_master where challan_no='".$result2['doc_no']."'"));
						  $tier3 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM vendor_order_master where po_no='".$result2['doc_no']."'"));
						  $tier4 = mysqli_fetch_assoc(mysqli_query($link1,"SELECT entry_date FROM opening_stock_master where doc_no='".$result2['doc_no']."'"));
						  
						  if($tier2['entry_date']!=""){
							  $prd = mysqli_fetch_array(mysqli_query($link1,"SELECT warranty_days,type_id,productname,model_name,productcategory,productsubcat,hsn_code,brand FROM product_master WHERE productcode = '".$result2['prod_code']."' "));
							  if($prd[0]!=""){ $ws = $prd[0]; }else{ $ws = 0; }
							  if($tier2['entry_date']!=""){ $wsd = $tier2['entry_date']; }
							  $days_diference = daysDifference($today,$wsd);
						  }else if($tier3['entry_date']!=""){
							  $prd = mysqli_fetch_array(mysqli_query($link1,"SELECT warranty_days,type_id,productname,model_name,productcategory,productsubcat,hsn_code,brand FROM product_master WHERE productcode = '".$result2['prod_code']."' "));
							  if($prd[0]!=""){ $ws = $prd[0]; }else{ $ws = 0; }
							  if($tier3['entry_date']!=""){ $wsd = $tier3['entry_date']; }
							  $days_diference = daysDifference($today,$wsd);
						  }else if($tier4['entry_date']!=""){
						  	  $prd = mysqli_fetch_array(mysqli_query($link1,"SELECT warranty_days,type_id,productname,model_name,productcategory,productsubcat,hsn_code,brand FROM product_master WHERE productcode = '".$result2['prod_code']."' "));
							  if($prd[0]!=""){ $ws = $prd[0]; }else{ $ws = 0; }
							  if($tier4['entry_date']!=""){ $wsd = $tier4['entry_date']; }
							  $days_diference = daysDifference($today,$wsd);
						  }else{
						  	  $prd = mysqli_fetch_array(mysqli_query($link1,"SELECT warranty_days,type_id,productname,model_name,productcategory,productsubcat,hsn_code,brand FROM product_master WHERE productcode = '".$result2['prod_code']."' "));
							  if($prd[0]!=""){ $ws = $prd[0]; }else{ $ws = 0; }
						  }
						  ?>
                          <?php if(($days_diference>0)&&($ws>0)){ ?>
                          <span style="color:#060;font-weight: 800;" >In Warranty</span>
                          <?php }else{ ?>
                          <span style="color:#C00;font-weight: 800;" >Out Warranty</span>
                          <?php } ?>
                          <br>
                          <span style="text-align:center;font-size: 12px;" >Product Warranty Days -> <?=$ws?></span><br>
                          <span style="text-align:center;font-size: 12px;" >Days Difference (From Last Ref. Date To Till Date) -> <?=$days_diference?></span><br>
                          <span style="text-align:center;font-size: 12px;" >Last Ref. Date -> <?=$wsd?></span><br>
                      </div><!--close panel body-->
                    </div><!--close panel-->
                  </div>
                </div>
                <div class="col-md-8">
               	<div class="panel-group">
                     <div class="panel panel-default table-responsive">
                      <div class="panel-heading heading1">Product Details</div>
                      <div class="panel-body" style="text-align:;">
                          <span style="color:#060;font-weight: 800;"><?=$prd["productname"]?></span>
                          <br>
                          <span style="text-align:center;font-size: 12px;"><strong>Product Code (System Generated)</strong> -> <?=$result2['prod_code']?></span><br>
                          <span style="text-align:center;font-size: 12px;"><strong>Product Model</strong> -> <?=$prd["model_name"]?></span><br>
                          <span style="text-align:center;font-size: 12px;"><strong>Product Category</strong> -> <?=getAnyDetails($prd['productcategory'],"cat_name","catid" ,"product_cat_master"  ,$link1)?></span><br>
                          <span style="text-align:center;font-size: 12px;"><strong>Product Sub Category</strong> -> <?=getAnyDetails($prd['productsubcat'],"prod_sub_cat","psubcatid" ,"product_sub_category"  ,$link1)?></span><br>
                          <span style="text-align:center;font-size: 12px;"><strong>Product Brand</strong> -> <?=getAnyDetails($prd['brand'],"make","id" ,"make_master"  ,$link1)?></span><br>
                          <span style="text-align:center;font-size: 12px;"><strong>Product Type</strong> -> <?=$prd["type_id"]?></span><br>
                          <span style="text-align:center;font-size: 12px;"><strong>Product HSN</strong> -> <?=$prd["hsn_code"]?></span><br>
                      </div><!--close panel body-->
                    </div><!--close panel-->
                  </div>
               </div>
                <!--<div class="col-md-4"></div>-->
        </div> 
             <!------------ warranty status panel end ------------------>
              
              
			  <?php
				}
				else{
				?>
				   <div class="alert alert-danger alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                          </button>
                        <strong>Alert <i class="fa fa-exclamation fa-lg"></i></strong>&nbsp;&nbsp;Entered serial number does not exist in database.
        			</div>
				<?php
                }
			}
			?>
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
