<?php

require_once('TCPDF/tcpdf.php');

$url = '';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

if ($response === false) {
    die('Error');
}

$pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('PDF Manual');
$pdf->SetHeaderData('', 0, 'PDF Manual', '');
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

$pdf->AddPage();

$pdf->SetFont('times', '', 10);

$pdf->writeHTML($response, true, false, true, false, '');

$pdf->Output('file.pdf', 'D');
