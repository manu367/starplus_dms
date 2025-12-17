<?php
require_once("../config/config.php");
$id=$_GET['id'];
$sql=mysqli_query($link1,"select * from sf_quote_master where quote_no='$id'");
$row=mysqli_fetch_array($sql);

/////// Using for attach file //////////
$path = mysqli_query($link1,"SELECT path FROM sf_tbl_party_document  WHERE create_by = '".$_SESSION['userid']."' and transaction_id = '".$row['quote_no']."' ");
$img_no = mysqli_num_rows($path);
////// final submit form ////
@extract($_POST);
$msg='';
if($_POST['internalnote']=='InternalNote'){
	//$inote=preg_replace('/[^a-zA-Z]+/', '', $internal_note);
	mysqli_query($link1,"insert into sf_quote_ticketmaster set quote_no='".$id."', party_id = '".$row['party_id']."', subject='".$sub."',  internal_note='".$internal_note."', ticket_dt='".$today."', ticket_time='".$logged_time."', ip='".$ip."', ticket_loggedby='".$_SESSION['userid']."',type='Internal Note', contact_person='".$contact_person."', schedule_date='".$sch_date."', schedule_time='".$sch_time."', comm_type='".$comm_type."'");
	if(mysqli_insert_id($link1)>0){
		dailyActivity($_SESSION['userid'],$row['reference'],"QUOTE","I-QUOTE ADD",$ip,$link1,"");
		$msgg='Internal Note Posted Successfully!';
		header("Location:quote_view.php?id=$id&msg=$msgg&sts=success");
	}
	else
	{
		$msgg="Request could not be processed!";
		header("Location:quote_view.php?id=$id&msg=$msgg&sts=fail");
	}
}
if($_POST['clientnote']=='ClientNote'){
	//$cnote=preg_replace('/[^a-zA-Z]+/', '', $client_note);
	mysqli_query($link1,"insert into sf_quote_ticketmaster set quote_no='".$id."', client_note='".$client_note."', ticket_dt='".$today."', ip='".$ip."', ticket_loggedby='".$_SESSION['userid']."', ticket_time='".$logged_time."', type='Client Note'");
	if(mysqli_insert_id($link1)>0)
	{
		dailyActivity($_SESSION['userid'],$row['reference'],"QUOTE","C-QUOTE ADD",$ip,$link1,"");
		$msgg="Client Note Posted Successfully!";
		header("Location:quote_view.php?id=$id&msg=$msgg&sts=success");
	}
	else
	{
		$msgg="Request could not be processed!";
		header("Location:quote_view.php?id=$id&msg=$msgg&sts=fail");
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
        $("#internalnote").validate();
		$("#clientnote").validate();
    });
	$(document).ready(function(){
    	$('#dt_basic5').dataTable();
		$('#dt_basic6').dataTable();
	});
	$(document).ready(function() {
		$('#sch_date').datepicker({
			format: "yyyy-mm-dd",
			todayHighlight: true,
			startDate: "<?=$todayt?>",
			autoclose: true
		});
	});
	
 </script>
 <script type="text/javascript" src="../js/jquery.validate.js"></script>
 <script language="javascript" type="text/javascript">
 $(function() {
		$('.pop1').on('click', function() {
			$('.imagepreview').css("width","100%");
			$('.imagepreview').css("height","auto");
			$('#imagemodal').modal('show');   
		});		
});
 </script>
 <script src="../js/bootstrap-datepicker.js"></script>
 <script src="../js/editor.js"></script>
 <link href="../css/editor.css" type="text/css" rel="stylesheet"/>
 
 <script type="text/javascript">
 	$(document).ready(function() {
		$("#txtEditor1").Editor();
	});
	$(document).ready(function() {
		$("#txtEditor2").Editor();
	});
	function setHtmlAreaValue1(){
		document.internalnote.internal_note.value =  $("#txtEditor1").Editor("getText");
	}
	function setHtmlAreaValue2(){
		document.clientnote.client_note.value =  $("#txtEditor2").Editor("getText");
	}
 </script>
 
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-eye"></i> View Quote</h2>
      <h4 align="center"> Quote No - <?php echo $id; ?></h4><br/><br/>
      <?php if($_REQUEST['msg']!=''){?>
      <h4 align="center">
        <span <?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span></h4><?php }?>
            
      <div class="form-group"  id="page-wrap" style="margin-left:10px;">
      
      <div style="margin-bottom:10px;">
      	<button class="btn <?=$btncolor?>" onClick="window.open('../print/print_quote.php?id=<?php echo base64_encode($id);?>&page=quote');" style="cursor: pointer;" type="button" >Print Quote</button>&nbsp &nbsp
  		<button title="Back" type="button" class="btn <?=$btncolor?>" style="float:right" onClick="window.location.href='quote_list.php?tab=<?php echo $_REQUEST['tab'];?>&page=quote&status=<?=$_REQUEST["status"]?><?=$pagenav?>'"><span>Back</span></button>
  	  </div>
  
      	<table class="table table-bordered" width="100%">
            <tbody>
              <tr>
                <td width="20%"><label class="control-label">Quote Id</label></td>
                <td width="30%"><?php echo ucfirst ($row['quote_no']); ?></td>
                <td width="20%"><label class="control-label">Remark</label></td>
                <td width="30%"><?php echo ucfirst($row['remark']); ?></td>
              </tr>
              <tr>
                <td><label class="control-label">Party Name</label></td>
                <td><?php echo ucwords(($row['party_id'])); ?> </td>
                <td><label class="control-label">Party Address</label></td>
                <td><?php echo $row['address']; ?></td>
              </tr>
              <tr>
                <td><label class="control-label">Create Date</label></td>
                <td><?php echo dt_format($row['create_dt']); ?></td>
                <td><label class="control-label">Create time</label></td>
                <td><?php echo $row['create_time']; ?></td>
              </tr>
              <tr>
                <td><label class="control-label">Attached File</label></td>
                <td> 
				<?php if($img_no>0){ ?>
				<a href="#" class="pop1">
                	<i class="fa fa-external-link"></i>&nbsp;&nbsp;<strong>Click To View File</strong>
                </a>
                <?php } ?>
                </td>
                <td><label class="control-label">Sales Executive</label></td>
                <td><?php echo  getAdminDetails($row['sales_executive'],"name",$link1);?></td>
              </tr>
              <tr>
                <td><label class="control-label">Approve Remark</label></td>
                <td><?php echo  $row['approve_remark'];?></td>
                <td><label class="control-label">Approve By</label></td>
                <td><?php echo ucwords(getAdminDetails($row['approve_by'],"name",$link1)); ?></td>
              </tr>
              <tr>
                <td><label class="control-label">Create By</label></td>
                <td><?php echo  getAdminDetails($row['create_by'],"name",$link1);?></td>
                <td><label class="control-label">Create Location</label></td>
                <td><?php echo  $row['create_location'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Update By</label></td>
                <td><?php echo ucwords(getAdminDetails($row['update_by'],"name",$link1));?></td>
                <td><label class="control-label">Update Date & Time</label></td>
                <td><?php echo  $row['update_dt'];?></td>
              </tr>
              <tr>
                <td><label class="control-label">Status</label></td>
                <td><?php echo  ucwords(get_status($row['status'],$link1)); ?></td>
                <td><label class="control-label">&nbsp;</label></td>
                <td>&nbsp;</td>
              </tr>
            </tbody>
          </table>
          
          <!-- row -->
	<div class="row">
					<h4 align="center">Items Details</h4>
					<!-- Items Detail content -->
					<div class="form-group">
						<table id="dt_basic6" class="table table-bordered table-hover" width="100%">
							<thead>
								<tr class="<?=$tableheadcolor?>" >
                                    <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a><strong>S No.</strong> </th>
                                    <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Product</strong></th>
                                    <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Create Date</strong></th>
                                    <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Create Time</strong></th>
                                    <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Price</strong></th>
                                    <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Tax %</strong></th>
                                    <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Qty</strong></th>
                                    <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Tax Amount</strong></th>
                                    <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Amount</strong></th>
								</tr>
							</thead>
                           
                	<tbody>
                  	<?php  
						$sno=0;
								$tsql=mysqli_query($link1,"select * from sf_quote_itemsdetail where create_by='".$_SESSION['userid']."' and quote_no='".$id."' order by id desc");
								if($tsql!=FALSE)
								{
										while($trow=mysqli_fetch_assoc($tsql))
										{
										$sno=$sno+1;
									 ?>	
								
                 	<tr title="" class="even pointer">
                    <td><?php echo $sno; ?></td>
                    <td><?php echo getProductDetails($trow['product_id'],"productname",$link1);?></td>
                    <td style=" text-align:center;"><?php echo dt_format($trow['create_dt']);?></td>
                    <td style=" text-align:center;"><?php echo $trow['create_time'];?></td>
                    <td style=" text-align:right;"><?php echo currencyFormat($trow['rate']); ?></td>
                    <td style=" text-align:right;"><?php echo currencyFormat($trow['tax_value']); ?></td>
                    <td style=" text-align:center;"><?php echo $trow['qty'];?></td>
                    <td style=" text-align:right;"><?php echo currencyFormat($trow['tax_amt']); ?></td>
                    <td style=" text-align:right;"><?php echo currencyFormat($trow['total']); ?></td>                  	
                  </tr>
                 <?php }}?>
                 <tfoot>
                 <tr title="" class="even pointer">
                     <td colspan="6"><strong> Grand Total </strong></td>
                     <td style=" text-align:center;"><strong><?php echo $row['qty']; ?></strong></td>
                     <td style=" text-align:right;"><strong><?php echo currencyFormat($row['total_taxamt']); ?></strong></td>
                     <td style=" text-align:right;"><strong><?php echo currencyFormat($row['grandtotal']); ?></strong></td>
                 </tr>
                 </tfoot>
 				</tbody>
      		</table>
    	</div>
									<!-- end Items Detail content -->
							</div>
							<!-- end Items Detail -->
          
                <!-- row -->
                <div class="row">
					<h4 align="center">Ticket Thread</h4>
					<!-- widget content -->
					<div class="form-group">
						<table id="dt_basic5" class="table table-bordered table-hover" width="100%">
							<thead>
								<tr class="<?=$tableheadcolor?>" >
								
                                  <th data-class="expand"><a href="#" name="entity_id" title="asc" ></a><strong>Date</strong> </th>
                                  <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Subject</strong></th>
                                  <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Person </strong></th>
                                  <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Notes</strong></th>
                                  <th data-hide="phone"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Post By</strong></th>
                                  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Type</strong></th>
                                  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Comm. Type</strong></th>
                                  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Sched. Date</strong></th>
                                  <th data-hide="phone,tablet"><a href="#" name="email" title="asc" class="not-sort"></a><strong>Sched. Time<br/>(In days)</strong></th>
			
								</tr>
							</thead>
                           
                <tbody>
                  <?php
						$sno=0;
								$tsql=mysqli_query($link1,"select * from sf_quote_ticketmaster where ticket_loggedby='".$_SESSION['userid']."' and quote_no='".$id."' order by id desc");
								if($tsql!=FALSE)
								{
										while($trow=mysqli_fetch_assoc($tsql))
										{
										$sno=$sno+1;
									 ?>	
								
                 	<tr title="" class="even pointer">
                    <td style=" text-align:center;"><?php echo dt_format($trow['ticket_dt']);?></td>
                    <td><?php echo $trow['subject'];?></td>
                    <td><?php echo $trow['contact_person'];?></td>
                    <td><?php if($trow['internal_note']!=''){echo ucwords(htmlspecialchars_decode($trow['internal_note']));} else {echo ucwords(htmlspecialchars_decode($trow['client_note']));}?></td>
                     <td><?php echo getAdminDetails($trow['ticket_loggedby'],"name",$link1);?></td>
                     <td><?php echo $trow['type'];?></td>
                     <td><?php echo get_communication($trow['comm_type'],$link1);?></td>
                  	 <td style=" text-align:center;"><?php echo ($trow['schedule_date']);?></td>
                      <td><?php echo $trow['schedule_time'];?></td>

                  </tr>
                 <?php }}?>
               
				
 </tbody>

      </table>
    </div>
									<!-- end widget content -->
				
							</div>
							<!-- end widget -->
                           
      	<ul class="nav nav-tabs">
            <li class="active"><a data-toggle="tab" href="#home"><i class="fa fa-sticky-note"></i> Internal Note</a></li>
            <li><a data-toggle="tab" href="#menu2"><i class="fa fa-edit"></i> Client Note</a></li>
          </ul>
           <div class="tab-content">
            <div id="home" class="tab-pane fade in active"><br/>
          	<form name="internalnote" id="internalnote" class="form-horizontal" action="" method="post">
            <div class="form-group">
                <div class="col-md-6"><label class="col-md-6 control-label">Subject <strong><span style="color:red">*</span></strong></label>
                  <div class="col-md-6">
                    <input type="text" name="sub" id="sub" class="form-control entername" required /> 
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6 control-label"></label>
                  <div class="col-md-6" >
                  </div>
                </div>
              </div>
              <div class="form-group">
                  <div class="col-md-12" >
                        <textarea name="internal_note" id="txtEditor1" class="form-control" placeholder="Note Detail" required> </textarea> 
                	</div>
              </div>
              <div class="form-group">
                <div class="col-md-6"><label class="col-md-6 control-label">Schedule Date<strong><span style="color:red"></span></strong></label>
                  <div class="col-md-6 input-append date">
                        <div style="display:inline-block;float:left;">
                            <input type="text" class="form-control span2" name="sch_date"  id="sch_date" style="width:180px;">
                        </div>
                        <div style="display:inline-block;float:left;">&nbsp;<i class="fa fa-calendar fa-lg"></i></div>
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6 control-label">Schedule Time(In Days) <strong><span style="color:red"></span></strong></label>
                  <div class="col-md-6" >
                  	<input type="text" name="sch_time" id="sch_time" class="form-control digits" />
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-6"><label class="col-md-6 control-label">Contact Person<strong><span style="color:red"></span></strong></label>
                  <div class="col-md-6">
                    <input type="text" name="contact_person" id="contact_person" class="form-control entername"  />
                  </div>
                </div>
                <div class="col-md-6"><label class="col-md-6 control-label">Communication Type<strong><span style="color:red"></span></strong></label>
                  <div class="col-md-6" >
                  	<select name="comm_type" id="comm_type" class="form-control" requireds >
                           <option value="">Select Communication Type</option>
                           <?php $comm=mysqli_query($link1,"select * from sf_tbl_comm_type");
                           while($crow=mysqli_fetch_assoc($comm)){
                           ?>
                           <option value="<?php echo $crow['id'];?>"><?php echo $crow['comm_type'];?></option>
                           <?php } ?>
                     </select>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <div class="col-md-12" align="center">
                  <button class="btn <?=$btncolor?>" type="submit" name="internalnote" value="InternalNote" onClick="setHtmlAreaValue1()" ><i class="fa fa-save"></i> Post Internal Note</button>
                  <button class="btn <?=$btncolor?>"  name="cancel" onclick="reset();">Cancel</button>
                </div>
              </div>
            </form>
    </div>
            <div id="menu2" class="tab-pane fade"> <br/>
              <form name="clientnote" id="clientnote" class="form-horizontal" action="" method="post">
                 <div class="form-group">
                  <div class="col-md-12" >
                       <textarea name="client_note" id="txtEditor2" class="form-control required" placeholder="" required> </textarea>
                      
                	</div>
              </div>
              <div class="form-group">
                <div class="col-md-12" align="center">
                  <button class="btn btn-primary" type="submit" name="clientnote" value="ClientNote" onClick="setHtmlAreaValue2()"><i class="fa fa-save"></i> Post Client Note</button>
                  <button class="btn btn-default"  name="cancel" onclick="reset();">Cancel</button>
                </div>
              </div>                    
       		</form>
            </div>
          </div>
      </div><!--End form group-->
    </div><!--End col-sm-9-->
  </div><!--End row content-->
</div><!--End container fluid-->
<div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content" align="center">              
      <div class="modal-body">
      	<?php if($img_no>0){ ?>
        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
        <div id="POPIMG" >
			<?php 
                $v = 1;
                while($IMGrow=mysqli_fetch_assoc($path)){	
             ?>
                <div style="text-align:center;"> -- <?=$v?> -- </div><br>
                <img class="imagepreview" src="<?php echo $IMGrow['path'];?>" style="width: 100%; height: auto;"> <br><br>
             <?php $v++; } ?> 
        </div>
        <?php } ?>
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