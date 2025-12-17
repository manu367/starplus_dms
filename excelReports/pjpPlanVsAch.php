<?php
require_once("../config/config.php");
$from_date = base64_decode($_REQUEST['fdate']);
$to_date = base64_decode($_REQUEST['tdate']);
$datediff = daysDifference($to_date,$from_date);
////// filter ///////////
if($from_date=='' || $to_date=='')
{
	$sql_date='1';
}
else
{
	$sql_date="(plan_date>='".$from_date."' and plan_date<='".$to_date."')";
}
//////End filters value/////
/** Include PHPExcel */
require_once("../ExcelExportAPI/Classes/PHPExcel.php");
//require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';

function cellsToMergeByColsRow($start = -1, $end = -1, $row = -1){
    $merge = 'A1:A1';
    if($start>=0 && $end>=0 && $row>=0){
        $start = PHPExcel_Cell::stringFromColumnIndex($start);
        $end = PHPExcel_Cell::stringFromColumnIndex($end);
        $merge = "$start{$row}:$end{$row}";
    }
    return $merge;
}
// Create new PHPExcel object
$objPHPExcel = new PHPExcel();

// Set document properties
$objPHPExcel->getProperties()->setCreator("Candour Software")
							 ->setLastModifiedBy("Candour Software")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("PJP Plan Vs Ach");


// Add some data
$row = 1;
$col = 0;
$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $row, 'RSM Name');
$col++;
for($d=0; $d <= $datediff; $d++){
	$pdate = date("d-M-y", strtotime ( "+".$d."days" , strtotime ( $from_date ) ) );
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $row, $pdate);
	$mergeto = $col+1;
	$objPHPExcel->setActiveSheetIndex(0)->mergeCells(cellsToMergeByColsRow($col,$mergeto,$row));
	$col++;
	$col++;
}
$row = 2;
$col = 1;
for($d=0; $d <= $datediff; $d++){
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $row, "Plan");
	$col++;
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $row, "Ach");
	$col++;
}
$column = PHPExcel_Cell::stringFromColumnIndex(((2*$datediff)+1));
$row = 1;
$cell = $column.$row;
$range = 'A1:'.$cell; 
$objPHPExcel->getActiveSheet()->getStyle($range)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => 'F28A8C' //Yellow
        )
));
$objPHPExcel->getActiveSheet()->getStyle($range)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER); 
$objPHPExcel->getActiveSheet()->getStyle($range)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$column = PHPExcel_Cell::stringFromColumnIndex(((2*$datediff)+2));
$row = 2;
$cell = $column.$row;
$range = 'A2:'.$cell; 
$objPHPExcel->getActiveSheet()->getStyle($range)->getFill()->applyFromArray(array(
        'type' => PHPExcel_Style_Fill::FILL_SOLID,
        'startcolor' => array(
             'rgb' => 'F28A8C' //Yellow
        )
));
////////////////
///////////////////////

$arr_user = array();
$sql_loc=mysqli_query($link1,"SELECT * FROM pjp_data WHERE task='Dealer Visit' AND ".$sql_date." ORDER BY plan_date");
while($row_loc = mysqli_fetch_array($sql_loc)){
	
	$arr_user[$row_loc['assigned_user']][$row_loc["plan_date"]] =  $row_loc["visit_area"];
}
$row = 3;
$col = 0;
foreach($arr_user as $userid => $arrval){
	$admin_detail = explode("~",getAdminDetails($userid,"name",$link1));
	$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $row, $admin_detail[0]);
	$col++;
	for($d=0; $d <= $datediff; $d++){
		$pdate = date("Y-m-d", strtotime ( "+".$d."days" , strtotime ( $from_date ) ) );
		$objPHPExcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($col, $row, $arrval[$pdate]);
		$col++;
		$col++;
	}
	$col = 0;
	$row++;
}
////////////////////////////////
///// apply border on export sheet
$styleArray = array(
  'borders' => array(
    'allborders' => array(
      'style' => PHPExcel_Style_Border::BORDER_MEDIUM
    )
  )
);
$cell = $column.($row-1);
$range = 'A1:'.$cell;
$objPHPExcel->getActiveSheet()->getStyle($range)->applyFromArray($styleArray);
unset($styleArray);
///////////////////////////////////////////////
// Rename worksheet
$objPHPExcel->getActiveSheet()->setTitle('Summary Sheet');
// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Redirect output to a client’s web browser (Excel2007)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="PJP Plan Vs Ach.xlsx"');
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
