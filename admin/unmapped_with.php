<?php
session_start();
require_once("../config/config.php");
$today=date("Y-m-d");
if($_POST[Submit]=='Update'){
// update brand rights
$model=$_REQUEST[model];
//print_r($state);
$count=count($model);
$i=0;
while($i < $count){
if($model[$i]==''){
$status='';
}else{
$status='Y';
}
// alrady exist
//echo "select mapped_code from mapped_master where uid='$_REQUEST[uid]' and mapped_code='$model[$i]'";
if(mysql_num_rows(mysql_query("select mapped_code from mapped_master where uid='$_REQUEST[uid]' and mapped_code='$model[$i]'"))>0){
//echo "update mapped_master set mapped_code='$model[$i]' where mapped_code='$_REQUEST[uid]'";
mysql_query("delete from mapped_master where mapped_code='$model[$i]' and uid='$_REQUEST[uid]'")or die("error5 :- ".mysql_error());
}
$i++;	
}
echo "<BODY onLoad='window.close(); window.opener.location.reload(true);'></BODY>";
exit;
}
?>
<!DOCTYPE html>
<html>
<head>
 <meta charset="utf-8">
 <meta name="viewport" content="width=device-width, initial-scale=1">
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
<style>
.red_small{
	color:red;
}

</style>

<title>Party mapping For Parent Party</title>
<SCRIPT LANGUAGE="JavaScript">
<!-- 	
// by Nannette Thacker
// http://www.shiningstar.net
// This script checks and unchecks boxes on a form
// Checks and unchecks unlimited number in the group...
// Pass the Checkbox group name...
// call buttons as so:
// <input type=button name="CheckAll"   value="Check All"
//onClick="checkAll(document.myform.list)">
// <input type=button name="UnCheckAll" value="Uncheck All"
//onClick="uncheckAll(document.myform.list)">
// -->
<!-- Begin
function checkAll(field)
{
for (i = 0; i < field.length; i++)
field[i].checked = true ;
}
function uncheckAll(field)
{
for (i = 0; i < field.length; i++)
field[i].checked = false ;
}
</script>
<script src="../js/frmvalidate.js"></script>
<script type="text/javascript" src="../js/jquery.validate.js"></script>
</head>
<body>
<div class="container-fluid">
  <div class="row content">
	<?php 
    include("../includes/leftnav2.php");
    ?>
    <div class="col-sm-9">
      <h2 align="center">Un-Mapped Party For Billing</h2><br/><br/>
      
     
          <form  name="form1" id="form1" class="form-horizontal" action="" method="post" onsubmit="return chk_data();">
          <div class="form-group">
           <center> <div class="col-md-12"><label class="col-md-5 control-label">Un-Mapped With SD/RSD/MD/BRANCH</label>
              <div class="col-md-5">
                <input type="hidden" name="userid" value="<?=$_REQUEST[userid]?>">
          
              </div>
            </div></center>
			</div>
			<div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">State:<span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="state" class="form-control" id="state" onChange="document.form1.submit();" >
              <option value='All'>All</option>
              <?php
$circlequery="select state from state_master where state in (select state from access_state where uid='$_SESSION[userid]' and status='Y') order by state";	
$circleresult=mysql_query($circlequery) or die("error-4".mysql_error());
while($circlearr=mysql_fetch_array($circleresult)){ ?>
              <option value="<?=$circlearr['state']?>" <?php if($circlearr['state']==$_REQUEST[state]) echo "selected";?>>
              <?=$circlearr['state']?>
              </option>
              <?php }
?>
            </select>
            <?php if($_REQUEST[state]=='All' || $_REQUEST[state]==''){ $state="1" ; } else{ $state="state='$_REQUEST[state]'" ; } ?> 
              </div>
            </div>
			<div class="col-md-6"><label class="col-md-5 control-label">Group<span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="group"  class="form-control" id="group"  onChange="document.form1.submit();">
              <option value='All'>All</option>
              <?php
$circlequery="select * from group_master where status='Active'";	
$circleresult=mysql_query($circlequery) or die("error-5".mysql_error());
while($circlearr=mysql_fetch_array($circleresult)){ ?>
              <option value="<?=$circlearr['group_id']?>" <?php if($circlearr['group_id']==$_REQUEST[group]) echo "selected";?>>
              <?=$circlearr['group_name']?>
              </option>
              <?php	}
?>
            </select>
            <?php if($_REQUEST[group]=='All' || $_REQUEST[group]==''){ $group="1" ; } else{ $group="group_id='$_REQUEST[group]'" ; } ?>
                </div> 
              </div>
          </div>
		  <div class="form-group">
            <div class="col-md-6"><label class="col-md-5 control-label">Party Type<span class="red_small">*</span></label>
              <div class="col-md-5">
                 <select name="distributer"  class="form-control"  onChange="document.form1.submit();">
              <option value='All'>All</option>
              <?php
$circlequery="select type from distributer_type where status='Active'";	
$circleresult=mysql_query($circlequery) or die("error-5".mysql_error());
while($circlearr=mysql_fetch_array($circleresult)){ ?>
              <option value="<?=$circlearr['type']?>" <?php if($circlearr['type']==$_REQUEST[distributer]) echo "selected";?>>
              <?=$circlearr['type']?>
              </option>
              <?php	}
?>
            </select>
            <?php if($_REQUEST[distributer]=='All' || $_REQUEST[distributer]==''){ $distributer="1" ; } else{ $distributer="id_type='$_REQUEST[distributer]'" ; } ?>
          </div>
              </div>
            </div>
			<div class="form-group">
            <div class="col-md-6" ><strong>
            <input name="uid" type="hidden" id="uid" value="<?=$_REQUEST[uid]?>">
            <?php  $rs2=mysql_query("select name,state,city,id_type from asc_master where asc_code='$_REQUEST[uid]'");
$row2=mysql_fetch_array($rs2);
echo $row2[name].",".$row2[state].",".$row2[city];
echo "<br>";
echo " (".$_REQUEST[uid].")"; ?>
            </strong></div></div>
       <div class="form-group"> <div class="col-md-12"  align="center"> <input name="CheckAll" type="button" class="btn btn-primary" 
onclick="checkAll(document.form1.list)" value="Check All" />
          &nbsp;
        <input name="UnCheckAll" type="button" class="btn btn-primary" 
onclick="uncheckAll(document.form1.list)" value="Uncheck All" />
          </div></div>
		  <?php 
		//if($row2[id_type]=='BRANCH'){ $str=" ((user_level <= '$_REQUEST[ulvl]') or (id_type='HO'))";}else{ $str=" (user_level < '$_REQUEST[ulvl]')";}
$circlequery="Select distinct(asc_code),name,state,city from asc_master where asc_code!='$_REQUEST[uid]' and status='Active' and $state and $group and $distributer order by state , name";
$circleresult=mysql_query($circlequery) or die(mysql_error());
echo "<fieldset class=Table_body> <legend  class=Head>Select Party Name</legend><table border=0 cellpadding=3 cellspacing=1 class=Table_body>";
if(mysql_num_rows($circleresult) > 0){
$hide='NO';
$i=1;
while($circlearr=mysql_fetch_array($circleresult)){
if($i%3==1){
echo "<tr>";
}
$state_acc=mysql_query("Select mapped_code from mapped_master  where mapped_code='$circlearr[asc_code]' and uid='$_REQUEST[uid]'")or die(mysql_error());
$num=mysql_num_rows($state_acc);
echo "<td class=Table_body><input type='checkbox' name='model[]' id='list' value='".$circlearr[asc_code]."'";
if($num==0)echo "disabled";
echo "/>".$circlearr['name']." | ".$circlearr['state']." | ".$circlearr['city']."(".$circlearr['asc_code'].") </td>";
if($i/3==0){
echo "</tr>";
}$i++;
}
}
else{
echo "<br/>";
echo "<div align='center' class='style1'>No Record Found !!</div>";
echo "<br/>";
$hide='YES';
}
echo "</table></fieldset>";
?>
          <div class="form-group">
            <div class="col-md-12" align="center">
              <?php if($hide=='NO'){ ?>
            <input type="submit" class="btn btn-primary" name="Submit" id="" value="Update">
        <input title="Back" type="button" class="btn btn-primary" value="Back" onClick="window.location.href='asp_details.php'">
       <?php } ?>
            </div>
			
          </div>
    </form>
      </div>

    </div>
    
  </div>

<?php
include("../includes/footer.php");
?>
</body>
</html>
