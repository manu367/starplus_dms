<?php 

require_once("../config/config.php");

//$arrstatus = getFullStatus("master",$link1);

////// filters value/////

//// extract all encoded variables

$ustatus=base64_decode($_REQUEST['status']);

$prodcategory=base64_decode($_REQUEST['prod_cat']);



## selected  Status

if($ustatus!=""){

	$status="status='".$ustatus."'";

}else{

	$status="1";

}

if($prodcategory!=""){

	$prodcat="productid='".$prodcategory."'";

}else{

	$prodcat="1";

}

//////End filters value/////

/** Include PHPExcel */

require_once("../ExcelExportAPI/Classes/PHPExcel.php");



// Create new PHPExcel object

$objPHPExcel = new PHPExcel();



// Set document properties

$objPHPExcel->getProperties()->setCreator("Candour Software")

->setLastModifiedBy("Candour Software")

->setTitle("Office 2007 XLSX Admin Users")

->setSubject("Office 2007 XLSX Admin Users")

->setDescription("PSC info for Office 2007 XLSX.")

->setKeywords("office 2007 openxml php")

->setCategory("Product SubCategory Master");



// Add some data

$objPHPExcel->setActiveSheetIndex(0)

->setCellValue('A1', 'S.No.')

->setCellValue('B1', 'Product SubCat Name')

->setCellValue('C1', 'Product Category')

->setCellValue('D1', 'Status');

/*->setCellValue('E1', 'Create By')

->setCellValue('F1', 'Create Date')

->setCellValue('G1', 'Update By')

->setCellValue('H1', 'Update Date');*/

///// Add Color in header

cellColor('A1:D1', '90EE90');



$i=2;

$count=1;



$sql=mysqli_query($link1,"Select * from product_sub_category where ".$status." and ".$prodcat." order by prod_sub_cat");

while($row_loc = mysqli_fetch_array($sql)){
if($row_loc['status'] == '1'){ $status =  "Active";} else { $status =  "Deactive";}

	//$createby = getAnyDetails($row_loc['createby'],"name","username","admin_users",$link1);

	//$updateby = getAnyDetails($row_loc['updateby'],"name","username","admin_users",$link1);

		$objPHPExcel->setActiveSheetIndex(0)

				->setCellValue('A'.$i, $count)

				->setCellValue('B'.$i, $row_loc['prod_sub_cat'])

				->setCellValue('C'.$i, $row_loc['product_category'])

				->setCellValue('D'.$i, $status);

/*				->setCellValue('E'.$i, $createby)

				->setCellValue('F'.$i, $row_loc['createdate'])

				->setCellValue('G'.$i, $updateby)

				->setCellValue('H'.$i, $row_loc['updatedate']);*/

				

				$i++;	

				$count++;		

}

///// apply border on export sheet

$styleArray = array(

  'borders' => array(

    'allborders' => array(

      'style' => PHPExcel_Style_Border::BORDER_MEDIUM

    )

  )

);



$objPHPExcel->getActiveSheet()->getStyle('A1:D'.($i-1))->applyFromArray($styleArray);

unset($styleArray);



// Rename worksheet

$objPHPExcel->getActiveSheet()->setTitle('productsubcatmaster');





// Set active sheet index to the first sheet, so Excel opens this as the first sheet

$objPHPExcel->setActiveSheetIndex(0);





// Redirect output to a client?s web browser (Excel2007)

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

header('Content-Disposition: attachment;filename="psclist.xlsx"');

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

?>

