<?php


function generatePDF()
{
    $url = 'https://node22.webte.fei.stuba.sk/Manual/index.php';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        http_response_code(500);
        echo json_encode(['message' => 'Error fetching the URL']);
        exit();
    }

    require_once('TCPDF/tcpdf.php');

    $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetTitle('PDF Manual');
    $pdf->SetHeaderData('', 0, 'PDF Manual', '');
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    $pdf->AddPage();

    libxml_use_internal_errors(true);

    $dom = new DOMDocument();
    $dom->loadHTML($response);

    libxml_clear_errors();

    function writeHtmlToPdf($pdf, $element, $font, $size, $style = '') {
        if ($element) {
            $pdf->SetFont($font, $style, $size);
            $pdf->writeHTML($element->textContent, true, false, true);
            $pdf->writeHTML('<br>', true, false, true);
        }
    }

    $title = $dom->getElementsByTagName('h1')->item(0);
    writeHtmlToPdf($pdf, $title, 'freeserif', 14, 'B');

    $subtitles = $dom->getElementsByTagName('h3');
    $texts = $dom->getElementsByTagName('p');

    for ($i = 0; $i < $subtitles->length; $i++) {
        $subtitle = $subtitles->item($i);
        writeHtmlToPdf($pdf, $subtitle, 'freeserif', 12, 'B');

        if ($i < $texts->length) {
            $text = $texts->item($i);
            writeHtmlToPdf($pdf, $text, 'freeserif', 10);
        }
    }

    $pdf->Output('file.pdf', 'D');
}


