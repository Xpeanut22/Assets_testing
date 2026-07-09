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
$pdf->cell(216,4,'Republic ofthe Philippines',0,1,'C');

$pdf->SetFont('Calibri', 'B', 10);
$pdf->SetX(0);
$pdf->cell(216,4,'CITY GOVERNMENT OF MUNTINLUPA',0,1,'C');

$pdf->SetFont('Calibri', '', 10);
$pdf->SetX(0);
$pdf->cell(216,4,'City of Muntinlupa',0,0,'C');

$pdf->Image('muntilogo.png',17,5,25,25);
$pdf->Image('drlogo.png',175,5,24,24);

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
$pdf->cell(12,5,'DATE:',0,0,'L');
$pdf->cell(20,4,'$date','B',0,'C');

$pdf->cell(110,4,'',0,0,'C'); //SPACING LANG

$pdf->cell(20,4,'CGM-OP-MCDRRM-01F1',0,0,'C');

//disregard
$pdf->SetXY(20,55);
$pdf->cell(12,5,'',0,0,'L');//spacing
$pdf->cell(20,4,'',0,0,'C');//spacing
$pdf->cell(101,4,'',0,0,'C'); //SPACING LANG

$pdf->cell(20,4,'Control No.',0,0,'L');
$pdf->cell(20,3,utf8_decode('$ctrl_no'),'B',0,'C');//Control No.

$pdf->SetFont('Calibri', 'B', 12);
$pdf->SetXY(0,65);
$pdf->cell(216,4,'MATERIALS & EQUIPMENT BORROWER\'S FORM',0,1,'C');

$pdf->SetFont('Calibri', 'B', 10);
$pdf->SetXY(20,75);
$pdf->cell(12,5,'Name:',0,0,'L');
$pdf->SetFont('Calibri', '', 10);
$pdf->cell(88,4,utf8_decode('$name'),'B',1,'L');

$pdf->SetX(20);
$pdf->SetFont('Calibri', 'B', 10);
$pdf->cell(21,5.5,'Department:',0,0,'L');
$pdf->SetFont('Calibri', '', 10);
$pdf->cell(79,4,utf8_decode('$department'),'B',1,'L');

$pdf->SetX(20);
$pdf->SetFont('Calibri', 'B', 10);
$pdf->cell(27,5.5,'Contact Number:',0,0,'L');
$pdf->SetFont('Calibri', '', 10);
$pdf->cell(73,4,utf8_decode('$contact_no'),'B',1,'L');

$text = "I, _____________________________________, hereby claim total responsibility for the proper use and deployment of the
equipment and also it must be kept in good condition and must be kept clean at all times. I understand that if this piece 
of equipment is lost, stolen, damaged etc. I am responsible for its replacement or repair and also I must submit an incident
report outlining what occured during the incident.";

$pdf->SetFont('Calibri', 'B', 10);
$pdf->SetXY(20, 93);
$pdf->MultiCell(192, 4, $text, 0, 'J');

//Name
$pdf->SetXY(24, 92.5);
$pdf->cell(65,4,utf8_decode('$name'),0,1,'C');

$text = "The City Government of Muntinlupa, specially the DDRM, is not responsible for any legal violation may I committed during
the time of borrowing involving or using the described equipment.";

$pdf->SetFont('Calibri', 'B', 10);
$pdf->SetXY(20, 115);
$pdf->MultiCell(186, 4, $text, 0, 'J');


$pdf->SetXY(0,128);
$pdf->cell(216,4,'Material/Equipment Requested',0,1,'C');

$pdf->SetXY(20, 135);
$pdf->SetFillColor(164, 172, 124);
$pdf->cell(8,5,'NO.',1,0,'C',true);
$pdf->cell(70,5,'ITEM(S) DESCRIPTION',1,0,'C',true);
$pdf->cell(20,5,'SERIAL NO.',1,0,'C',true);
$pdf->cell(15,5,'QTY.',1,0,'C',true);
$pdf->cell(20,5,'USED',1,0,'C',true);
$pdf->cell(52,5,'REMARKS',1,1,'C',true);


$pdf->SetX(20);
$pdf->cell(8,6,'1.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(52,6,utf8_decode(''),1,1,'C');

$pdf->SetX(20);
$pdf->cell(8,6,'2.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(52,6,utf8_decode(''),1,1,'C');

$pdf->SetX(20);
$pdf->cell(8,6,'3.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(52,6,utf8_decode(''),1,1,'C');

$pdf->SetX(20);
$pdf->cell(8,6,'4.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(52,6,utf8_decode(''),1,1,'C');

$pdf->SetX(20);
$pdf->cell(8,6,'5.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(52,6,utf8_decode(''),1,1,'C');

//signatories
$pdf->SetXY(20,175);
$pdf->cell(20,6,'RECEIVED BY:',0,0,'C');
$pdf->cell(85,6,'',0,0,'C');//Spacing lang
$pdf->cell(20,6,'CHECKED BY:',0,0,'C');

$pdf->SetXY(20,185);
$pdf->cell(70,6,utf8_decode('$name'),'B',0,'C');
$pdf->cell(40,6,'',0,0,'C');//Spacing lang
$pdf->cell(70,6,'DOMINIC A. NAVARRO',0,1,'C');

$pdf->SetX(20);
$pdf->cell(70,6,'Signature Over Printed Name',0,0,'C');
$pdf->cell(40,6,'',0,0,'C');//Spacing lang
$pdf->cell(70,6,'Section Head - Logistic',0,1,'C');


//signatories
$pdf->SetXY(20,205);
$pdf->cell(20,6,'ISSUED BY:',0,0,'C');
$pdf->cell(85,6,'',0,0,'C');//Spacing lang
$pdf->cell(20,6,'APPROVED BY:',0,0,'C');


$pdf->SetXY(20,215);
$pdf->cell(70,6,utf8_decode('$name'),'B',0,'C');
$pdf->cell(40,6,'',0,0,'C');//Spacing lang
$pdf->cell(70,6,'ERWIN O. ALFONSO',0,1,'C');

$pdf->SetX(20);
$pdf->cell(70,6,'Signature Over Printed Name',0,0,'C');
$pdf->cell(40,6,'',0,0,'C');//Spacing lang
$pdf->cell(70,6,'Department Head - DDRM',0,1,'C');


//RETURN FORM KUYA LUDS HEHE
//LINE
$pdf->SetXY(0,230);
$pdf->SetFillColor(33, 19, 13);
$pdf->cell(216,0.5,'',1,1,'C',true);

$pdf->SetXY(0,231);
$pdf->cell(216,0.2,'',1,1,'C',true);

$pdf->SetFont('Calibri', 'B', 12);
$pdf->SetXY(0,235);
$pdf->cell(216,4,'MATERIALS & EQUIPMENT RETURNED FORM',0,1,'C');
$pdf->SetFont('Calibri', 'B', 10);
$pdf->SetX(0);
$pdf->cell(216,4,'Material/Equipment Returned',0,1,'C');


$pdf->SetXY(20, 250);
$pdf->SetFillColor(164, 172, 124);
$pdf->cell(8,5,'NO.',1,0,'C',true);
$pdf->cell(70,5,'ITEM(S) DESCRIPTION',1,0,'C',true);
$pdf->cell(20,5,'SERIAL NO.',1,0,'C',true);
$pdf->cell(15,5,'QTY.',1,0,'C',true);
$pdf->cell(72,5,'REMARKS',1,1,'C',true);


$pdf->SetX(20);
$pdf->cell(8,6,'1.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(72,6,utf8_decode(''),1,1,'C');

$pdf->SetX(20);
$pdf->cell(8,6,'2.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(72,6,utf8_decode(''),1,1,'C');

$pdf->SetX(20);
$pdf->cell(8,6,'3.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(72,6,utf8_decode(''),1,1,'C');

$pdf->SetX(20);
$pdf->cell(8,6,'4.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(72,6,utf8_decode(''),1,1,'C');

$pdf->SetX(20);
$pdf->cell(8,6,'5.',1,0,'C');
$pdf->cell(70,6,utf8_decode(''),1,0,'C');
$pdf->cell(20,6,utf8_decode(''),1,0,'C');
$pdf->cell(15,6,utf8_decode(''),1,0,'C');
$pdf->cell(72,6,utf8_decode(''),1,1,'C');



//signatories
$pdf->SetXY(20,295);
$pdf->cell(20,6,'RETURNED BY:',0,0,'C');
$pdf->cell(85,6,'',0,0,'C');//Spacing lang
$pdf->cell(20,6,'CHECKED BY:',0,0,'C');

$pdf->SetXY(20,305);
$pdf->cell(70,6,utf8_decode('$name'),'B',0,'C');
$pdf->cell(40,6,'',0,0,'C');//Spacing lang
$pdf->cell(70,6,'DOMINIC A. NAVARRO',0,1,'C');

$pdf->SetX(20);
$pdf->cell(70,6,'Signature Over Printed Name',0,0,'C');
$pdf->cell(40,6,'',0,0,'C');//Spacing lang
$pdf->cell(70,6,'Section Head - Logistic',0,1,'C');


//signatories
$pdf->SetXY(20,320);
$pdf->cell(20,6,'RECEIVED BY:',0,0,'C');

$pdf->SetXY(20,330);
$pdf->cell(70,6,utf8_decode('$name'),'B',0,'C');

$pdf->SetXY(20,335);
$pdf->cell(70,6,'Signature Over Printed Name',0,0,'C');



$pdf->SetXY(160,345);
$pdf->cell(50,6,'CGM-OP-MCDRRM-01F1',0,0,'C');










































































$pdf->Output();
?>