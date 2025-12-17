<?php 
require_once("../includes/common_function.php");
include("../MPDF/mpdf.php");

$mpdf=new mPDF('win-1252','A4','','',5,5,5,5,0,0); 

/*$mpdf->SetHeader('|INVOICE|');*/

$mpdf->setFooter('{PAGENO}');// Giving page number to your footer.

$mpdf->SetDisplayMode('fullpage','continuous');

$mpdf->SetAutoPageBreak(true);

//$mpdf->useOnlyCoreFonts = true;    // false is default

if($_REQUEST[status]=="Invoice Cancelled"){

$mpdf->SetWatermarkText('CANCELLED');

$mpdf->watermark_font = 'DejaVuSansCondensed';

$mpdf->showWatermarkText = true;

}

ob_start();

?>

<?php include "../admin/ledgerpdf_data.php";

 //This is your php page ?>

<?php 

$html = ob_get_contents();

ob_end_clean();

//$stylesheet = file_get_contents('../MPDF/tablecss.css');

//$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

//$mpdf->SetColumns(2,'J');

$mpdf->WriteHTML($html);

$mpdf->Output();

exit;

?>