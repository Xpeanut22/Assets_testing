    <?php
    require('WriteHTML.php');

    $pdf = new PDF_HTML ('P', 'mm', 'LEGAL');
    $pdf->AddPage();
    $pdf->SetAutoPageBreak(FALSE);

    $pdf->AddFont('Calibri', '', 'Calibri-Regular.php');
    $pdf->AddFont('Calibri', 'B', 'Calibri-Bold.php');
    $pdf->AddFont('Calibri', 'BI', 'Calibri-Bold-Italic.php');
    $pdf->AddFont('Calibri', 'I', 'Calibri-Italic.php');

    $pdf->SetFont('Calibri', '', 10);



    //Header
    $pdf->SetXY(0, 7);
    $pdf->cell(216,4,'Republic of the Philippines',0,1,'C');

    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetX(0);
    $pdf->cell(216,4,'CITY GOVERNMENT OF MUNTINLUPA',0,1,'C');

    $pdf->SetFont('Calibri', '', 10);
    $pdf->SetX(0);
    $pdf->cell(216,4,'City of Muntinlupa',0,0,'C');


    $pdf->Image('muntilogo.png',17,5,25,25);
    $pdf->Image('ddrm.png',175,5,24,24);


    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetXY(0,25);
    $pdf->cell(216,4,'DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT',0,1,'C');

    $pdf->SetFont('Calibri', '', 10);
    $pdf->SetX(0);
    $pdf->cell(216,4,'(Formerly Muntinlupa City Disaster Risk Reduction Management Office)',0,1,'C');

    $pdf->SetXY(0,35);
    $pdf->cell(216,4,'Hall of Justice Compound, Resilince Building, Susana Heights, Tunasan, Muntinlupa City',0,1,'C');

    $pdf->SetX(0);
    $pdf->cell(216,4,'Tel No.: 8925-43-82',0,1,'C');

    
    $pdf->SetFont('Calibri', '', 10);
    $pdf->SetXY(160,42);
    $pdf->cell(18,5,'CGM-OP-MCDRRM-01F9',0,0,'L');

    $pdf->SetXY(160,48);
    $pdf->cell(18,5,'CGM No. Are 2025-0124',0,0,'L');


    
    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->SetXY(0,60);
    $pdf->cell(216,4,'AKNOWLEDGEMENT RECEIPT FOR EQUIPMENT ',0,1,'C');

     $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetXY(18, 70);
    $pdf->cell(20,6,'QUANTITY',1,0,'C',);
      $pdf->cell(17,6,' UNIT',1,0,'C',);
     $pdf->cell(105,6,'DESCRIPTION',1,0,'C',);
     $pdf->cell(38,6,' SERIAL NO.',1,1,'C',);


     $pdf->SetFont('Calibri', '', 10);
     $pdf->SetX(18);
      $pdf->cell(20,30,utf8_Decode('$Quantity'),1,0,'C',);
      $pdf->cell(17,30,utf8_Decode('$unit_type'),1,0,'C',);
      $pdf->cell(105,30,utf8_Decode('$description'),1,0,'C',);



             $pdf->SetXY(160, 76);
            $pdf->cell(38,5,utf8_Decode('$Serial_no.'),1,1,'C',);
            $pdf->SetX(160);
            $pdf->cell(38,5,utf8_Decode('$Serial_no.'),1,1,'C',);
 $pdf->SetX(160);
            $pdf->cell(38,5,utf8_Decode('$Serial_no.'),1,1,'C',);
 $pdf->SetX(160);
            $pdf->cell(38,5,utf8_Decode('$Serial_no.'),1,1,'C',);
 $pdf->SetX(160);
            $pdf->cell(38,5,utf8_Decode('$Serial_no.'),1,1,'C',);
 $pdf->SetX(160);
            $pdf->cell(38,5,utf8_Decode('$Serial_no.'),1,1,'C',);

    $pdf->SetXY(18, 106);
    $pdf->cell(180,6,'***nothing follows***',1,0,'C',);

    
                   $pdf->SetXY(18, 112);    
                $pdf->Cell(180,133,'',1,0,);

// ===== 2-PHOTO SIDE-BY-SIDE BLOCK (like your Kenwood sample) =====

$boxX = 18;     // container left
$boxY = 119;    // container top
$boxW = 180;    // container width
$boxH = 120;    // container height

// settings (tweak if needed)
$pad   = 2;     // padding inside container
$gap   = 4;     // space between left and right photos
$imgPad = 1;    // inner padding inside each photo frame

// inner usable area
$innerX = $boxX + $pad;
$innerY = $boxY + $pad;
$innerW = $boxW - ($pad * 2);
$innerH = $boxH - ($pad * 2);

// each photo frame size
$frameW = ($innerW - $gap) / 2;
$frameH = $innerH;

// left frame position
$leftX = $innerX;
$leftY = $innerY;

// right frame position
$rightX = $innerX + $frameW + $gap;
$rightY = $innerY;

// optional: draw container border
// $pdf->Rect($boxX, $boxY, $boxW, $boxH);

// white backgrounds (so it looks clean on top of form)
$pdf->SetFillColor(255,255,255);
$pdf->Rect($leftX,  $leftY,  $frameW, $frameH, 'F');
$pdf->Rect($rightX, $rightY, $frameW, $frameH, 'F');

// frame borders (like your sample image)
$pdf->Rect($leftX,  $leftY,  $frameW, $frameH);
$pdf->Rect($rightX, $rightY, $frameW, $frameH);

// draw images inside frames
$pdf->Image('sample1.png',  $leftX  + $imgPad, $leftY  + $imgPad, $frameW - ($imgPad*2), $frameH - ($imgPad*2));
$pdf->Image('sample2.png', $rightX + $imgPad, $rightY + $imgPad, $frameW - ($imgPad*2), $frameH - ($imgPad*2));




                   $pdf->SetXY(18, 245);    
                $pdf->Cell(90,40,'',1,0,);

                   $pdf->SetXY(108, 245);    
                $pdf->Cell(90,40,'',1,0,);

                

                             //signatories
    $pdf->SetXY(20  ,245);
    $pdf->Cell(20,6,'Received From: ',0,0,'C');
    $pdf->Cell(85,6,'',0,0,'C'); // spacing
    $pdf->SetX(115);
    $pdf->Cell(10,6,'Received From:',0,1,'C');






        date_default_timezone_set('Asia/Manila');
        $printDate = date('F j, Y');

        // ===== POSITION SETTINGS =====
        $x = 35;        // left
        $y = 257;       // top position
        $lineWidth = 58  ;

        // ===== NAME =====
        $name = 'ERWIN O. ALFONSO, Ph.D.';

        $pdf->SetFont('Arial','B',11);
        $pdf->SetXY($x, $y);
        $pdf->Cell($lineWidth,6,$name,0,0,'C');

        // Underline (manual)
        $pdf->Line($x, $y + 6, $x + $lineWidth, $y + 6);

        // ===== POSITION =====
        $pdf->SetFont('Arial','',10);
        $pdf->SetXY($x, $y + 8);
        $pdf->Cell($lineWidth,5,'Department Head - DDRM',0,0,'C');

        // ===== DATE (auto today) =====
        $pdf->SetXY($x, $y + 20);
        $pdf->Cell($lineWidth,5,$printDate,0,0,'C');




// ===== RIGHT COLUMN SETTINGS =====
$columnWidth = 60;     // width ng right column
$x = 125;              // right side position (adjust if needed)
$y = 257;              // vertical position

$name = 'HON. JAIME R. FRESNEDI';
$position = 'Congressman';

// ===== NAME =====
$pdf->SetFont('Arial','B',11);
$pdf->SetXY($x, $y);
$pdf->Cell($columnWidth,6,$name,0,0,'C'); // centered

// UNDERLINE
$pdf->Line($x, $y + 6, $x + $columnWidth, $y + 6);

// ===== POSITION =====
$pdf->SetFont('Arial','',10);
$pdf->SetXY($x, $y + 8);
$pdf->Cell($columnWidth,5,$position,0,0,'C');

     // ===== DATE (auto today) =====
        $pdf->SetXY($x, $y + 20);
        $pdf->Cell($lineWidth,5,$printDate,0,0,'C');

         $pdf->SetXY(20  ,285);
    $pdf->Cell(20,6,'Original Copy ',0,0,'C');





      
    
    $pdf->Output();
    ?>
    