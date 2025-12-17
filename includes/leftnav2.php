 <div class="col-sm-3 nav-side-menu" style="padding-left:0px;padding-right:0px;">
    <!--<h4><img src="../img/inner_logo.png" width="200"/></h4>-->
    <div class="brand" style="background-color:#FFFFFF"><img src="../img/inner_logo.png" style="width:210px"/></div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
        <div class="menu-list">
            <?php if (isset($_SESSION['uname'])) { ?>
               <!--<i class="fa fa-user fa-lg"></i>&nbsp;&nbsp;--><span>&nbsp;<?php echo $_SESSION['uname']."  (".$_SESSION['userid'].")";?><br/>
			<?php if($_SESSION['utype']!="8"){ ?>
			&nbsp;<?php echo getAnyDetails($_SESSION['userdesig'],"designame","designationid","hrms_designation_master",$link1)."  (".getAnyDetails($_SESSION['userdepart'],"dname","departmentid","hrms_department_master",$link1).")";?><br/>
			<?php }?>
               &nbsp;<?php echo date("l, F dS Y");?><a href="../logout.php" style="text-decoration:none;color:#FFF" title="Logout">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-power-off fa-lg"></i></a></span>
             <?php } ?><br/><br/>
            <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for tab.." class="form-control">
            <ul id="menu-content" class="menu-content collapse out">
                
                <li <?php if($_REQUEST['pid']==0 && $_REQUEST['hid']==0){ echo "class='active'";} ?> style="font-size:14px">
                <a href="../admin/home2.php?pid=0&hid=0" style="text-decoration:none;color:#FFF">
                  &nbsp;&nbsp;<i class="fa fa-home fa-lg" style="color:#e0a800"></i> Home
                  </a>
                </li>
                
              <?php
			  ////// start get access module added on 07 feb 2024 by shekhar
				$access_module = getAccessModule($link1);
				$check_module_str = "";
				$check_module_str2 = "";
				$expl_module = explode(",",$access_module);
				for($i=0; $i<count($expl_module); $i++){
					if($check_module_str){
						$check_module_str .= " OR find_in_set('".$expl_module[$i]."',module_name) <> 0";
						$check_module_str2 .= " OR find_in_set('".$expl_module[$i]."',b.module_name) <> 0";
					}else{
						$check_module_str = "find_in_set('".$expl_module[$i]."',module_name) <> 0";
						$check_module_str2 = "find_in_set('".$expl_module[$i]."',b.module_name) <> 0";
					}	
				}
				////// end get access module added on 07 feb 2024 by shekhar	
			  ///////////////////// select main tab ///////// 
	          $res_maintab=mysqli_query($link1,"select b.header,b.header_id from access_report a,report_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id AND (".$check_module_str2.") group by b.header_id ORDER by b.header_id")or die("error1".mysqli_error($link1));
              $num_maintab=mysqli_num_rows($res_maintab);
              if ($num_maintab > 0) { ///// start main tab if
			    $i=1;
                while($row_maintab=mysqli_fetch_array($res_maintab)){ ////// start main tab while
				if($row_maintab['header']=="Dashboard"){$icon="fa-dashboard";}elseif($row_maintab['header']=="Master Management"){$icon="fa-database";}else{$icon="fa-newspaper-o";}
			  ?>
                <li data-toggle="collapse" data-target="#products<?=$i?>" class="collapsed <?php if($_REQUEST['hid']==$row_maintab['header_id']){ echo "active";} ?>" style="font-size:12px">
                  <a href="#"><i class="fa <?=$icon?> fa-lg" style="color:#36b9cc"></i> <?=$row_maintab['header']?> <span class="arrow"></span></a>
                </li>
                <ul <?php if($_REQUEST['hid']==$row_maintab['header_id']){ echo "class='collapsed'";}else{ echo "class='collapse'";} ?> id="products<?=$i?>">
               <?php 
			   //////////////////// select sub tab of main tab ///////////////
	           $res_subtab=mysqli_query($link1,"select b.id,b.header_id,b.file_name,b.name,b.icon_img from access_report a,report_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id and b.header_id='$row_maintab[header_id]' AND (".$check_module_str2.") ORDER by b.name");
               $num_subtab=mysqli_num_rows($res_subtab);
               if ($num_subtab > 0) { /////start sub tab if
                    while($row_subtab=mysqli_fetch_array($res_subtab)){ /////// start sub tab while
			   ?>
               
                    <li <?php if($_REQUEST['pid']==$row_subtab['id'] && $_REQUEST['hid']==$row_subtab['header_id']){ echo "class='active'";} ?>><a href="<?=$row_subtab['file_name'];?>.php?pid=<?=$row_subtab['id']?>&hid=<?=$row_subtab['header_id']?>" style="text-decoration:none;color:#FFF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa <?=$row_subtab['icon_img']?> fa-lg" style="color:#1cc88a"></i>&nbsp;<?=$row_subtab['name']?></a></li>
                    
               <?php 
					}///// end sub tab while
			   }//// end sub tab if
			   ?>
                </ul>
             <?php
			    $i++;
				}////// end main tab while
			  }//// end main tab if
			  ?>
              <?php
			  ///////////////////// select Processes main tab ///////// 
	          $res_maintab=mysqli_query($link1,"select b.function_id from access_function a,sub_function_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.function_id=b.id AND (".$check_module_str2.") group by b.function_id ORDER by b.function_id")or die("error1".mysqli_error($link1));
              $num_maintab=mysqli_num_rows($res_maintab);
              if ($num_maintab > 0) { ///// start main tab if
			    $j=$i+1;
                while($row_maintab=mysqli_fetch_array($res_maintab)){ ////// start main tab while
				$fun_name=mysqli_fetch_array(mysqli_query($link1,"select function_name,function_id,icon_img from function_master where function_id='$row_maintab[function_id]'"));
			  ?>
                <li data-toggle="collapse" data-target="#products<?=$j?>" class="collapsed <?php if($_REQUEST['hid']==$row_maintab['function_id']){ echo "active";} ?>" style="font-size:14px">
                  <a href="#"><i class="fa <?=$fun_name['icon_img']?> fa-lg" style="color:#36b9cc"></i> <?=$fun_name['function_name'];?> <span class="arrow"></span></a>
                </li>
                <ul <?php if($_REQUEST['hid']==$row_maintab['function_id']){ echo "class='collapsed'";}else{ echo "class='collapse'";} ?> id="products<?=$j?>">
               <?php 
			   //////////////////// select sub tab of main tab ///////////////
	           $res_subtab=mysqli_query($link1,"select b.id,b.function_id,b.file_name,b.sub_name,b.icon_img from access_function a,sub_function_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.function_id=b.id and b.function_id='$row_maintab[function_id]' AND (".$check_module_str2.") ORDER by b.sub_name");
               $num_subtab=mysqli_num_rows($res_subtab);
               if ($num_subtab > 0) { /////start sub tab if
                    while($row_subtab=mysqli_fetch_array($res_subtab)){ /////// start sub tab while
			   ?>
               
                    <li <?php if($_REQUEST['pid']==$row_subtab['id'] && $_REQUEST['hid']==$row_subtab['function_id']){ echo "class='active'";} ?>><a href="<?=$row_subtab['file_name'];?>.php?pid=<?=$row_subtab['id']?>&hid=<?=$row_subtab['function_id']?>" style="text-decoration:none;color:#FFF">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<i class="fa <?=$row_subtab['icon_img']?> fa-lg" style="color:#1cc88a"></i>&nbsp;<?=$row_subtab['sub_name']?></a></li>
                    
               <?php 
					}///// end sub tab while
			   }//// end sub tab if
			   ?>
                </ul>
             <?php
			    $j++;
				}////// end main tab while
			  }//// end main tab if
			  ?>
				<?php if($_SESSION['userid']=="admin"){?>
                  <li <?php if($_REQUEST['pid']==1001 && $_REQUEST['hid']==1001){ echo "class='active'";} ?> style="font-size:14px"><a href="../admin/application_setting.php?pid=1001&hid=1001" style="text-decoration:none;color:#FFF">                
                   &nbsp;&nbsp;<i class="fa fa-cog fa-lg" style="color:#CCFF99"></i> Settings</a>
                  </li>
				  <li <?php if($_REQUEST['pid']==1002 && $_REQUEST['hid']==1002){ echo "class='active'";} ?> style="font-size:14px"><a href="../admin/master_tabs.php?pid=1002&hid=1002" style="text-decoration:none;color:#FFF">                
                   &nbsp;&nbsp;<i class="fa fa-list fa-lg" style="color:#CCFF99"></i> Master Tabs</a>
                  </li>
                 <?php }?>
                 
                   <li <?php if($_REQUEST['pid']==1000 && $_REQUEST['hid']==1000){ echo "class='active'";} ?> style="font-size:14px"><a href="../admin/changepassword.php?pid=1000&hid=1000" style="text-decoration:none;color:#FFF">                
                   &nbsp;&nbsp;<i class="fa fa-user-secret fa-lg" style="color:#36b9cc"></i> Change Password</a>
                  </li>
                  
                 
                 <li style="font-size:14px"><a href="../logout.php" style="text-decoration:none;color:#FFF">
                  &nbsp;&nbsp;<i class="fa fa-power-off fa-lg" style="color:#c82333"></i> Logout</a>
                </li>
                
            </ul>
     </div>
</div>
<script>
function myFunction() {
  // Declare variables
  var input, filter, ul, li, a, i, txtValue;
  input = document.getElementById('myInput');
  filter = input.value.toUpperCase();
  ul = document.getElementById("menu-content");
  li = ul.getElementsByTagName('li');
  // Loop through all list items, and hide those who don't match the search query
  for (i = 0; i < li.length; i++) {
	li[i].parentNode.className = "collapse in";  
    a = li[i].getElementsByTagName("a")[0];
    txtValue = a.textContent || a.innerText;
    if (txtValue.toUpperCase().indexOf(filter) > -1) {
      li[i].style.display = "";
    } else {
      li[i].style.display = "none";
    }
  }
}
</script>