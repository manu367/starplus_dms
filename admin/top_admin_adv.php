<nav id="main-nav" role="navigation">
	<ul id="main-menu" class="sm sm-blue">
      <?php 
$rs=mysql_query("select b.header,b.header_id from access_report a,report_master b where ((a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id and b.tab!='app') or (b.tab='Approval' and b.status='S')) group by b.header_id ORDER by b.header_id");
$num=mysql_num_rows($rs);
if ($num > 0) { 
while($row=mysql_fetch_array($rs)){ ?>
    <li><a href="#"><?=$row['header'];?></a>
      <ul>
        <?php
		if($row[header_id]=="2"){
			if(mysql_num_rows(mysql_query("select b.id from access_report a,report_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id and b.tab='app' and b.header_id='$row[header_id]'"))>0){ 
		?>
        <li><a href="#">Approvals</a>
           <ul>
           <?php 
		   $res_app=mysql_query("select b.file_name,b.name from access_report a,report_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id and b.tab='app' and b.header_id='$row[header_id]' ORDER by b.name");
		   while($row_app=mysql_fetch_array($res_app)){
		   ?>
              <li><a href="?page=<?=$row_app[file_name];?>" ><?=$row_app[name];?></a></li>
           <?php
		   }
		   ?>
           </ul>
           </li>
        <?php } }?>
            <?php 
	$rs_report=mysql_query("select b.file_name,b.name from access_report a,report_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id and b.tab!='app' and b.header_id='$row[header_id]' ORDER by b.name");
$num=mysql_num_rows($rs_report);
if ($num > 0) { 
while($row1=mysql_fetch_array($rs_report)){ ?>
      <li><a href="?page=<?=$row1[file_name];?>" ><?=$row1[name];?></a></li>
      <?php }}?>
      </ul>
    </li>
    <?php 
}
}
    ?>
    <?php 
	  //echo "select b.header,b.header_id from access_report a,report_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id group by b.header_id ORDER by b.header_id";
	$rs=mysql_query("select b.function_id from access_function a,sub_function_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.function_id=b.id group by b.function_id ORDER by b.function_id");
$num=mysql_num_rows($rs);
if ($num > 0) { 
while($row=mysql_fetch_array($rs)){ 
$fun_name=mysql_fetch_array(mysql_query("select function_name,function_id from function_master where function_id='$row[function_id]'"));
?>
    <li><a href="#"><?=$fun_name[function_name];?></a>
      <ul>
            <?php 
	$rs_report=mysql_query("select b.file_name,b.sub_name from access_function a,sub_function_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.function_id=b.id and b.function_id='$row[function_id]' ORDER by b.sub_name");
$num=mysql_num_rows($rs_report);
if ($num > 0) { 
while($row1=mysql_fetch_array($rs_report)){ ?>
      <li><a href="?page=<?=$row1[file_name];?>" ><?=$row1[sub_name];?></a></li>
      <?php }}?>
      </ul>
    </li>
    <?php 
}}
if(mysql_num_rows(mysql_query("select id from access_function where function_id in (select id from sub_function_master where function_id in (select function_id from function_master where function_name='Logistic' and status='Active')) and status='Y' and uid='$_SESSION[userid]'"))>0){
	$count_no=mysql_num_rows(mysql_query("select sno from part_request where  type='PO' and (status='Approve By A/C' or wh_disptach_challan in (select challan_no from billing_master where diesel_code='')) and w_code in(select location_id from access_location where uid='$_SESSION[userid]' and status='Y') and w_code!='' group by request_no"));
	?>
	<li style="background-color:#FF0000;position:relative; left:0;" id="prem_hint"><a href="?page=ready_to_disp_po" style="color:#FFFFFF;">Requesting PO (<?=$count_no?>)</a></li>
    <!--<script language="javascript">Blink('prem_hint');</script> -->
<?php }
    ?>
    <li><a href="../LSPT/logout.php">Logout</a></li>
	</ul>
</nav>
