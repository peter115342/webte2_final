<?php
require_once('TCPDF/tcpdf.php');

$url = 'https://node22.webte.fei.stuba.sk/Manual/index.php';
function generatePDF()
{
    $url = 'https://node22.webte.fei.stuba.sk/Manual/index.php';

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

if ($response === false) {
    die('Error');
}
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
    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('PDF Manual');
    $pdf->SetHeaderData('', 0, 'PDF Manual', '');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->AddPage();


$dom = new DOMDocument();
$dom->loadHTML($response);
    $dom = new DOMDocument();
    $dom->loadHTML($response);

$title = $dom->getElementsByTagName('h1')->item(0);
$pdf->SetFont('freeserif', 'B', 14);
$pdf->writeHTML($title->textContent, true, false, true);
$pdf->writeHTML('<br>', true, false, true);
    $title = $dom->getElementsByTagName('h1')->item(0);
    $pdf->SetFont('freeserif', 'B', 14);
    $pdf->writeHTML($title->textContent, true, false, true);
    $pdf->writeHTML('<br>', true, false, true);

$subtitle1 = $dom->getElementsByTagName('h3')->item(0);
$pdf->SetFont('freeserif', 'B', 12);
$pdf->writeHTML($subtitle1->textContent, true, false, true);
$pdf->writeHTML('<br>', true, false, true);
    $subtitle1 = $dom->getElementsByTagName('h3')->item(0);
    $pdf->SetFont('freeserif', 'B', 12);
    $pdf->writeHTML($subtitle1->textContent, true, false, true);
    $pdf->writeHTML('<br>', true, false, true);

$subtitle1_text = $dom->getElementsByTagName('p')->item(0);
$pdf->SetFont('freeserif', '', 10);
$pdf->writeHTML($subtitle1_text->textContent, true, false, true);
$pdf->writeHTML('<br>', true, false, true);
    $subtitle1_text = $dom->getElementsByTagName('p')->item(0);
    $pdf->SetFont('freeserif', '', 10);
    $pdf->writeHTML($subtitle1_text->textContent, true, false, true);
    $pdf->writeHTML('<br>', true, false, true);

$subtitle2 = $dom->getElementsByTagName('h3')->item(1);
$pdf->SetFont('freeserif', 'B', 12);
$pdf->writeHTML($subtitle2->textContent, true, false, true);
$pdf->writeHTML('<br>', true, false, true);
    $subtitle2 = $dom->getElementsByTagName('h3')->item(1);
    $pdf->SetFont('freeserif', 'B', 12);
    $pdf->writeHTML($subtitle2->textContent, true, false, true);
    $pdf->writeHTML('<br>', true, false, true);

$subtitle2_text = $dom->getElementsByTagName('p')->item(1);
$pdf->SetFont('freeserif', '', 10);
$pdf->writeHTML($subtitle2_text->textContent, true, false, true);
$pdf->writeHTML('<br>', true, false, true);
    $subtitle2_text = $dom->getElementsByTagName('p')->item(1);
    $pdf->SetFont('freeserif', '', 10);
    $pdf->writeHTML($subtitle2_text->textContent, true, false, true);
    $pdf->writeHTML('<br>', true, false, true);

$subtitle3 = $dom->getElementsByTagName('h3')->item(2);
$pdf->SetFont('freeserif', 'B', 12);
$pdf->writeHTML($subtitle3->textContent, true, false, true);
$pdf->writeHTML('<br>', true, false, true);
    $subtitle3 = $dom->getElementsByTagName('h3')->item(2);
    $pdf->SetFont('freeserif', 'B', 12);
    $pdf->writeHTML($subtitle3->textContent, true, false, true);
    $pdf->writeHTML('<br>', true, false, true);

$subtitle3_text = $dom->getElementsByTagName('p')->item(2);
$pdf->SetFont('freeserif', '', 10);
$pdf->writeHTML($subtitle3_text->textContent, true, false, true);
$pdf->writeHTML('<br>', true, false, true);
    $subtitle3_text = $dom->getElementsByTagName('p')->item(2);
    $pdf->SetFont('freeserif', '', 10);
    $pdf->writeHTML($subtitle3_text->textContent, true, false, true);
    $pdf->writeHTML('<br>', true, false, true);

$pdf->Output('file.pdf', 'D');
    $pdf->Output('file.pdf', 'D');
}