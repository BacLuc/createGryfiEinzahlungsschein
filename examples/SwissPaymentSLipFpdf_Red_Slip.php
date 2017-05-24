<?php
/**
 * Swiss Payment Slip FPDF
 *
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 * @copyright 2012-2015 Some nice Swiss guys
 * @author Marc W?rth <ravage@bluewin.ch>
 * @author Manuel Reinhard <manu@sprain.ch>
 * @author Peter Siska <pesche@gridonic.ch>
 * @link https://github.com/ravage84/SwissPaymentSlipFpdf
 */
?>

<?php
// Measure script execution/generating time
$time_start = microtime(true);
// Make sure the classes get auto-loaded
require __DIR__ . '/../vendor/autoload.php';
// Import necessary classes
use SwissPaymentSlip\SwissPaymentSlip\RedPaymentSlipData;
use SwissPaymentSlip\SwissPaymentSlip\RedPaymentSlip;
use SwissPaymentSlip\SwissPaymentSlipFpdf\PaymentSlipFpdf;
use fpdf\FPDF;
// Make sure FPDF has access to the additional fonts
define('FPDF_FONTPATH', __DIR__ . '/../src/Resources/font');
// Create an instance of FPDF, setup default settings


$lager = array(
    "Pfi La" => 100.00
    ,"So La" => 200.00
    ,"Chla La" => 80.00
);

foreach($lager as $name => $amount){
    createSlip($name,$amount);
}




/**
 * @param $amount
 * @param $fPdf
 * @param $reason
 */
function createSlip($reason,$amount)
{
    $fPdf = new FPDF('P', 'mm', array(210,106));
// Add OCRB font to FPDF
    $fPdf->AddFont('OCRB10');
// Add page, don't break page automatically
    $fPdf->AddPage();
    $fPdf->SetAutoPageBreak(false);
// Insert a dummy invoice text, not part of the payment slip itself
    $fPdf->SetFont('Helvetica', '', 9);
// Create a payment slip data container (value object)
    $paymentSlipData = new RedPaymentSlipData();
// Fill the data container with your data
//$paymentSlipData->setBankData('Seldwyla Bank', '8021 Z?rich');
    $paymentSlipData->setWithBank(false);
    $paymentSlipData->setWithPayer(false);
    $paymentSlipData->setAccountNumber('80-30209-9');
    $paymentSlipData->setRecipientData(
        'Pfadfinderabteilung'
        , 'Gryfenberg'
        , '8050 Zürich'
        , "CH83 0900 0000 8003 0209 8"
    );
//$paymentSlipData->setIban('CH3808888123456789012');
    $paymentSlipData->setAmount($amount);
//$paymentSlipData->setPaymentReasonData('Pfi-La');
//$paymentSlipData->setWithPaymentReason(true);

// Create a payment slip object, pass in the prepared data container
    $paymentSlip = new RedPaymentSlip($paymentSlipData, 0, 0);//191
// Create an instance of the FPDF implementation
    $paymentSlipFpdf = new PaymentSlipFpdf($fPdf, $paymentSlip);
// "Print" the slip with its elements according to their attributes
    $paymentSlipFpdf->createPaymentSlip($paymentSlip);

    $fPdf->SetXY(125, 10);
    $fPdf->Cell(40, 6, $reason);

// Output PDF named example_fpdf_red_slip.pdf to examples folder
    $pdfName = str_replace(" ", "_", $reason) . '_einzahlungsschein.pdf';

    $pdfPath = __DIR__ . DIRECTORY_SEPARATOR . $pdfName;
    $fPdf->Output($pdfPath);
}