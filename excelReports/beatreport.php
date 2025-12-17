<?php
require_once("../config/config.php");
$from_date = base64_decode($_REQUEST['fdate']);
$to_date = base64_decode($_REQUEST['tdate']);
$Getuser = base64_decode($_REQUEST['floc']);
////// filter ///////////
if($from_date=='' || $to_date=='')
{
	$sql_date='1';
	$sqlvisit_date='1';
}
else
{
	$sql_date="(plan_date>='".$from_date."' and plan_date<='".$to_date."')";
	
	$sqlvisit_date="(visit_date>='".$from_date."' and visit_date<='".$to_date."')";
}

if($Getuser=='')
{
	$sql_user='';
	$sql_visituser='';
}
else
{
	$sql_user=" and assigned_user='".$Getuser."'";
	
	$sql_visituser=" and party_code='".$Getuser."'";
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
							 ->setCategory("Beat Report");


// Add some data
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Employee Code')
            ->setCellValue('C1', 'Employee Name')
         	->setCellValue('D1', 'Plan Date')
			->setCellValue('E1', 'PJP Name')
			->setCellValue('F1', 'Task Name')
			->setCellValue('G1', 'Visit City')
			->setCellValue('H1', 'Beat Count')
	        ->setCellValue('I1', 'Perform Count')
			->setCellValue('J1', 'Achieve %')
			->setCellValue('K1', 'Matched?');
		
////////////////
///////////////////////
cellColor('A1:K1', 'F28A8C');
////////////////////////////////
///////////////////////////////////////////////
// Miscellaneous glyphs, UTF-8
$i=2;

$sql_loc=mysqli_query($link1,"SELECT * FROM pjp_data WHERE ".$sql_date.$sql_user);
while($row_loc = mysqli_fetch_array($sql_loc)){

$achieve_per='0';
	
##### Condition for Match or Not 
if($row_loc['task_count']==$row_loc['task_acheive']){  $match_flag='Matched'; } else{ $match_flag='Not Matched';  }

$achieve_per=(($row_loc['task_acheive']*100)/$row_loc['task_count']);
#####	
	
	$admin_detail=explode("~",getAdminDetails($row_loc['assigned_user'],"name",$link1));
$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $row_loc['assigned_user'])
			 ->setCellValue('C'.$i, $admin_detail[0])
			->setCellValue('D'.$i, $row_loc['plan_date'])
			->setCellValue('E'.$i, $row_loc['pjp_name'])
			->setCellValue('F'.$i, $row_loc['task'])
			->setCellValue('G'.$i, $row_loc['visit_area'])
          	->setCellValue('H'.$i, $row_loc['task_count'])
	        ->setCellValue('I'.$i, $row_loc['task_acheive'])
			->setCellValue('J'.$i, $achieve_per.' %')
		    ->setCellValue('K'.$i, $match_flag);
			
			$i++;					
}
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Summary Sheet');

/////// second sheet
$objPHPExcel->createSheet();
$objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A1', 'S.No.')
            ->setCellValue('B1', 'Employee Code')
            ->setCellValue('C1', 'Employee Name')
         	->setCellValue('D1', 'Visit Location')
			->setCellValue('E1', 'Visit Date')
			->setCellValue('F1', 'Matched?')
			->setCellValue('G1', 'Task Performed')
			->setCellValue('H1', 'Task Ref Id')
	        ->setCellValue('I1', 'Approval Required')
			->setCellValue('J1', 'Approve By')
			->setCellValue('K1', 'Approval Date')
	->setCellValue('L1', 'Latitude')
	->setCellValue('M1', 'Longitude');
cellColor('A1:M1', 'F28A8C');
$i=2;
$sql_visit=mysqli_query($link1,"SELECT * FROM dealer_visit WHERE ".$sqlvisit_date.$sql_visituser);
while($row_visit = mysqli_fetch_array($sql_visit)){
	
$res_pjp=mysqli_fetch_array(mysqli_query($link1,"select document_no,assigned_user from pjp_data where id='".$row_visit['pjp_id']."'"));	

$res_deviation=mysqli_fetch_array(mysqli_query($link1,"select * from deviation_request where pjp_id='".$row_visit['pjp_id']."'"));

	
##### Condition for Match or Not 
if($res_deviation['sch_visit']==$res_deviation['change_visit']){  $visit_flag='Matched'; } else{ $visit_flag='Not Matched';  }

#####	
	
	$admin_detail2=explode("~",getAdminDetails($res_pjp['assigned_user'],"name",$link1));
$objPHPExcel->setActiveSheetIndex(1)
            ->setCellValue('A'.$i, $i)
			->setCellValue('B'.$i, $res_pjp['assigned_user'])
			->setCellValue('C'.$i, $admin_detail2['0'])
			->setCellValue('D'.$i, $res_deviation['sch_visit'])
			->setCellValue('E'.$i, $row_visit['visit_date'])
			->setCellValue('F'.$i, $visit_flag)
			->setCellValue('G'.$i, $res_deviation['change_visit'])
          	->setCellValue('H'.$i, $res_pjp['document_no'])
	        ->setCellValue('I'.$i, $res_deviation['app_status'])
			->setCellValue('J'.$i, $res_deviation['app_by'])
		    ->setCellValue('K'.$i, $res_deviation['app_date'])
	->setCellValue('L'.$i, $row_visit['latitude'])
	->setCellValue('M'.$i, $row_visit['longitude']);
			
			$i++;					
}


$objPHPExcel->getActiveSheet()->setTitle('Detailed Sheet');

// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="beat_report.xlsx"');
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
