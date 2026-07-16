<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Vehicle Trip Ticket</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #e9e9e9;
            color: #000;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
        }

        .page {
            width: 216mm;
            min-height: 279mm;
            margin: 12px auto;
            padding: 8mm 10mm;
            background: #fff;
        }

        .center {
            text-align: center;
        }

        .header {
            font-size: 12px;
            line-height: 1.25;
        }

        .header strong {
            font-size: 13px;
        }

        .rule {
            border-top: 3px solid #000;
            border-bottom: 1px solid #000;
            height: 6px;
            margin-top: 6px;
        }

        .meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 8px 0 28px;
            font-size: 18px;
        }

        .meta-right {
            justify-self: end;
            line-height: 1.6;
        }

        .line {
            display: inline-block;
            min-width: 90px;
            border-bottom: 1px solid #000;
            text-align: center;
            padding: 0 6px;
        }

        .form-title {
            margin-bottom: 50px;
            font-weight: 700;
            font-size: 18px;
        }

        table.ticket {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            font-size: 14px;
        }

        .ticket th,
        .ticket td {
            border: 1px solid #000;
            vertical-align: top;
        }

        .ticket th {
            padding: 7px 4px;
            background: #65bfd8;
            font-size: 16px;
            letter-spacing: .2px;
        }

        .cell {
            height: 72px;
            padding: 5px;
        }

        .remarks-cell {
            height: 96px;
        }

        .label {
            font-weight: 700;
            margin-bottom: 22px;
        }

        .value {
            text-align: center;
            word-break: break-word;
        }

        .remarks-value {
            min-height: 58px;
            padding: 4px 10px;
            text-align: center;
            white-space: pre-wrap;
        }

        .list-line {
            display: grid;
            grid-template-columns: 18px 1fr;
            align-items: end;
            gap: 4px;
            margin-top: 4px;
        }

        .list-line span:last-child {
            border-bottom: 1px solid #000;
            min-height: 17px;
            text-align: center;
        }

        .notes {
            margin-top: 8px;
            text-align: center;
            font-size: 12px;
            line-height: 1.7;
        }

        .signatures {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 44mm;
            margin: 86px 12mm 0;
            text-align: center;
            font-size: 13px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            min-height: 18px;
            padding-bottom: 2px;
        }

        .signature-label {
            margin-top: 6px;
        }

        .custodian {
            width: 76mm;
            margin: 42px auto 0;
            text-align: center;
            font-size: 13px;
        }

        .print-actions {
            width: 216mm;
            margin: 16px auto 0;
            text-align: right;
        }

        .print-actions button {
            padding: 7px 14px;
            border: 1px solid #0d6efd;
            border-radius: 4px;
            background: #0d6efd;
            color: #fff;
            cursor: pointer;
        }

        @media print {
            body {
                background: #fff;
            }

            .page {
                width: auto;
                min-height: auto;
                margin: 0;
                padding: 8mm 10mm;
            }

            .print-actions {
                display: none;
            }
        }
    </style>
</head>
<body>
@php
    $getTrip = function ($key, $fallback = '') use ($trip) {
        return isset($trip[$key]) && $trip[$key] !== null && $trip[$key] !== '' ? $trip[$key] : $fallback;
    };

    $custodianName = $history->employeename ?: '';
    $driver = $getTrip('name_of_driver');
    $phone = $getTrip('phone');
    $vehicleType = $history->assetname ?: ($history->vehiclecategory ?: 'Vehicle');
    $date = $history->date ? date('Y-m-d', strtotime($history->date)) : date('Y-m-d');
    $dateBorrowed = $getTrip('date_borrowed', $date);
    $timeDeparture = $getTrip('time_of_departure', $getTrip('time_of_arrival'));
    $departureMileage = $getTrip('departure_mileage', $getTrip('arrival_mileage'));
    $controlNo = isset($history->control_number) ? $history->control_number : '';
    $custodian = $getTrip('custodian', $custodianName);
    $signatureName = $getTrip('borrower_signature_name');
@endphp
<div class="print-actions">
    <button type="button" onclick="window.print()">Print</button>
</div>

<main class="page">
    <div class="header center">
        <div>Republic of the Philippines</div>
        <strong>CITY GOVERNMENT OF MUNTINLUPA</strong>
        <div>City of Muntinlupa</div>
        <strong>DEPARTMENT OF DISASTER RESILIENCE AND MANAGEMENT</strong>
        <div>(Formerly Muntinlupa City Disaster Risk Reduction Management Office)</div>
        <div>Hall of Justice Compound, Resilience Building, Susana Heights, Tunasan, Muntinlupa City</div>
        <div>Tel No.: 8925-43-82</div>
    </div>

    <div class="rule"></div>

    <section class="meta">
        <div>DATE: <span class="line">{{ $date }}</span></div>
        <div class="meta-right">
            <div>CGM-OP-MCDDRM-01F3</div>
            <div>Control #: <span class="line">{{ $controlNo }}</span></div>
        </div>
    </section>

    <div class="form-title center">Borrowed Form</div>

    <table class="ticket">
        <tr>
            <th colspan="3">VEHICLE TRIP TICKET.</th>
        </tr>
        <tr>
            <td class="cell">
                <div class="label">Name of Driver:</div>
                <div class="value">{{ $driver }}</div>
            </td>
            <td class="cell">
                <div class="label">Destination(s):</div>
                <div class="value">{{ $getTrip('destination') }}</div>
            </td>
            <td class="cell">
                <div class="label">Vehicle Plate Number/ Conduction Sticker:</div>
                <div class="value">{{ $history->assettag }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell">
                <div class="label">Phone #:</div>
                <div class="value">{{ $phone }}</div>
            </td>
            <td class="cell">
                <div class="label">Purpose(s):</div>
                <div class="value">{{ $getTrip('purpose') }}</div>
            </td>
            <td class="cell">
                <div class="label">Type of Vehicle:</div>
                <div class="value">{{ $vehicleType }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell">
                <div class="label">Date Borrowed:</div>
                <div class="value">{{ $dateBorrowed }}</div>
            </td>
            <td class="cell">
                <div class="label">Time of Departure:</div>
                <div class="value">{{ $timeDeparture }}</div>
            </td>
            <td class="cell">
                <div class="label">Departure Millage:</div>
                <div class="value">{{ $departureMileage }}</div>
            </td>
        </tr>
        <tr>
            <td class="cell">
                <div class="label" style="margin-bottom: 2px;">Division:</div>
                <div class="list-line"><span>1</span><span>{{ $getTrip('division_1') }}</span></div>
                <div class="list-line"><span>2</span><span>{{ $getTrip('division_2') }}</span></div>
                <div class="list-line"><span>3</span><span>{{ $getTrip('division_3') }}</span></div>
            </td>
            <td class="cell">
                <div class="label" style="margin-bottom: 2px;">Name of passenger:</div>
                <div class="list-line"><span>1</span><span>{{ $getTrip('passenger_1') }}</span></div>
                <div class="list-line"><span>2</span><span>{{ $getTrip('passenger_2') }}</span></div>
                <div class="list-line"><span>3</span><span>{{ $getTrip('passenger_3') }}</span></div>
            </td>
            <td class="cell remarks-cell">
                <div class="label">Remarks:</div>
                <div class="remarks-value">{{ $getTrip('trip_remarks') }}</div>
            </td>
        </tr>
    </table>

    <div class="notes">
        <div>*If by accident, any damage is done to the vehicle, the borrower will be held liable for the damage and liabilities.</div>
        <div>*This trip ticket is applicable only during non-office hour, for non-emergency use and for all type of vehicles.</div>
    </div>

    <section class="signatures">
        <div>
            <div class="signature-line">{{ $signatureName }}</div>
            <div class="signature-label">Signature Over Printed Name</div>
        </div>
        <div>
            <div class="signature-line">{{ $getTrip('supervising_officer') }}</div>
            <div class="signature-label">Supervising Officer</div>
        </div>
    </section>

    <section class="custodian">
        <div class="signature-line">{{ $custodian }}</div>
        <div class="signature-label">Custodian</div>
    </section>
</main>
</body>
</html>
