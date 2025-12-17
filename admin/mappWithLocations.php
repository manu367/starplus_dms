<?php
require_once("../config/config.php");
$getid=base64_decode($_REQUEST['id']);
////// get details of selected location////
$res_locdet=mysqli_query($link1,"SELECT * FROM asc_master where sno='".$getid."'")or die(mysqli_error($link1));
$row_locdet=mysqli_fetch_array($res_locdet);
////// final submit form ////
@extract($_POST);
   if($_POST['save']=='Update'){
      $count=count($parentloc);
      $i=0;
	  mysqli_query($link1,"update mapped_master set status='' where mapped_code='".$childcode."'")or die("er0".mysqli_error($link1));
      while($i < $count){
           if($parentloc[$i]==''){
              $status='';
           }else{
              $status='Y';
		   }
		   ///// check mapping is already is there
           if(mysqli_num_rows(mysqli_query($link1,"select mapped_code from mapped_master where uid='".$parentloc[$i]."' and mapped_code='".$childcode."'"))>0){
              mysqli_query($link1,"update mapped_master set status='".$status."',update_date='".$today."' where uid='".$parentloc[$i]."' and mapped_code='".$childcode."'")or die("ER1".mysqli_error($link1));
           }else{
              mysqli_query($link1,"insert into mapped_master set mapped_code='".$childcode."',uid='".$parentloc[$i]."',status='".$status."',update_date='".$today."'")or die("ER2".mysqli_error($link1));
		   }
           /////////////// insert one record in current cr table if mapping is not there
           if(mysqli_num_rows(mysqli_query($link1,"select * from current_cr_status where parent_code='".$parentloc[$i]."' and asc_code='".$childcode."'"))==0){
              mysqli_query($link1,"insert into current_cr_status set parent_code='".$parentloc[$i]."', asc_code='".$childcode."',cr_abl='0',cr_limit='0',total_cr_limit='0'")or die("ER3".mysqli_error($link1));	
		   }
           $i++;	
	  }///close while loop
      ////// insert in activity table////
	  dailyActivity($_SESSION['userid'],$childcode,"MAPPING","UPDATE",$ip,$link1,"");
	  ////// return message
	  $msg="You have successfully mapped selected location ".$childcode." to their parent locations";
	  ///// move to parent page
      header("Location:asp_details.php?msg=".$msg."".$pagenav);
	  exit;
   }

############################ Filters value apply to refine loctaion ids to mapped
if($_REQUEST['state']!=''){ $locstate="state='$_REQUEST[state]'" ; } else{ $locstate="1";} 
if($_REQUEST['city']!=''){ $loccity="city='$_REQUEST[city]'" ; } else{ $loccity="1";}
if($_REQUEST['loctype']!=''){ $loctype="id_type='$_REQUEST[loctype]'" ; } else{ $loctype="id_type IN ('HO','BR')" ;}
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
<script type="text/javascript" src="../js/common_js.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center"><i class="fa fa-map-signs"></i> Mapped with locations</h2>
      <h4 align="center">Location Name: <?=$row_locdet[name].",".$row_locdet[city].",".$row_locdet[state]."(".$row_locdet[asc_code].")"?></h4><br/><br/>    
          <form  name="form1" id="form1" class="form-horizontal" action="" method="post">
			<div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Location State</label>
              <div class="col-md-5">
               <select name="state" class="form-control" id="state" onChange="document.form1.submit();">
                <option value=''>--Please Select--</option>
                <?php
				$circlequery="select state from state_master order by state";	
				$circleresult=mysqli_query($link1,$circlequery) or die("error-4".mysqli_error($link1));
				while($circlearr=mysqli_fetch_array($circleresult)){ ?>
                <option value="<?=$circlearr['state']?>" <?php if($circlearr['state']==$_REQUEST[state]) echo "selected";?>><?=$circlearr['state']?></option>
                <?php }?>
               </select>
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Location City</label>
              <div class="col-md-5">
               <select name="city"  class="form-control" id="city"  onChange="document.form1.submit();">
                 <option value=''>--Please Select--</option>
                 <?php
				 $circlequery="SELECT distinct city FROM district_master where ".$locstate." order by city";
				 $circleresult=mysqli_query($link1,$circlequery) or die("error-5".mysqli_error($link1));
				 while($circlearr=mysqli_fetch_array($circleresult)){ ?>
                 <option value="<?=$circlearr['city']?>" <?php if($circlearr['city']==$_REQUEST[city]) echo "selected";?>><?=$circlearr['city']?></option>
                 <?php	}?>
                 <option value='Others'<?php if($row_locdet[city]=="Others"){ echo "selected";}?>>Others</option>
               </select>
              </div> 
             </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Location Type</label>
              <div class="col-md-5">
                 <select name="loctype" id="loctype" class="form-control" onChange="document.form1.submit();">
                   <option value=''>--Please Select--</option>
				   <?php
                    //$circlequery="select locationtype,locationname from location_type where status='A' and seq_id < '".$row_locdet['user_level']."'";	
					$circlequery="select locationtype,locationname from location_type where status='A'";	
                    $circleresult=mysqli_query($link1,$circlequery) or die("error-5".mysqli_error($link1));
                    while($circlearr=mysqli_fetch_array($circleresult)){ ?>
                    <option value="<?=$circlearr['locationtype']?>"<?php if($circlearr['locationtype']==$_REQUEST[loctype]) echo "selected";?>><?=$circlearr['locationname']?></option>
                  <?php	}?>
                 </select>
               </div>
            </div>
          </div>
         <!-- <div class="form-group">
            <div class="col-md-6">
              <div class="col-md-5">&nbsp;
              </div>
            </div>
            <div class="col-md-6">
              <div class="col-md-10" align="right"><input name="CheckAll" type="button" class="btn btn-primary" onclick="checkAll(document.form1.list)" value="Check All" />&nbsp;
                                                  <input name="UnCheckAll" type="button" class="btn btn-primary" onclick="uncheckAll(document.form1.list)" value="Uncheck All" />
              </div>
            </div>
          </div>-->
          <div class="form-group">
            <div class="col-md-12" align="right">
			<input name="CheckAll" type="button" class="btn <?=$btncolor?>" onClick="checkAll(document.form1.list)" value="Check All" />&nbsp;
            <input name="UnCheckAll" type="button" class="btn <?=$btncolor?>" onClick="uncheckAll(document.form1.list)" value="Uncheck All" />
            </div>
          </div>
          <div class="panel-group">
           <div class="panel panel-default table-responsive">
             <div class="panel-heading heading1">Locations Name</div>
               <div class="panel-body">
          <?php
          //$sql_locations="Select distinct(asc_code),name,state,city from asc_master where asc_code!='".$row_locdet[asc_code]."' and ".$locstate." and ".$loccity." and ".$loctype." and user_level <= '".$row_locdet['user_level']."' and status='Active' order by state, name";
		  $sql_locations="Select distinct(asc_code),name,state,city from asc_master where asc_code!='".$row_locdet[asc_code]."' and ".$locstate." and ".$loccity." and ".$loctype." and status='Active' order by state, name";
          $loccount=mysqli_query($link1,$sql_locations) or die(mysqli_error($link1));
		  ?>
          <table id="myTable" class="table table-hover" border="0">
           <tbody>
          <?php
          if(mysqli_num_rows($loccount) > 0){
             $hide='NO';
             $i=1;
             while($row_locations=mysqli_fetch_array($loccount)){
               if($i%3==1){
		  ?>
            <tr>
          <?php  } 
		  
				$mappedloc=mysqli_query($link1,"Select mapped_code from mapped_master where mapped_code='".$row_locdet[asc_code]."' and uid='".$row_locations[asc_code]."' and status='Y'")or die(mysqli_error($link1));
				$num=mysqli_num_rows($mappedloc);
				//  if($num>0) {
		  ?>	
              <td><input type="checkbox" name="parentloc[]" id="list" <?php if($num>0){ echo "checked";}?> value="<?=$row_locations[asc_code]?>"/>&nbsp;<?=$row_locations['name']." | ".$row_locations['state']." | ".$row_locations['city']."(".$row_locations['asc_code'].")";?></td>
              <?php 
			//  }
			  if($i/3==0){
			  ?>
            </tr>
              <?php
              }
		     $i++;
			 }//close while loop
		  }//close row check if
          else{
            echo "<br/>";
			echo "<div align='center' class='red_small'> Not required for HO !!</div>";
			echo "<br/>";
			$hide='YES';
		  }
		  ?>
          </tbody>
          </table>
          </div><!--close panel body-->
         </div><!--close panel-->
         </div><!--close panel group-->
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($hide=='NO'){ ?>
                 <input type="submit" class="btn <?=$btncolor?>" name="save" id="save" value="Update">&nbsp;
                 <input name="childcode" id="childcode" type="hidden" value="<?=$row_locdet[asc_code]?>"/>
              <?php } ?>   
                 <input title="Back" type="button" class="btn <?=$btncolor?>" value="Back" onClick="window.location.href='asp_details.php?<?=$pagenav?>'">
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
