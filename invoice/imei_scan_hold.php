<?php
require_once("../config/config.php");
$invoice = base64_decode($_REQUEST['id']);
$invoice_date = base64_decode($_REQUEST['invdate']);
$ownloc = base64_decode($_REQUEST['invloc']);
$res_data = mysqli_query($link1,"select prod_code,qty from billing_model_data where challan_no='".$invoice."'");
////// final submit form ////
@extract($_POST);
if($_POST['submit'] == "Save"){}
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
 
<script type="text/javascript" src="../js/jquery.validate.js"></script>
<script type="text/javascript" src="../js/jstree.js"></script>
<link href="../css/jstree.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid">
	<div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    	<div class="col-sm-9">
      		<h2 align="center"><i class="fa fa-barcode"></i>&nbsp;&nbsp;Select / Scan IMEI</h2>
      		<div class="form-group" id="page-wrap" style="margin-left:10px;">
                <form name="frm1" id="frm1" class="form-horizontal" action="" method="post">
                	<div class="panel-group">
    					<div class="panel panel-default table-responsive">
        					<div class="panel-heading">Invoice Information</div>
         					<div class="panel-body">
          						<table class="table table-bordered" width="100%">
            						<tbody>
              							<tr>
                							<td width="20%"><label class="control-label">Invoice No.</label></td>
                							<td width="30%"><?php echo $invoice;?></td>
                                            <td width="20%"><label class="control-label">Invoice Date</label></td>
                                            <td width="30%"><?php echo $invoice_date;?></td>
              							</tr>
                                        <?php 
										$arr_model = array();
										while($row_data = mysqli_fetch_assoc($res_data)){
											$proddet=explode("~",getProductDetails($row_data['prod_code'],"productname,productcolor",$link1));
											$arr_model[$row_data['prod_code']] = $proddet[0];
										?>
                                        <tr>
                							<td colspan="2"><label class="control-label"><?=$proddet[0]." (".$proddet[1].") ".$row_data['prod_code']?></label></td>
                							<td colspan="2"><?=$row_data["qty"]?></td>
                                        </tr>
                                        <?php }?>
                                    </tbody>
                               	</table>
                        	</div>
						</div>
						<div class="panel panel-default table-responsive">
        					<div class="panel-heading">Select / Scan IMEI</div>
         					<div class="panel-body">                                
                                <div style="float:left; display:inline-block; width:50%">
                                    <div style="float:left; display:inline-block"><input type="text" id="search" class="form-control"/></div><div style="float:left; display:inline-block">&nbsp;&nbsp;<button id="clear" class="btn btn-primary">Clear</button></div>
                                    <div id="jstree">
                                    </div>
                                </div>
                                <div style="float:left; display:inline-block;  width:50%; text-align:left">
                                	<p><label class="control-label">Selected IMEIs:</label></p>
                                    <ul id="output">
                                    </ul>
                                </div>
                            </div>
                        </div>
					</div>
                </form>
      		</div>
    	</div> 
  	</div>
</div>
<script>
	$(document).ready(function(){
        $("#frm1").validate();
	$("#jstree").jstree({
	  plugins: ["search", "checkbox", "wholerow"],
	  core: {
		data: [
		<?php $cm = 0; foreach($arr_model as $modelcode => $modelname){?>
		////// if multiple entry then comma (,) should be added
		<?php if($cm > 0){ ?> , <?php } $cm++;?>
		  { id: "<?=$modelcode?>", parent: "#", text: "<?=$modelname." ".$modelcode?>" },
		  ////// get model related IMEI
		  <?php
		  $cm2 = 0;
		  $res_imei = mysqli_query($link1,"SELECT id,owner_code,imei1 FROM billing_imei_data WHERE prod_code = '".$modelcode."' order by id desc");
		  if(mysqli_num_rows($res_imei)>0){
		  while($row_imei = mysqli_fetch_assoc($res_imei)){
			  ////// check if owner code is same
			  if($row_imei["owner_code"]==$ownloc){
		  ?>
		  <?php if($cm2 > 0){ ?> , <?php } $cm2++;?>
		  { id: "<?=$row_imei["id"]?>", parent: "<?=$modelcode?>", text: "<?=$row_imei["imei1"]?>" }
		<?php
			  }
		  }///// close while loop
		  }else{
		?>
			{ id: "<?=$cm?>", parent: "<?=$modelcode?>", text: "No IMEI Found"}
		<?php
		  }
		}?>
		],
		animation: false,
		//'expand_selected_onload': true,
		themes: {
		  icons: false
		}
	  },
	  search: {
		show_only_matches: true,
		show_only_matches_children: true
	  }
	});
	
	$("#search").on("keyup change", function() {
	  $("#jstree")
		.jstree(true)
		.search($(this).val());
	});
	
	$("#clear").click(function(e) {
	  $("#search")
		.val("")
		.change()
		.focus();
	});
	
	$("#jstree").on("changed.jstree", function(e, data) {
	  var objects = data.instance.get_selected(true);
	  var leaves = $.grep(objects, function(o) {
		return data.instance.is_leaf(o);
	  });
	  var list = $("#output");
	  list.empty();
	  $.each(leaves, function(i, o) {
		$("<li/>")
		  .text(o.text)
		  .appendTo(list);
	  });
	});
	});	
 </script>
<?php
include("../includes/footer.php");
include("../includes/connection_close.php");
?>
</body>
</html>