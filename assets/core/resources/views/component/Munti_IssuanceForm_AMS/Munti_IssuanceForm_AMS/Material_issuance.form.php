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

    //LINE
    $pdf->SetXY(12,43.5);
    $pdf->SetFillColor(33, 19, 13);
    $pdf->cell(192,0.5,'',1,1,'C',true);

    $pdf->SetXY(12,45);
    $pdf->cell(192,0.2,'',1,1,'C',true);

    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetXY(20,50);
    $pdf->cell(10,5,'DATE:',0,0,'L');
    $pdf->cell(20,4,'$date.','B',0,'C');
    


    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetXY(160,50);
    $pdf->cell(18,5,'CGM-OP-MCDRRM-01F2',0,0,'L');


    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetXY(160,56);
    $pdf->cell(18,5,'Control No.',0,0,'L');
    $pdf->cell(20,4,'$Cotrol_no.','B',0,'C');

    $pdf->cell(110,4,'',0,0,'C'); //SPACING LANG

    //disregard
    $pdf->SetXY(20,50);
    $pdf->cell(12,5,'',0,0,'L');//spacing
    $pdf->cell(20,4,'',0,0,'C');//spacing
    $pdf->cell(101,4,'',0,0,'C'); //SPACING LANG



    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->SetXY(0,65);
    $pdf->cell(216,4,'MATERIALS & EQUIPMENT ISSUANCE`S FORM  ',0,1,'C');

      $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetXY(20,80);
    $pdf->cell(12,5,'Name:',0,0,'L');
    $pdf->cell(100,4,'$name.','B',1,'C');

        $pdf->SetXY(20,85);
       $pdf->cell(20,7,'Department:',0,0,'L');
    $pdf->cell(92,5,'$Dept.','B',0,'C');

         $pdf->SetXY(20,90);
       $pdf->cell(27,7,'Contact Number:',0,0,'L');
    $pdf->cell(85,5,'$cont_num.','B',0,'C');
    
                

        $pdf->SetFont('Calibri', '', 12);
            $pdf->SetXY(20,100);


                // "I," text
            // $pdf->SetX(10);
            $pdf->Cell(6,6,'I, ',0,0,'L');

            // Blank underline space for name
            $pdf->SetLineWidth(0.3);
            $pdf->Cell(69,5,utf8_decode(''),'B',0,'L');


            $text = "hereby claim total responsibility for the described equipment. I understand that if this piece of equipment is lost, stolen, damaged, etc., that I am responsible for its replacement or repair.";

            $cut = "hereby claim total responsibility for the described equipment.";

            // Split text
            $firstPart = $cut;
            $secondPart = substr($text, strlen($cut));

            $pdf->SetFont('Arial', '', 11);

            // FIRST LINE
            $pdf->SetXY(95, 100);
            $pdf->MultiCell(125 , 8, utf8_decode($firstPart), 0, 'J');

            // Get Y position after first multicell
            $y = $pdf->GetY(20);

            // SECOND LINE (new X position)
            $pdf->SetXY(20, $y);
            $pdf->MultiCell(180, 6, utf8_decode(trim($secondPart)), 0, 'J');

            $pdf->SetXY(20, 125);
            $pdf->MultiCell(180,6,
            'The City Government of Muntinlupa, especially the DDRM, is not responsible for any legal violation that may arise from the use or involvement of the issued tools, accessories, and equipment.'
            );


            
    $pdf->SetFont('Calibri', 'B', 12);
    $pdf->SetXY(0,150);
    $pdf->cell(216,4,'  Material/Equipment Requested',0,1,'C');


    $pdf->SetFont('Calibri', 'B', 10);
    $pdf->SetXY(20, 160);
    $pdf->SetFillColor(174, 170, 136);
    $pdf->cell(20,10,'No.',1,0,'C',true);
     $pdf->cell(100,10,'Item(s)s Description',1,0,'C',true);
    $pdf->cell(27,10,'Quantity',1,0,'C',true);
        $pdf->cell(33,10,' Remarks',1,1,'C',true);

    

    $pdf->SetX(20);
    $pdf->cell(20,10,'1.',1,0,'C',);
     $pdf->cell(100,10,' ',1,0,'C',);
    $pdf->cell(27,10,'',1,0,'C',);
        $pdf->cell(33,10,' ',1,1,'C',);

           $pdf->SetX(20);
    $pdf->cell(20,10,'2.',1,0,'C',);
     $pdf->cell(100,10,' ',1,0,'C',);
    $pdf->cell(27,10,'',1,0,'C',);
        $pdf->cell(33,10,' ',1,1,'C',);

           $pdf->SetX(20);
    $pdf->cell(20,10,'3.',1,0,'C',);
     $pdf->cell(100,10,' ',1,0,'C',);
    $pdf->cell(27,10,'',1,0,'C',);
        $pdf->cell(33,10,' ',1,1,'C',);

           $pdf->SetX(20);
    $pdf->cell(20,10,'4.',1,0,'C',);
     $pdf->cell(100,10,' ',1,0,'C',);
    $pdf->cell(27,10,'',1,0,'C',);
        $pdf->cell(33,10,' ',1,1,'C',);

           $pdf->SetX(20);
    $pdf->cell(20,10,'5.',1,0,'C',);
     $pdf->cell(100,10,' ',1,0,'C',);
    $pdf->cell(27,10,'',1,0,'C',);
        $pdf->cell(33,10,' ',1,1,'C',);
       

           //signatories
    $pdf->SetFont('Calibri', '', 10);
    $pdf->SetXY(20,250);
    $pdf->Cell(20,6,'RECEIVED BY:',0,0,'C');
    $pdf->Cell(85,6,'',0,0,'C'); // spacing
    $pdf->Cell(20,6,'CHECKED BY:',0,1,'C');

    $pdf->SetXY(20,260);
    $pdf->cell(70,6,utf8_decode('$name'),0,0,'C'); // LEFT NAME
    $pdf->cell(40,6,'',0,0,'C'); // spacing
    $pdf->cell(70,6,utf8_decode('ALMOND G. GREGORIO'),0,1,'C'); // RIGHT NAME

    $yLine = $pdf->GetY() - 1;
    $pdf->Line(20, $yLine, 90, $yLine);    // RECEIVED BY line
    $pdf->Line(130, $yLine, 200, $yLine);  // CHECKED BY line

    $pdf->SetX(20);
    $pdf->Cell(70,6,'Signature Over Printed Name',0,0,'C');
    $pdf->Cell(40,6,'',0,0,'C'); // spacing
    $pdf->Cell(70,6,'Section Head - Logistic',0,1,'C');




    
             //signatories
    $pdf->SetXY(20,290);
    $pdf->Cell(20,6,'ISSUED BY:',0,0,'C');
    $pdf->Cell(85,6,'',0,0,'C'); // spacing
    $pdf->Cell(20,6,'APPROVED BY:',0,1,'C');
    $pdf->SetXY(20,300);
    $pdf->cell(70,6,utf8_decode('$Name'),0,0,'C'); // LEFT NAME
    $pdf->cell(40,6,'',0,0,'C'); // spacing
    $pdf->cell(70,6,utf8_decode('ERWIN O. ALFONSO'),0,1,'C'); // RIGHT NAME

    $yLine = $pdf->GetY() - 1;
    $pdf->Line(20, $yLine, 90, $yLine);    // RECEIVED BY line
    $pdf->Line(130, $yLine, 200, $yLine);  // CHECKED BY line

    $pdf->SetX(20);
    $pdf->Cell(70,6,'Signature Over Printed Name',0,0,'C');
    $pdf->Cell(40,6,'',0,0,'C'); // spacing
    $pdf->Cell(70,6,'Department Head - DDRM',0,1,'C');

        $pdf->Image('CGM FOOTER.png',0,336,219,20);





    $pdf->Output();
    ?>
    

