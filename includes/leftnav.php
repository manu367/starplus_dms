<div class="nav-side-menu">
    <div class="brand" style="background-color:#FFF"><img src="../img/inner_logo.png" width="250"/></div>
    <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
        <div class="menu-list">
            <ul id="menu-content" class="menu-content collapse out">
                <li>
                  <a href="#">
                  <i class="fa fa-dashboard fa-lg"></i> Dashboard
                  </a>
                </li>
              <?php
			  ///////////////////// select main tab ///////// 
	          $res_maintab=mysql_query("select b.header,b.header_id from access_report a,report_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id group by b.header_id ORDER by b.header_id")or die("error1".mysql_error());
              $num_maintab=mysql_num_rows($res_maintab);
              if ($num_maintab > 0) { ///// start main tab if
			    $i=1;
                while($row_maintab=mysql_fetch_array($res_maintab)){ ////// start main tab while
			  ?>
                <li  data-toggle="collapse" data-target="#products<?=$i?>" class="collapsed active">
                  <a href="#"><i class="fa fa-gift fa-lg"></i> <?=$row_maintab['header']?> <span class="arrow"></span></a>
                </li>
                <ul class="sub-menu collapse" id="products<?=$i?>">
               <?php 
			   //////////////////// select sub tab of main tab ///////////////
	           $res_subtab=mysql_query("select b.id,b.header_id,b.file_name,b.name from access_report a,report_master b where a.uid='$_SESSION[userid]' and a.status='Y' and b.status='Y' and a.report_id=b.id and b.header_id='$row_maintab[header_id]' ORDER by b.name");
               $num_subtab=mysql_num_rows($res_subtab);
               if ($num_subtab > 0) { /////start sub tab if
                    while($row_subtab=mysql_fetch_array($res_subtab)){ /////// start sub tab while
			   ?>
                    <li class="active"><a href="<?=$row_subtab['file_name'];?>.php?pid=<?=$row_subtab['id']?>&hid=<?=$row_subtab['header_id']?>"><?=$row_subtab['name']?></a></li>
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
                 <li>
                  <a href="#">
                  <i class="fa fa-user fa-lg"></i> Profile
                  </a>
                  </li>

                 <li>
                  <a href="../logout.php">
                  <i class="fa fa-sign-out fa-lg"></i> Logout
                  </a>
                </li>
            </ul>
     </div>
</div>