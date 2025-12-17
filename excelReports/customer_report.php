<?php
require_once("../config/config.php");

$accessLocation=getAccessLocation($_SESSION['userid'],$link1);
$accessState=getAccessState($_SESSION['userid'],$link1);

////// if location come ///////////
if($_REQUEST['location']!=""){
	$loc_code="and mapplocation ='".$_REQUEST['location']."'";
}else{
	$loc_code="and mapplocation in (".$accessLocation.")";
}
//// if customer state come
if($_REQUEST['cust_state']!=""){
	$loc_state="state='".$_REQUEST['cust_state']."'";
}else{
	$loc_state="state in (".$accessState.")";
}
## if customer city
if($_REQUEST['cust_city']!=""){
	$loc_city="and city='".$_REQUEST['cust_city']."'";
}else{
	$loc_city="";
}
## Category
if($_REQUEST['customertype']!=""){
	$loc_cat="and category='".$_REQUEST['customertype']."'";
}else{
	$loc_cat="";
}

## selected location Status
if($_REQUEST['status']!=""){
	$loc_status="and status='".$_REQUEST['status']."'";
}else{
	$loc_status="";
}

//////End filters value/////

/** Include PHPExcel */
require_once("../ExcelExportAPI/Classes/PHPExcel.php");
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Candour Software")
							 ->setLastModifiedBy("Candour Software")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Customer Report");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Customer Id')
            ->setCellValue('C1', 'Customer Name')
         	 ->setCellValue('D1', 'State')
			->setCellValue('E1', 'City')
			->setCellValue('F1', 'Customer Type')
			->setCellValue('G1', 'Email')
			->setCellValue('H1', 'Contact No.')
	        ->setCellValue('I1', 'Address')
			->setCellValue('J1', 'Location')
			->setCellValue('K1', 'Status');
		
////////////////
///////////////////////
cellColor('A1:K1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;

$sql_loc=mysqli_query($link1,"Select * from customer_master where $loc_state $loc_code $loc_city $loc_cat $loc_status order by id");
while($row_loc = mysqli_fetch_array($sql_loc)){
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['customerid'])
			 ->setCellValue('C'.$i, $row_loc['customername'])
			->setCellValue('D'.$i, $row_loc['state'])
			->setCellValue('E'.$i,$row_loc['city'])
			->setCellValue('F'.$i, $row_loc['category'])
			->setCellValue('G'.$i,$row_loc['emailid'])
          	->setCellValue('H'.$i, $row_loc['contactno'])
	        ->setCellValue('I'.$i, $row_loc['address'])
			->setCellValue('J'.$i, getLocationDetails($row_loc['mapplocation'],"name",$link1))
		    ->setCellValue('K'.$i, $row_loc['status']);
			
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Simple');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="customer_report.xlsx"');
header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
header ('Pragma: public'); // HTTP/1.0

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
