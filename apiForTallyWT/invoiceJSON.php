<?php
require_once("../config/config.php");
/////// get invoice no///
//$invno = base64_decode($_REQUEST['refid']);
$invno = $_REQUEST['refid'];
////// initialize rollback parameter
mysqli_autocommit($link1, false);
$flag = true;
$err_msg = "";
/////// get master data of billing
$res_billm = mysqli_query($link1,"SELECT * FROM billing_master WHERE challan_no='".$invno."'");
$row_billm = mysqli_fetch_assoc($res_billm);
///// get GST statecode from GSTIN
$from_statecode = substr($row_billm["from_gst_no"],0,2);
$to_statecode = substr($row_billm["to_gst_no"],0,2);
$inv_date = str_replace("-","/",dt_format($row_billm["sale_date"]));
$dc_date = str_replace("-","/",dt_format($row_billm["dc_date"]));
/////// get access details of from location so that data can be push on e-inv portal
//$res_loc = mysqli_query($link1,"SELECT gstno,cd_key,einv_username,einv_password,ef_username,ef_password FROM location_master WHERE location_code='".$row_billm["from_location"]."'");
//$row_loc = mysqli_fetch_assoc($res_loc);
////// check from address character count not more than 100
$from_addrs_cnt = strlen($row_billm["from_addrs"]);
if($from_addrs_cnt > 99){
	$from_addrs_splt = str_split($row_billm["from_addrs"],90);
	$from_pty_addrs = $from_addrs_splt[0];
	$from_pty_addrs2 = $from_addrs_splt[1];
}else{
	$from_pty_addrs = $row_billm["from_addrs"];
	$from_pty_addrs2 = "";
}
////// check to address character count not more than 100
$to_addrs_cnt = strlen($row_billm["to_addrs"]);
if($to_addrs_cnt > 99){
	$to_addrs_splt = str_split($row_billm["to_addrs"],90);
	$to_pty_addrs = $to_addrs_splt[0];
	$to_pty_addrs2 = $to_addrs_splt[1];
}else{
	$to_pty_addrs = $row_billm["to_addrs"];
	$to_pty_addrs2 = "";
}
////// check disp address character count not more than 100
$disp_addrs_cnt = strlen($row_billm["disp_addrs"]);
if($disp_addrs_cnt > 99){
	$disp_addrs_splt = str_split($row_billm["disp_addrs"],90);
	$disp_pty_addrs = $disp_addrs_splt[0];
	$disp_pty_addrs2 = $disp_addrs_splt[1];
}else{
	$disp_pty_addrs = $row_billm["disp_addrs"];
	$disp_pty_addrs2 = "";
}
////// check delivery address character count not more than 100
$deliv_addrs_cnt = strlen($row_billm["deliv_addrs"]);
if($deliv_addrs_cnt > 99){
	$deliv_addrs_splt = str_split($row_billm["deliv_addrs"],90);
	$deliv_pty_addrs = $deliv_addrs_splt[0];
	$deliv_pty_addrs2 = $deliv_addrs_splt[1];
}else{
	$deliv_pty_addrs = $row_billm["deliv_addrs"];
	$deliv_pty_addrs2 = "";
}
//////// billing item list
$bill_itemlist = "";
$tax_per = "";
$i=1;
$res_billd = mysqli_query($link1,"SELECT * FROM billing_model_data WHERE challan_no='".$invno."'");
while($row_billd = mysqli_fetch_assoc($res_billd)){
	$tax_per = $row_billd['cgst_per']+$row_billd['sgst_per']+$row_billd['igst_per'];
	if($row_billd['prod_code']=='39' || $row_billd['prod_code']=='AMC0001'){ $service_flag='Y';} else { $service_flag='N'; }
	/// get part details
	$part_det = explode("~",getAnyDetails($row_billd['prod_code'],"productname,hsn_code","productcode","product_master",$link1));
	//////
	$bill_itemlist .= '{
			"SlNo": "'.$i.'",
			"PrdDesc": "'.$part_det[0].'",
			"IsServc": "'.$service_flag.'",
			"HsnCd": "'.$part_det[1].'",
			"Barcde": "",
			"Qty": '.$row_billd["qty"].',
			"FreeQty": 0,
			"Unit": "PCS",
			"UnitPrice": '.$row_billd["price"].',
			"TotAmt": '.$row_billd["value"].',
			"Discount": 0,
			"PreTaxVal": '.$row_billd["value"].',
			"AssAmt": '.$row_billd["value"].',
			"GstRt": '.$tax_per.',
			"IgstAmt": '.$row_billd["igst_amt"].',
			"CgstAmt": '.$row_billd["cgst_amt"].',
			"SgstAmt": '.$row_billd["sgst_amt"].',
			"CesRt": '.$row_billd["cess_rate"].',
			"CesAmt": '.$row_billd["cess_adval_amt"].',
			"CesNonAdvlAmt": '.$row_billd["cess_non_adval_amt"].',
			"StateCesRt": '.$row_billd["state_cess_rate"].',
			"StateCesAmt": '.$row_billd["state_cess_adval_amt"].',
			"StateCesNonAdvlAmt": '.$row_billd["state_cess_non_adval_amt"].',
			"OthChrg": 0,
			"TotItemVal": '.$row_billd["totalvalue"].',
			"OrdLineRef": "",
			"OrgCntry": "",
			"PrdSlNo": "",
			"BchDtls": {
				"Nm": "",
				"Expdt": "",
				"wrDt": ""
			},
			"AttribDtls": [
				{
					"Nm": "",
					"Val": ""
				}
			]
		},';
	$i++;
}
////close data while loop
///// now we have item list string
$item_json = rtrim($bill_itemlist,",");
/////// check if invoice is applicable for E-way bill or not
##### currently E-way bill condition is invoice value(incl of all taxes) should be greater than 50000 and inter state. we can generate E-way bill if intra state transaction with greater than 50000 invoice value optional
if($row_billm['total_cost']>50000 && ($from_statecode!=$to_statecode || $_REQUEST['ewaybill']=="Y")){
	$tran_gst = $row_billm["trans_gstin"];
	$tran_name = $row_billm["diesel_code"];
	$tran_distance = $row_billm["distance"];
	$tran_inv = $row_billm["docket_no"];
	$tran_invdate = $dc_date;
	$tran_vehicno = $row_billm["vehical_no"];
	$tran_vehictype = "";
	$tran_mode = $row_billm["trans_mode"]; ////Mode of transport (Road-1, Rail-2, Air-3, Ship-4)
}else{
	$tran_gst = "";
	$tran_name = "";
	$tran_distance = "";
	$tran_inv = "";
	$tran_invdate = "";
	$tran_vehicno = "";
	$tran_vehictype = "";
	$tran_mode = "";
}
//////// start complete JSON
$post_json = '{
	"TranDtls": {
		"SupTyp": "B2B",
		"RegRev": "N",
		"EcmGstin": null,
		"IgstOnIntra": "N"
	},
	"DocDtls": {
		"Typ": "INV",
		"No": "'.$row_billm["challan_no"].'",
		"Dt": "'.$inv_date.'"
	},
	"SellerDtls": {
		"Gstin": "'.$row_billm["from_gst_no"].'",
		"LglNm": "'.$row_billm["from_partyname"].'",
		"TrdNm": "",
		"Addr1": "'.$from_pty_addrs.'",
		"Addr2": "'.$from_pty_addrs2.'",
		"Loc": "'.$row_billm["from_city"].'",
		"Pin": '.$row_billm["from_pincode"].',
		"Stcd": "'.$from_statecode.'",
		"Ph": "'.$row_billm["from_phone"].'",
		"Em": "'.$row_billm["from_email"].'"
	},
	"BuyerDtls": {
		"Gstin": "'.$row_billm["to_gst_no"].'",
		"LglNm": "'.$row_billm["party_name"].'",
		"TrdNm": "",
		"Pos": "'.$to_statecode.'",
		"Addr1": "'.$to_pty_addrs.'",
		"Addr2": "'.$to_pty_addrs2.'",
		"Loc": "'.$row_billm["to_city"].'",
		"Pin": '.$row_billm["to_pincode"].',
		"Stcd": "'.$to_statecode.'",
		"Ph": "'.$row_billm["to_phone"].'",
		"Em": "'.$row_billm["to_email"].'"
	},
	"DispDtls": {
		"Nm": "'.$row_billm["from_partyname"].'",
		"Addr1": "'.$disp_pty_addrs.'",
		"Addr2": "'.$disp_pty_addrs2.'",
		"Loc": "'.$row_billm["from_city"].'",
		"Pin": '.$row_billm["from_pincode"].',
		"Stcd": "'.$from_statecode.'"
	},
	"ShipDtls": {
		"Gstin": "'.$row_billm["to_gst_no"].'",
		"LglNm": "'.$row_billm["party_name"].'",
		"TrdNm": "",
		"Addr1": "'.$deliv_pty_addrs.'",
		"Addr2": "'.$deliv_pty_addrs2.'",
		"Loc": "'.$row_billm["to_city"].'",
		"Pin": '.$row_billm["to_pincode"].',
		"Stcd": "'.$to_statecode.'"
	},
	"ItemList": ['.$item_json.'],
	"ValDtls": {
		"AssVal": '.$row_billm['basic_cost'].',
		"CgstVal": '.$row_billm['cgst_amt'].',
		"SgstVal": '.$row_billm['sgst_amt'].',
		"IgstVal": '.$row_billm['igst_amt'].',
		"CesVal": '.$row_billm['cess_amt'].',
		"StCesVal": '.$row_billm['state_cess_amt'].',
		"Discount": 0,
		"OthChrg": 0,
		"RndOffAmt": '.$row_billm['round_off'].',
		"TotInvVal": '.$row_billm['total_cost'].',
		"TotInvValFc": 0
	},
	"PayDtls": {
		"Nm": "",
		"Accdet": "",
		"Mode": "",
		"Fininsbr": "",
		"Payterm": "",
		"Payinstr": "",
		"Crtrn": "",
		"Dirdr": "",
		"Crday": 0,
		"Paidamt": 0,
		"Paymtdue": 0
	},
	"RefDtls": {
		"InvRm": "",
		"DocPerdDtls": {
			"InvStDt": "",
			"InvEndDt": ""
		},
		"PrecDocDtls": [
			{
				"InvNo": "",
				"InvDt": "",
				"OthRefNo": ""
			}
		],
		"ContrDtls": [
			{
				"RecAdvRefr": "",
				"RecAdvDt": "",
				"Tendrefr": "",
				"Contrrefr": "",
				"Extrefr": "",
				"Projrefr": "",
				"Porefr": "",
				"PoRefDt": ""
			}
		]
	},
	"AddlDocDtls": [{
		"Url": "",
		"Docs": "",
		"Info": ""
	}],
	"ExpDtls": {
		"ShipBNo": "",
		"ShipBDt": "",
		"Port": "",
		"RefClm": "",
		"ForCur": "",
		"CntCode": "",
		"ExpDuty": 0
	},
	"EwbDtls": {
		"Transid": "'.$tran_gst.'",
		"Transname": "'.$tran_name.'",
		"Distance": "'.$tran_distance.'",
		"Transdocno": "'.$tran_inv.'",
		"TransdocDt": "'.$tran_invdate.'",
		"Vehno": "'.$tran_vehicno.'",
		"Vehtype": "'.$tran_vehictype.'",
		"TransMode": "'.$tran_mode.'"
	}
}';
////// output
echo "<pre>";
echo $post_json;
?>


