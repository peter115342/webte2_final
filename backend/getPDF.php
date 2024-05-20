<?php



function generatePDF_SK()
{
    $url = 'https://node22.webte.fei.stuba.sk/Manual/manSK.php';

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

    function writeElementToPdf($pdf, $element, $font, $size, $style = '')
    {
        if ($element) {
            $pdf->SetFont($font, $style, $size);
            $pdf->writeHTML($element, true, false, true, false, '');
        }
    }

    $title = $dom->getElementsByTagName('h1')->item(0);
    writeElementToPdf($pdf, $dom->saveHTML($title), 'freeserif', 14, 'B');
    $pdf->writeHTML('<br>', true, false, true);

    $sections = $dom->getElementsByTagName('section');
    foreach ($sections as $section) {
        $subtitle = $section->getElementsByTagName('h3')->item(0);
        writeElementToPdf($pdf, $dom->saveHTML($subtitle), 'freeserif', 12, 'B');
        $pdf->writeHTML('<br>', true, false, true);


        $paragraphs = $section->getElementsByTagName('p');
        foreach ($paragraphs as $paragraph) {
            writeElementToPdf($pdf, $dom->saveHTML($paragraph), 'freeserif', 10);
            $pdf->writeHTML('<br>', true, false, true);
        }

        $lists = $section->getElementsByTagName('ul');
        foreach ($lists as $list) {
            writeElementToPdf($pdf, $dom->saveHTML($list), 'freeserif', 10);
            $pdf->writeHTML('<br>', true, false, true);
        }

    }

    $pdf->Output('file.pdf', 'D');
}

function generatePDF_EN()
{
    $url = 'https://node22.webte.fei.stuba.sk/Manual/manEN.php';

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

    function writeElementToPdf($pdf, $element, $font, $size, $style = '')
    {
        if ($element) {
            $pdf->SetFont($font, $style, $size);
            $pdf->writeHTML($element, true, false, true, false, '');
        }
    }

    $title = $dom->getElementsByTagName('h1')->item(0);
    writeElementToPdf($pdf, $dom->saveHTML($title), 'freeserif', 14, 'B');
    $pdf->writeHTML('<br>', true, false, true);

    $sections = $dom->getElementsByTagName('section');
    foreach ($sections as $section) {
        $subtitle = $section->getElementsByTagName('h3')->item(0);
        writeElementToPdf($pdf, $dom->saveHTML($subtitle), 'freeserif', 12, 'B');
        $pdf->writeHTML('<br>', true, false, true);


        $paragraphs = $section->getElementsByTagName('p');
        foreach ($paragraphs as $paragraph) {
            writeElementToPdf($pdf, $dom->saveHTML($paragraph), 'freeserif', 10);
            $pdf->writeHTML('<br>', true, false, true);
        }

        $lists = $section->getElementsByTagName('ul');
        foreach ($lists as $list) {
            writeElementToPdf($pdf, $dom->saveHTML($list), 'freeserif', 10);
            $pdf->writeHTML('<br>', true, false, true);
        }

    }

    $pdf->Output('file.pdf', 'D');
}