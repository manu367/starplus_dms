<?php
include('constant.php');
class GET_Functions{       
	private $db;
	private $link;
	private $dt_format;
	function __construct() { 
		include_once './config/dbconnect.php';
		$this->db = new DatabaseService();
		$this->link = $this->db->getConnection();
		///////////////////
		$this->dt_format = new DateTime("now", new DateTimeZone('Asia/Calcutta')); //first argument "must" be a string
		$this->dt_format->setTimestamp(time()); //adjust the object to correct timestamp
	}       
	function __destruct() {
	}
	////////////get last 12 months from current
	public function getLast12Months(){
		
		$a = array();
		for ($i = 0; $i < 12; $i++) 
		{
		   $months[] = date("Y-m", strtotime( date( 'Y-m-01' )." -$i months"));
		   $mnthname[] = date("Y-M", strtotime( date( 'Y-m-01' )." -$i months"));
		}
		$a[] = $months;
		$a[] = $mnthname;
		return $a;
	}
	//// get user attendance on selected dates
	public function getAttendance($user_code,$from_date,$to_date){
		return mysqli_query($this->link,"SELECT * FROM user_attendence WHERE user_id='".$user_code."' AND insert_date >='".$from_date."' AND insert_date<='".$to_date."'");
	}
	//// get user travel on selected dates
	public function getUserTravel($user_code,$from_date,$to_date){
		//return mysqli_query($this->link,"SELECT * FROM user_travel_plan WHERE user_id='".$user_code."' AND DATE(in_datetime) >='".$from_date."' AND DATE(out_datetime)<='".$to_date."'");
		return mysqli_query($this->link,"SELECT * FROM user_travel_plan WHERE user_id='".$user_code."' AND insert_date >='".$from_date."' AND insert_date<='".$to_date."'");
	}
	///// get task type
	public function getTaskType(){
		return mysqli_query($this->link,"SELECT * FROM pjptask_master WHERE status='A' ORDER BY task_name");	
	}
	///// get dealer list
	public function getDealerList($user_id,$visitcity){
		$access_location = $this->getAccessLocation($user_id);
		if($visitcity){
			//return mysqli_query($this->link,"SELECT * FROM asc_master WHERE id_type IN ('DL','DS') AND asc_code IN (".$access_location.") AND status='active' AND city LIKE '%".$visitcity."%' ORDER BY name");
			return mysqli_query($this->link,"SELECT * FROM asc_master WHERE id_type IN ('DL','DS','RT','SR') AND status='active' AND city LIKE '%".$visitcity."%' ORDER BY name");
		}else{
			return mysqli_query($this->link,"SELECT * FROM asc_master WHERE id_type IN ('DL','RT') AND asc_code IN (".$access_location.") AND status='active' ORDER BY name");
		}
	}
	
	///// get Branches list
	public function getBranchList($user_id,$visitcity){
		$access_location = $this->getAccessLocation($user_id);
		if($visitcity){
			//return mysqli_query($this->link,"SELECT * FROM asc_master WHERE id_type IN ('DL','DS') AND asc_code IN (".$access_location.") AND status='active' AND city LIKE '%".$visitcity."%' ORDER BY name");
			return mysqli_query($this->link,"SELECT * FROM asc_master WHERE id_type IN ('BR') AND status='active' AND city LIKE '%".$visitcity."%' ORDER BY name");
		}else{
			return mysqli_query($this->link,"SELECT * FROM asc_master WHERE id_type IN ('BR') AND asc_code IN (".$access_location.") AND status='active' ORDER BY name");
		}
	}
	///// get location name
	public function getLocationName($locationcode,$locationcity){
		return mysqli_query($this->link,"SELECT * FROM asc_master WHERE id_type IN ('DL','DS','RT') AND (asc_code='".$locationcode."' OR phone='".$locationcode."') AND city LIKE '%".$locationcity."%'");
	}
	public function getLocationName2($locationcode){
		return mysqli_query($this->link,"SELECT * FROM asc_master WHERE (asc_code='".$locationcode."' OR phone='".$locationcode."')");
	}
	///// get lead status
	public function getLeadStatus($statusid){
		return mysqli_query($this->link,"SELECT * FROM sf_status_master WHERE id='".$statusid."'");	
	}
	///// get task list of user
	public function getTaskList($user_code,$from_date,$to_date,$srch_taskid){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND plan_date >= '".$from_date."' AND plan_date <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific task is to be search
		if($srch_taskid){ $srch = " AND id = '".$srch_taskid."'";}else{ $srch = "";}
		return mysqli_query($this->link,"SELECT * FROM pjp_data WHERE assigned_user = '".$user_code."' ".$datefilter." ".$srch);
	}
	///// get task history
	public function getTaskHistory($user_code,$task_id){
		return mysqli_query($this->link,"SELECT * FROM task_history WHERE userid  = '".$user_code."' AND task_id = '".$task_id."'");
	}
	//// get payment mode
	public function getPaymentMode(){
		return mysqli_query($this->link,"SELECT * FROM payment_mode WHERE status='A' ORDER BY mode");
	}
	///// get feedback type
	public function getFeedbackType(){
		return mysqli_query($this->link,"SELECT * FROM problem_master WHERE status='A' ORDER BY problem");
	}
	//// get TA DA List
	public function getTaDaList($user_code,$from_date,$to_date,$status,$srch_tadaid){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND entry_date >= '".$from_date."' AND entry_date <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific TADA is to be search
		if($srch_tadaid){ $srch = " AND id = '".$srch_tadaid."'";}else{ $srch = "";}
		///// check if any status is s
		if($status=="" || $status=="All" || $status=="all"){ $st = "";}else{ $st = " AND status = '".$status."'";}
		return mysqli_query($this->link,"SELECT * FROM ta_da WHERE userid = '".$user_code."' ".$datefilter." ".$srch." ".$st." ORDER BY id DESC");
	}
	//////get location stock
	public function getStockList($dealer_code,$part_code){
		if($part_code){ $condition = " and partcode='".$part_code."'";}else{ $condition = "";}
		return $result = mysqli_query($this->link,"SELECT * FROM stock_status WHERE asc_code = '".$dealer_code."' ".$condition."");
	}
	///// get product details on behalf of partcode
	public function getProductDetail($part_code,$fields){
		$result = mysqli_query($this->link,"SELECT ".$fields." FROM product_master WHERE productcode='".$part_code."'"); 
		return $result;	 
	}
	///// get product details on behalf of partcode
	public function getmodelDetail($part_code,$fields){
		//echo "SELECT wp FROM model_master WHERE model_id='".$part_code."'";exit;
		$result = mysqli_query($this->link,"SELECT ".$fields." FROM model_master WHERE model_id='".$part_code."'"); 
		return $result;	 
	}
	///// get Combo details on behalf of partcode
	public function getComboDetail($part_code,$fields){
		$result = mysqli_query($this->link,"SELECT ".$fields." FROM combo_master WHERE bom_modelcode='".$part_code."'"); 
		return $result;	 
	}
	
	////// get product category and sub category details on basis of product sub catid
	public function getCatName($sub_cat_id){
		$result = mysqli_query($this->link,"SELECT prod_sub_cat,product_category FROM product_sub_category WHERE psubcatid='".$sub_cat_id."'"); 
		return $result;	 
	}
	////// get brand name
	public function getBrandName($brand_id){
		$result = mysqli_query($this->link,"SELECT make FROM make_master WHERE id='".$brand_id."'"); 
		return $result;	 
	}
	////// get access location
	public function getAccessLocation($user_id){
		$loction_str="";
		$result = mysqli_query($this->link,"SELECT location_id FROM access_location WHERE uid='".$user_id."' AND status='Y'"); 
		if(mysqli_num_rows($result)>0){
			while($row_parent=mysqli_fetch_assoc($result)){
			   if($loction_str==""){
				   $loction_str.="'".$row_parent['location_id']."'";
			   }else{
				   $loction_str.=",'".$row_parent['location_id']."'";
			   }
			}
		}else{
			$loction_str="''";
		}
		return $loction_str;
	}
	//// get access state ////
	public function getAccessState($user_id){
		$state_str="";
		$res_state=mysqli_query($this->link,"SELECT state FROM access_state WHERE uid='".$user_id."' AND status='Y'");
		if(mysqli_num_rows($res_state)>0){
			while($row_state=mysqli_fetch_assoc($res_state)){
			   if($state_str==""){
				   $state_str.="'".$row_state['state']."'";
			   }else{
				   $state_str.=",'".$row_state['state']."'";
			   }
			}
		}else{
			$state_str="''";
		}
		return $state_str;
	}
	//// get Sales Order List
	public function getSalesOrderList($user_code,$from_date,$to_date,$srch_sono){
		$todayDate = $this->dt_format->format('Y-m-d');
		$prevdate = date('Y-m-d', strtotime($todayDate. ' - 10 days'));
		///check if any date selection is there
		if($from_date){ $datefilter = " AND entry_date >= '".$from_date."' AND entry_date <= '".$to_date."'";}else{ $datefilter = " AND entry_date >= '".$prevdate."' AND entry_date <= '".$todayDate."'";}
		//// check if any specific TADA is to be search
		if($srch_sono){ $srch = " AND po_no = '".$srch_sono."'";}else{ $srch = "";}
		return mysqli_query($this->link,"SELECT * FROM purchase_order_master WHERE create_by = '".$user_code."' ".$datefilter." ".$srch." ORDER BY id DESC");
	}
	//// get Sales Order List
	public function getSalesOrderList2($user_code,$from_date,$to_date,$srch_sono){
		$todayDate = $this->dt_format->format('Y-m-d');
		$prevdate = date('Y-m-d', strtotime($todayDate. ' - 10 days'));
		///check if any date selection is there
		if($from_date){ $datefilter = " AND entry_date >= '".$from_date."' AND entry_date <= '".$to_date."'";}else{ $datefilter = " AND entry_date >= '".$prevdate."' AND entry_date <= '".$todayDate."'";}
		//// check if any specific SO is to be search
		if($srch_sono){ $srch = " AND po_no = '".$srch_sono."'";}else{ $srch = "";}
		//// check if any specific user SO is to be search
		if($user_code){ $srchuser = "create_by = '".$user_code."'";}else{ $srchuser = "1";}
		return mysqli_query($this->link,"SELECT * FROM purchase_order_master WHERE ".$srchuser." ".$datefilter." ".$srch." ORDER BY id ASC");
	}
	///// get Sales Order Data
	public function getSalesOrderData($user_code,$ref_no){
		return mysqli_query($this->link,"SELECT * FROM purchase_order_data WHERE po_no = '".$ref_no."'");
	}
	//// get Lead List
	public function getLeadList($user_code,$from_date,$to_date,$srch_leadid){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND tdate >= '".$from_date."' AND tdate <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific TADA is to be search
		if($srch_leadid){ $srch = " AND reference = '".$srch_leadid."'";}else{ $srch = "";}
		return mysqli_query($this->link,"SELECT * FROM sf_lead_master WHERE create_by = '".$user_code."' ".$datefilter." ".$srch);
	}
	///// get Lead history
	public function getLeadHistory($user_code,$sysRefNo){
		$result = mysqli_query($this->link,"SELECT * FROM sf_lead_history WHERE system_ref_no ='".$sysRefNo."'"); 
		return $result;	
	}
	///// get self created dealer count
	public function getDealerCount($user_id,$user_code,$date_filter){
		if($date_filter){ $srch = " AND start_date LIKE '".$date_filter."%'";}else{ $srch = "";}
		$result = mysqli_query($this->link,"SELECT COUNT(sno) AS nos FROM asc_master WHERE create_by ='".$user_code."'".$srch); 
		return $result;
	}
	//// get self dealer visit count
	public function getDealerVisitCount($user_id,$user_code,$visit_type,$date_filter){
		if($visit_type){ $vstype = " AND dealer_type='".$visit_type."'";}else{ $vstype = "";}
		if($date_filter){ $srch = " AND visit_date LIKE '".$date_filter."%'";}else{ $srch = "";}
		$result = mysqli_query($this->link,"SELECT COUNT(id), dealer_type FROM `dealer_visit` WHERE `userid` LIKE '".$user_code."'".$vstype."".$srch); 
		return $result;
	}
	///// get self created Sales Order count and amount
	public function getSOCount($user_id,$user_code,$date_filter){
		if($date_filter){ $srch = " AND entry_date LIKE '".$date_filter."%'";}else{ $srch = "";}
		$result = mysqli_query($this->link,"SELECT COUNT(id) AS nos, SUM(po_value) AS amt FROM purchase_order_master WHERE create_by ='".$user_code."'".$srch); 
		return $result;
	}
	///// get self leave count
	public function getLeaveCount($user_id,$user_code,$date_filter){
		if($date_filter){ $srch = " AND CONCAT(year,'-',month) LIKE '".$date_filter."%'";}else{ $srch = "";}
		$result = mysqli_query($this->link,"SELECT SUM(cl+ml+pl+sl) AS nos FROM leave_summary WHERE emp_code ='".$user_code."'".$srch); 
		return $result;
	}
	///// get self created Lead count
	public function getLeadCount($user_id,$user_code,$date_filter,$lead_status){
		if($date_filter){ $srch = " AND tdate LIKE '".$date_filter."%'";}else{ $srch = "";}
		if($lead_status){ $leadstatus = " AND status IN (".$lead_status.")";}else{ $leadstatus = "";}
		$result = mysqli_query($this->link,"SELECT COUNT(lid) AS nos, status FROM sf_lead_master WHERE create_by ='".$user_code."'".$srch."".$leadstatus); 
		return $result;
	}
	///// get self created Sales Order invoiced count
	public function getInvCount($user_id,$user_code,$date_filter){
		if($date_filter){ $srch = " AND challan_date LIKE '".$date_filter."%'";}else{ $srch = "";}
		$result = mysqli_query($this->link,"SELECT COUNT(id) AS nos FROM purchase_order_master WHERE create_by ='".$user_code."' AND dispatch_challan!='' ".$srch); 
		return $result;
	}
	///// get last 12 months  target vs acheivement
	public function getTargetAcheivement($user_id,$user_code,$date_filter){
		if($date_filter){ $srch = " AND plan_date LIKE '".$date_filter."%'";}else{ $srch = "";}
		$result = mysqli_query($this->link,"SELECT SUM(task_count) AS tar, SUM(task_acheive) AS ach FROM pjp_data WHERE assigned_user ='".$user_code."' ".$srch); 
		return $result;
	}
	///// get user time line/activities
	public function getUserTimeLine($user_id,$user_code,$fromdate,$todate){
		if($fromdate){ $srch = " AND entry_date >= '".$fromdate."' AND entry_date <= '".$todate."'";}else{ $srch = "";}
		$result = mysqli_query($this->link,"SELECT * FROM user_track WHERE userid ='".$user_code."' ".$srch." ORDER BY id DESC"); 
		return $result;
	}
	//// get dealer visit List
	public function getDealerVisitList($user_code,$from_date,$to_date,$srch_id){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND visit_date >= '".$from_date."' AND visit_date <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific TADA is to be search
		if($srch_id){ $srch = " AND pjp_id = '".$srch_id."'";}else{ $srch = "";}
		return mysqli_query($this->link,"SELECT * FROM dealer_visit WHERE userid = '".$user_code."' ".$datefilter." ".$srch);
	}
	//// get feedback List
	public function getFeedbackList($user_code,$from_date,$to_date,$srch_id){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND entry_date >= '".$from_date."' AND entry_date <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific TADA is to be search
		if($srch_id){ $srch = " AND pjp_id = '".$srch_id."'";}else{ $srch = "";}
		return mysqli_query($this->link,"SELECT * FROM query_master WHERE entry_by = '".$user_code."' ".$datefilter." ".$srch);
	}
	//// get collection List
	public function getCollectionList($user_code,$from_date,$to_date,$srch_id){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND entry_date >= '".$from_date."' AND entry_date <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific TADA is to be search
		if($srch_id){ $srch = " AND pjp_id = '".$srch_id."'";}else{ $srch = "";}
		//return mysqli_query($this->link,"SELECT * FROM query_master WHERE entry_by = '".$user_code."' ".$datefilter." ".$srch);
		return 1;
	}
	////// get any details of any table written by shekhar on 15 FEB 2022
	public function getAnyDetails($keyid,$fields,$lookupname,$tbname){
		///// check no. of column
		$chk_keyword = substr_count($fields, ',');
			
		if($chk_keyword > 0){
			$explodee = explode(",",$fields);
			$tb_details = mysqli_fetch_array(mysqli_query($this->link,"SELECT ".$fields." FROM ".$tbname." WHERE ".$lookupname." = '".$keyid."'"));
			$rtn_str = "";
			for($k=0;$k < count($explodee);$k++){
				if($rtn_str==""){
					$rtn_str.= $tb_details[$k];
				}
				else{
					$rtn_str.= "~".$tb_details[$k];
				}
			}
		}
		else{
			$tb_details = mysqli_fetch_array(mysqli_query($this->link,"SELECT ".$fields." FROM ".$tbname." WHERE ".$lookupname." = '".$keyid."'"));
			$rtn_str = $tb_details[$fields];
		}
		return $rtn_str;
	}
	//// get feedback List
	public function getDeviationStatus($task_id){
		return mysqli_query($this->link,"SELECT * FROM deviation_request WHERE pjp_id = '".$task_id."' ORDER BY id DESC");
	}
	///// get user last latitude and longitude
	public function getLastLatitudeLongitude($user_id,$user_code,$reqdate){
		if($reqdate){ $srch = " AND entry_date >= '".$reqdate."' AND entry_date <= '".$reqdate."'";}else{ $srch = "";}
		$result = mysqli_query($this->link,"SELECT latitude,longitude FROM user_track WHERE userid ='".$user_code."' ".$srch." ORDER BY id DESC"); 
		return $result;
	}
	///// get ticket list of user of DS/DL
	public function getTicketList($user_code,$location_code,$from_date,$to_date,$srch_ticketno){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND DATE(ticket_date) >= '".$from_date."' AND DATE(ticket_date) <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific location is to be search
		if($location_code){ $srch_location = " AND location_code = '".$location_code."'";}else{ $srch_location = "";}
		//// check if any specific ticket is to be search
		if($srch_ticketno){ $srch = " AND ticket_no = '".$srch_ticketno."'";}else{ $srch = "";}
		return mysqli_query($this->link,"SELECT * FROM service_ticket_master WHERE create_by = '".$user_code."' ".$datefilter." ".$srch." ".$srch_location);
	}
	//// get ticket history
	public function getTicketHist($user_code,$ticket_no){
		return mysqli_query($this->link,"SELECT * FROM service_ticket_history WHERE ticket_no = '".$ticket_no."' ORDER BY id DESC");
	}
	///// get indian time from UTC developed by shekhar on 26 aug 2022
	public function getISTfromUTC($utc_datetime){
		// create a $dt object with the UTC timezone
		$dt = new DateTime($utc_datetime, new DateTimeZone('UTC'));
		// change the timezone of the object without changing its time
		$dt->setTimezone(new DateTimeZone('Asia/Calcutta'));
		// format the datetime
		return $dt->format('Y-m-d H:i:s');
	}
	//// get Activity List
	public function getActivityList($user_code,$from_date,$to_date,$srch_actid){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND activity_date >= '".$from_date."' AND activity_date <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific no. is to be search
		if($srch_actid){ $srch = " AND ref_no = '".$srch_actid."'";}else{ $srch = "";}
		return mysqli_query($this->link,"SELECT * FROM activity_master WHERE user_id = '".$user_code."' ".$datefilter." ".$srch." order  by id desc");
	}
	
	
		//// get deviation List
	public function getDeviationList($user_code,$from_date,$to_date,$srch_actid){
		//// get team
		$todayDate = $this->dt_format->format('Y-m-d');
		$team = $this->getTeamMembers($user_code);
		///check if any date selection is there
		if($from_date){ $datefilter = " AND DATE(entry_date) >= '".$from_date."' AND DATE(entry_date) <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific no. is to be search
		if($srch_actid){ $srch = " AND id = '".$srch_actid."'";}else{ $srch = "";}
		if($user_code=="admin"){ $teammember = " AND DATE(entry_date) >= '".$todayDate."' AND DATE(entry_date) <= '".$todayDate."'";}else{ $teammember = " AND entry_by IN (".$team.")";}
		return mysqli_query($this->link,"SELECT * FROM  deviation_request WHERE app_status='Pending For Approval' ".$datefilter." ".$srch." ".$teammember." ORDER BY id DESC");
	}
	
	///// get Activity history
	public function getActivityHistory($user_code,$sysRefNo){
		$result = mysqli_query($this->link,"SELECT * FROM activity_history WHERE ref_no ='".$sysRefNo."'"); 
		return $result;	
	}
	//// get down the line all childs written by shekhar on 06 march 23////
	public function getTeamMembers($userid){
		$loction_str="";
		$res_parent=mysqli_query($this->link,"SELECT child_id FROM relation_data WHERE user_id='".$userid."'")or die(mysqli_error($this->link));
		if(mysqli_num_rows($res_parent)>0){
		while($row_parent=mysqli_fetch_assoc($res_parent)){
		   if($loction_str==""){
			   $loction_str.="'".$row_parent['child_id']."'";
		   }else{
			   $loction_str.=",'".$row_parent['child_id']."'";
		   }
		}
		}else{
			$loction_str="''";
		}
		return $loction_str;
	}
	//// get Leave Request List
	public function getLeaveRequestList($user_code,$from_date,$to_date,$status,$srch_actid){
		///check if any date selection is there
		if($from_date){ $datefilter = " AND entry_date >= '".$from_date."' AND entry_date <= '".$to_date."'";}else{ $datefilter = "";}
		//// check if any specific no. is to be search
		if($srch_actid){ $srch = " AND leave_type = '".$srch_actid."'";}else{ $srch = "";}
		//// check if any specific status is to be search
		if($status){ $srch_status = " AND status = '".$status."'";}else{ $srch_status = "";}
		return mysqli_query($this->link,"SELECT * FROM hrms_leave_request WHERE empid = '".$user_code."' ".$datefilter." ".$srch." ".$srch_status." order  by id desc");
	}
	///// get catalog list
	public function getCatalog($user_code,$addonsrch){
		
		$result = mysqli_query($this->link,"SELECT * FROM catalog_master WHERE status='Active' ".$addonsrch.""); 
		return $result;	
	}
	///// get serial info written by shekhar on 09 jun 2025 for CRM and other
	public function getSerialInfo($serialno){
		$result = mysqli_query($this->link,"SELECT * FROM billing_imei_data WHERE imei1='".$serialno."' ORDER BY id DESC"); 
		return $result;	
	}
}