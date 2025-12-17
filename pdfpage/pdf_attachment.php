<?php 
//include "../includes/dbconnect.php";
include("../MPDF/mpdf.php");
//require('../fpdf181/fpdf.php');
$mpdf=new mPDF('win-1252','A4','2','2',5,5,5,5,0,0); 
/*$mpdf->SetHeader('|INVOICE|');*/
$mpdf->setFooter('{PAGENO}');// Giving page number to your footer.
$mpdf->SetDisplayMode('fullpage','continuous');
$mpdf->SetAutoPageBreak(false);
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
$inv = base64_encode($_REQUEST['invoice_no']);
$text = "Invoice No" ;
$pdfname = $text."-".$_REQUEST['invoice_no'];
//$stylesheet = file_get_contents('../MPDF/tablecss.css');
//$mpdf->WriteHTML($stylesheet,1);	// The parameter 1 tells that this is css/style only and no body/html/text

//$mpdf->SetColumns(2,'J');
$mpdf->WriteHTML($html);
//$mpdf = new FPDF();

$mpdf->AddPage();

$mpdf->$html;

$content = $mpdf->Output($inv.".".pdf,'F');

//$mpdf->Output();
include("../admin/testmail.php");
exit;
?>