<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOT - F05 — Reports &amp; Items Delivery — {{ $testRequest->receiving_record_no }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            background: #fff;
        }
        @media print {
            @page { size: A4 landscape; margin: 1cm; }
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        @media screen {
            body { background: #f3f4f6; padding: 16px; }
            .sheet { max-width: 1100px; margin: 0 auto; background: #fff; padding: 16px; box-shadow: 0 1px 4px rgba(0,0,0,.12); }
        }
        .print-button, .close-button {
            position: fixed;
            top: 12px;
            padding: 8px 14px;
            border: 0;
            border-radius: 6px;
            color: #fff;
            cursor: pointer;
            font-weight: 700;
        }
        .print-button { right: 16px; background: #111827; }
        .close-button { right: 140px; background: #6b7280; }
        .doc-header-bar {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 64px;
            padding: 8px 16px;
            background: #f2f2f2;
            border: 1px solid #000;
            border-bottom-style: dotted;
        }
        .doc-header-docno, .doc-header-title {
            font-family: "Times New Roman", Times, serif;
            font-weight: 700;
            font-size: 16px;
        }
        .doc-header-title {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            line-height: 1.25;
        }
        .doc-header-title .ar { display: block; font-size: 15px; }
        .doc-header-logo-wrap img {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 50%;
            background: #fff;
        }
        table.delivery-log {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        table.delivery-log th, table.delivery-log td {
            border: 1px solid #000;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 10px;
        }
        table.delivery-log th { background: #f7f7f7; font-weight: 700; line-height: 1.3; }
        table.delivery-log td { height: 42px; }
        .sig-line { min-height: 28px; }
        table.prepared {
            width: 100%;
            border-collapse: collapse;
            margin-top: 28px;
        }
        table.prepared td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 11px;
            height: 36px;
        }
        table.prepared .label { font-weight: 700; width: 16%; background: #f7f7f7; }
    </style>
</head>
<body>
    <button onclick="window.print()" class="print-button no-print">طباعة / Print</button>
    <button onclick="window.close()" class="close-button no-print">إغلاق / Close</button>

    <div class="sheet">
        <div class="doc-header-bar" dir="ltr">
            <span class="doc-header-docno">HOT - F05</span>
            <span class="doc-header-title">
                <span class="ar">تسليم التقارير والعناصر</span>
                Reports &amp; Items Delivery
            </span>
            <div class="doc-header-logo-wrap">
                @if(file_exists(public_path('images/idg_logo.jpg')))
                    <img src="{{ asset('images/idg_logo.jpg') }}" alt="IDG">
                @endif
            </div>
        </div>

        <table class="delivery-log" dir="ltr">
            <thead>
                <tr>
                    <th style="width:4%;">No.</th>
                    <th style="width:16%;">Company/customer name<br>اسم العميل/الشركة</th>
                    <th style="width:12%;">Report no.<br>رقم التقرير</th>
                    <th style="width:9%;">Number of Reports<br>عدد التقارير</th>
                    <th style="width:8%;">No. of pieces<br>عدد القطع</th>
                    <th style="width:11%;">Delivered by<br>المسلّم</th>
                    <th style="width:11%;">Received by<br>المستلم</th>
                    <th style="width:9%;">Received Date<br>تاريخ الاستلام</th>
                    <th style="width:10%;">Signature<br>التوقيع</th>
                    <th style="width:10%;">Note<br>ملاحظة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td>{{ $customerLabel }}</td>
                    <td>{{ $reportNumbers }}</td>
                    <td>{{ $reportsCount }}</td>
                    <td>{{ $piecesCount }}</td>
                    <td>{{ $deliveredBy }}</td>
                    <td>{{ $receivedBy }}</td>
                    <td>{{ $receivedDate }}</td>
                    <td class="sig-line"></td>
                    <td>{{ $note }}</td>
                </tr>
            </tbody>
        </table>

        <table class="prepared" dir="ltr">
            <tr>
                <td class="label">Prepared by:<br>أعد بواسطة</td>
                <td>{{ $deliveredBy }}</td>
                <td class="label">Signature:<br>التوقيع</td>
                <td></td>
                <td class="label">Date:<br>التاريخ</td>
                <td>{{ $receivedDate }}</td>
            </tr>
        </table>
    </div>
</body>
</html>
