<?php
require("fpdf/fpdf.php");
$pdf = new FPDF();
$pdf->SetMargins(20, 20, 20);

$pdf->AddPage("", "legal");
$pdf->AliasNbPages();

//border
$pdf->SetLineWidth(0.7);
$pdf->Rect(20, 20, 175, 311);

//header
$pdf->Image('img/Nagcarlan.png', 25, 25, 30, 0);
$pdf->Image('img/Bagong Pilipinas - Blue.png', 160, 25, 30, 0);
$pdf->SetFont("Arial", "", 11);
$pdf->Cell(0, 15, "Republic of the Philippines", 0, 1, "C");
$pdf->Cell(0, -4, "Province of Laguna", 0, 1, "C");
$pdf->SetFont("Arial", "B", 11);
$pdf->Cell(0, 15, "MUNICIPAL GOVERNMENT OF NAGCARLAN", 0, 1, "C");
$pdf->AddFont('Diploma', '', 'Diploma.php');
$pdf->SetFont('Diploma', '', 18);
$pdf->Cell(0, 0, "Office of the Municipal Treasurer", 0, 1, "C");

$pdf->SetLineWidth(0.7);
$pdf->Line(20, 57, 195, 57);
$pdf->SetFont("Arial", "b", 15);
$pdf->Cell(0, 30, "APPLICATION FOR RETIREMENT", 0, 1, "C");
$pdf->Line(20, 65, 195, 65);

$pdf->SetLineWidth(0.3);
$pdf->Line(20, 72, 70, 72);
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(50, -1, "Date", 0, 1, "C");

$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 20, "DELMA C. ARMAMENTO", 0, 1, "L");
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(0, -11, "Acting Municipal Treasurer", 0, 1, "L");
$pdf->Cell(0, 20, "Nagcarlan, Laguna", 0, 1, "L");

$pdf->Cell(0, 5, "Dear Sir/Madam:", 0, 1, "L");
$y = $pdf->SetY(110);
$x = $pdf->SetX(22);
$pdf->MultiCell(170,5,'                     Pursuant to the provision of Section 2B.04 Article B of the Revised Revenue Code of Nagcarlan, Laguna. I am herewith applying for the retirement of the business described below:',0,"J");

$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 20, "OWNER                                    :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, -7, "BUSINESS NAME                    :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, 19, "BUSINESS NATURE                :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, -6, "ADDRESS OF BUSINESS       :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, 18, "ADDRESS OF OWNER            :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, -7, "LAST PERMIT NUMBER         :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, 18, "LAST GROSS DECLARATION:_____________________________________________________", 0, 1, "R");

$y = $pdf->SetY(175);
$x = $pdf->SetX(22);
$pdf->SetFont("Arial", "", 10);
$pdf->MultiCell(170,5,'                     Submitted herewith for cancellation, together with the above named Mayor\'s Permit are the photocopies of the following Official Receipt/s, covering the payment of the business and taxes for the current year of latest payment and latest photocopies of Mayor\'s Permit.',0,"J");

$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 15, "O.R. NUMBER                           :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, -3, "O.R. DATE                                 :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, 14, "Amount Paid                             :_____________________________________________________", 0, 1, "R");
$pdf->Cell(0, 5, "_________________________________", 0, 1, "R");
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(165, 5, "Printed Name and Signature", 0, 1, "R");
$pdf->SetFont("Arial", "B", 10);
$pdf->Cell(0, 13, "_________________________________", 0, 1, "R");
$pdf->SetFont("Arial", "", 10);
$pdf->Cell(165, -3, "Printed Name and Signature", 0, 1, "R");
$pdf->Cell(155, 12, "Representative", 0, 1, "R");

$pdf->SetLineWidth(0.7);
$pdf->Line(20, 290, 195, 290);
$pdf->Image('img/MANTRA-A.png', 28, 295, 160, 0);

$pdf->SetFont("Arial", "BI", 10);
$y = $pdf->SetY(310);
$x = $pdf->SetX(30);
$pdf->Cell(0, 0, "Address: ", 0, 1, "");

$pdf->SetFont("Arial", "I", 10);
$y = $pdf->SetY(310);
$x = $pdf->SetX(47);
$pdf->Cell(0, 0, "Rizal Ave., Poblacion 2, Nagcarlan, Laguna", 0, 1, "");

$pdf->SetFont("Arial", "BI", 10);
$y = $pdf->SetY(315);
$x = $pdf->SetX(30);
$pdf->Cell(0, 0, "Tel. No.: ", 0, 1, "");

$pdf->SetFont("Arial", "I", 10);
$y = $pdf->SetY(315);
$x = $pdf->SetX(47);
$pdf->Cell(0, 0, "(049)563-1201, (049)-543-2236", 0, 1, "");

$pdf->SetFont("Arial", "BI", 10);
$y = $pdf->SetY(320);
$x = $pdf->SetX(30);
$pdf->Cell(0, 0, "Email: ", 0, 1, "");

$pdf->SetFont("Arial", "I", 10);
$y = $pdf->SetY(320);
$x = $pdf->SetX(47);
$pdf->Cell(0, 0, "nagcarlantreasureroffice@gmail.com", 0, 1, "");

$pdf->Output();

