<?php
require_once("../config/config.php");

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
 <link rel="stylesheet" href="../css/bootstrap-select.min.css">
<script src="../js/bootstrap-select.min.js"></script>
<script  type="text/javascript">
$(document).ready(function(){

    $("#frm3").validate();

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
      <h2 align="center"><i class="fa fa-inr"></i> Salary Generate </h2>
      <?php if($_REQUEST['msg']!=''){?>
      	<h4 align="center">
        	<span 
			<?php if($_REQUEST['sts']=="success"){ echo "class='info-success' style='color: #090;'"; } if($_REQUEST['sts']=="fail"){ echo "class='info-fail' style='color:#FF0033'";} else echo "class='info-fail' style='color:#FF0033'";?>>
			<?php echo $_REQUEST['msg'];?>
			</span>
        </h4>
	  <?php }?>
      <br>     
      <form name="frm1" id="frm1" class="form-horizontal" action="" method="post" enctype="multipart/form-data" >
               
          <br/><br/>                
          <div class="panel-group">
            <div class="panel panel-info table-responsive">
                <div class="panel-heading heading1"><i class="fa fa-inr fa-lg"></i>&nbsp;&nbsp;Salary Details</div>
                 <div class="panel-body">
                 	      
                 	<div class="form-group">
                      <div class="col-md-12" > 
                          <label class="col-md-4 control-label"> Employee Name <span class="red_small">*</span></label> 
                          <div class="col-md-6">
                          	<?php 
								$emp_qry = mysqli_query($link1, " Select loginid, empname from hrms_employe_master  order by empname ");
							?>
                          	<select id="emp_name" name="emp_name" class="form-control selectpicker required" data-live-search="true" onChange="document.frm1.submit();" required>
                            	<option value="" <?php if($_REQUEST['emp_name'] == ""){ echo "selected"; } ?> > -- Please Select -- </option>
                                <?php 
									while($row = mysqli_fetch_array($emp_qry)){
								?>
                                <option value="<?=$row[0];?>" <?php if($_REQUEST['emp_name'] == $row[0]){ echo "selected"; } ?>  > <?php echo $row[1]." | ".$row[0]; ?> </option>
                                <?php 
									}
								?>
                            </select> 
                          </div>    
                      </div>  
                    </div>
                    
                    <div class="form-group">
                      <div class="col-md-12" > 
                          <label class="col-md-4 control-label"> Month <span class="red_small">*</span></label> 
                          <div class="col-md-6">
                          	<select id="emp_month" name="emp_month" class="form-control required" onChange="document.frm1.submit();" required>
                                <option value="" <?php if($_REQUEST['emp_month'] == "") { echo "selected" ;} ?> > -- Please Select -- </option>
                                <option value="01" <?php if($_REQUEST['emp_month']=='01')echo "selected";?>>JAN</option>
                                <option value="02" <?php if($_REQUEST['emp_month']=='02')echo "selected";?>>FEB</option>
                                <option value="03" <?php if($_REQUEST['emp_month']=='03')echo "selected";?>>MAR</option>
                                <option value="04" <?php if($_REQUEST['emp_month']=='04')echo "selected";?>>APR</option>
                                <option value="05" <?php if($_REQUEST['emp_month']=='05')echo "selected";?>>MAY</option>
                                <option value="06" <?php if($_REQUEST['emp_month']=='06')echo "selected";?>>JUN</option>
                                <option value="07" <?php if($_REQUEST['emp_month']=='07')echo "selected";?>>JUL</option>
                                <option value="08" <?php if($_REQUEST['emp_month']=='08')echo "selected";?>>AUG</option>
                                <option value="09" <?php if($_REQUEST['emp_month']=='09')echo "selected";?>>SEP</option>
                                <option value="10" <?php if($_REQUEST['emp_month']=='10')echo "selected";?>>OCT</option>
                                <option value="11" <?php if($_REQUEST['emp_month']=='11')echo "selected";?>>NOV</option>
                                <option value="12" <?php if($_REQUEST['emp_month']=='12')echo "selected";?>>DEC</option>	 
                            </select> 
                          </div>    
                      </div>  
                    </div>
                    
                    <div class="form-group">
                      <div class="col-md-12" > 
                          <label class="col-md-4 control-label"> Year <span class="red_small">*</span></label> 
                          <div class="col-md-6">
                            <select id="emp_year" name="emp_year" class="form-control required" onChange="document.frm1.submit();" required>
                                <option value="" <?php if($_REQUEST['emp_year']==""){ echo "selected";}?> > -- Please Select -- </option>
								<?php
                                $currrent_year=date('Y');
                                $last_year=$currrent_year-1;
                                $sec_last_year=$currrent_year-2;
                                ?>
                                <option value="<?=$sec_last_year?>" <?php if($_REQUEST['emp_year']==$sec_last_year)echo "selected";?>><?=$sec_last_year?></option>
                                <option value="<?=$last_year?>" <?php if($_REQUEST['emp_year']==$last_year)echo "selected";?>><?=$last_year?></option>
                                <option value="<?=$currrent_year?>" <?php if($_REQUEST['emp_year']==$currrent_year)echo "selected";?>><?=$currrent_year?></option>
                            </select> 
                          </div>    
                      </div>  
                    </div>
                    
                    <br><br>
                    
             
                </div><!--close panel body-->
            </div><!--close panel-->
          </div>
                    
          <br><br>                  
      </form>  
     <form name= "frm3" id="frm3" lass="form-horizontal" action="" method="post">
                    <div class="form-group">
                        <div class="col-md-12" style="text-align:center;" > 
                           <input class="btn <?=$btncolor?>" type="button" name="submit" value="Salary Generate" onClick="window.location.href='salary_generate_view.php?rb=view&emp_id=<?php echo base64_encode($_REQUEST['emp_name']);?>&emp_month=<?php echo base64_encode($_REQUEST['emp_month']);?>&emp_year=<?php echo base64_encode($_REQUEST['emp_year']);?><?=$pagenav?>'" / >  
                          
                           
                        </div>  
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
<?php if($_REQUEST['emp_name']=='' || $_REQUEST['emp_month']=='' || $_REQUEST['emp_year']==''){ ?>
<script>
$("#frm3").find("input[type='button']:enabled, select:enabled, textarea:enabled").attr("disabled", "disabled");
</script>
<?php } ?>