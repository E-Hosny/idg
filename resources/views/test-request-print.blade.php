<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <title>@if(!empty($redeliveryFromLabPrint) && isset($redelivery))إعادة التسليم — دفعة #{{ $redelivery->id }} — @elseif(!empty($redeliveryFromLabPrint))إعادة التسليم للاستقبال — @elseif(!empty($labDeliveryFile))ملف التسليم للمختبر — @elseطلب اختبار — @endif{{ $testRequest->receiving_record_no }}</title>
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            direction: rtl;
            text-align: right;
            background: white;
            color: #333;
            line-height: 1.4;
        }

        /* Print Styles */
        @media print {
            @page {
                size: A4 landscape;
                margin: 1cm;
            }
            
            body {
                font-size: 10px;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
                min-height: 100vh;
                margin: 0 !important;
                padding: 0 !important;
            }

            .container.print-doc-wrapper {
                display: flex !important;
                flex-direction: column;
                min-height: calc(100vh - 2cm);
                box-sizing: border-box;
            }

            .print-content-body {
                flex: 1 1 auto;
            }

            .print-page-tail {
                flex-shrink: 0;
                margin-top: auto;
                padding-top: 8px;
            }

            .terms-conditions-print tbody td {
                font-size: 9px !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            .no-print {
                display: none !important;
            }

            .print-break {
                page-break-before: always;
            }

            table {
                border-collapse: collapse !important;
            }
        }

        /* Screen Styles */
        @media screen {
            body {
                max-width: 297mm;
                margin: 20px auto;
                padding: 20px;
                background: #f5f5f5;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                box-sizing: border-box;
            }

            .container {
                background: white;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                border-radius: 8px;
            }

            .print-doc-wrapper.container {
                flex: 1;
                display: flex;
                flex-direction: column;
                min-height: calc(100vh - 40px);
            }

            .print-content-body {
                flex: 1 1 auto;
            }

            .print-page-tail {
                margin-top: auto;
                flex-shrink: 0;
            }
        }

        /* Container */
        .container {
            width: 100%;
            max-width: none;
        }

        /* Document header strip (matches dashboard test request header) */
        .doc-header-bar {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-height: 52px;
            padding: 10px 20px;
            margin-bottom: 15px;
            background: #f2f2f2;
            border-top: 1px solid #000;
            border-left: 1px solid #000;
            border-right: 1px solid #000;
            border-bottom: 1px dotted #000;
            box-sizing: border-box;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .doc-header-bar .doc-header-title {
            position: absolute;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-family: "Times New Roman", Times, serif;
            font-weight: 700;
            color: #000;
            font-size: 18px;
            line-height: 1.2;
            text-align: center;
            white-space: nowrap;
            max-width: 55%;
            overflow: hidden;
            text-overflow: ellipsis;
            pointer-events: none;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .doc-header-bar .doc-header-docno {
            font-family: "Times New Roman", Times, serif;
            font-weight: 700;
            color: #000;
            font-size: 18px;
            line-height: 1.2;
            white-space: nowrap;
            flex-shrink: 0;
            z-index: 1;
            padding-right: 8px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .doc-header-logo-wrap {
            flex-shrink: 0;
            z-index: 1;
            background: #fff;
            padding: 6px;
            border: 1px solid #ddd;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .doc-header-logo-wrap img {
            width: 48px;
            height: 48px;
            display: block;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Customer Info Section */
        .info-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #333;
            margin-bottom: 15px;
        }

        .info-table td {
            border: 1px solid #333;
            padding: 8px;
            font-size: 11px;
        }

        .label {
            background-color: #f5f5f5;
            font-weight: bold;
            width: 25%;
            text-align: center;
        }

        .value {
            width: 25%;
            text-align: center;
        }

        /* Items Section */
        .section-title {
            background-color: #f5f5f5;
            border: 2px solid #333;
            padding: 10px;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .total-info {
            background-color: #f0f0f0;
            padding: 5px;
            font-size: 11px;
            text-align: center;
            margin-bottom: 8px;
            border: 1px solid #333;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #333;
            margin-bottom: 15px;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #333;
            padding: 6px;
            text-align: center;
            font-size: 9px;
            vertical-align: middle;
        }

        .items-table th {
            background-color: #e5e5e5;
            font-weight: bold;
        }

        .terms-conditions-row td {
            border-bottom: 1px solid #ccc !important;
        }

        .terms-conditions-row:last-child td {
            border-bottom: 1px solid #333 !important;
        }

        /* Terms & Conditions: EN left/LTR, AR right/RTL (ignore page RTL flips) */
        .terms-conditions-print {
            font-size: 9px;
            direction: ltr;
        }

        .terms-conditions-print thead th:first-child,
        .terms-conditions-print tbody td:first-child {
            direction: ltr !important;
            text-align: left !important;
            unicode-bidi: isolate;
        }

        .terms-conditions-print thead th:last-child,
        .terms-conditions-print tbody td:last-child {
            direction: rtl !important;
            text-align: right !important;
            unicode-bidi: isolate;
        }

        .terms-conditions-print tbody td {
            vertical-align: top !important;
            line-height: 1.45 !important;
        }

        .terms-conditions-print thead th {
            font-size: 10px;
        }

        /* Delivery Documentation (2×6 grid) */
        .delivery-section {
            margin-top: 15px;
        }

        .delivery-doc-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-top: 0;
        }

        .delivery-doc-table .delivery-doc-heading {
            background-color: #f5f5f5;
            border-bottom: 2px solid #000;
            padding: 10px 8px;
            text-align: center;
            font-weight: bold;
            font-size: 13px;
            font-family: "Times New Roman", Times, serif;
            color: #000;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .delivery-doc-table td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 11px;
            vertical-align: middle;
            color: #000;
        }

        .delivery-doc-table .delivery-doc-label {
            font-weight: bold;
            font-family: "Times New Roman", Times, serif;
            text-align: left;
            width: 14%;
        }

        .delivery-doc-table .delivery-doc-date {
            text-align: center;
            font-weight: bold;
            font-family: "Times New Roman", Times, serif;
        }

        .delivery-doc-table .delivery-doc-sig-box {
            min-height: 48px;
            background: #fff;
            border: 1px solid #333;
        }

        /* Push delivery + contact strip to bottom when printing */
        .print-doc-wrapper {
            display: block;
        }

        .print-contact-footer {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            align-items: flex-end;
            gap: 12px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #000;
            font-size: 9px;
            line-height: 1.35;
            color: #000;
        }

        .print-contact-footer .cfa {
            flex: 1 1 0;
            text-align: left;
            min-width: 0;
        }

        .print-contact-footer .cfd {
            flex: 1 1 0;
            text-align: right;
            min-width: 0;
        }

        .print-page-tail {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        /* Redelivery from lab → reception (extended footer) */
        .redelivery-footer-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-top: 10px;
        }
        .redelivery-footer-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 11px;
            vertical-align: middle;
            color: #000;
        }
        .redelivery-section-heading {
            background-color: #f5f5f5;
            font-weight: bold;
            text-decoration: underline;
            text-align: center;
            font-size: 13px;
            font-family: "Times New Roman", Times, serif;
            padding: 10px 8px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .redelivery-count-cell {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            font-family: "Times New Roman", Times, serif;
        }

        /* Print Button */
        .print-button {
            position: fixed;
            top: 20px;
            left: 20px;
            background: #4f46e5;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .print-button:hover {
            background: #3730a3;
        }

        .close-button {
            position: fixed;
            top: 20px;
            left: 150px;
            background: #6b7280;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            z-index: 1000;
        }

        .close-button:hover {
            background: #4b5563;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 10px;
            }
            
            .info-table td,
            .items-table th,
            .items-table td,
            .delivery-doc-table td {
                font-size: 9px;
                padding: 4px;
            }
        }
    </style>
</head>
<body>
    <!-- Print & Close Buttons (hidden when printing) -->
    <button onclick="window.print()" class="print-button no-print">
        🖨️ طباعة / Print
    </button>
    <button onclick="window.close()" class="close-button no-print">
        ❌ إغلاق / Close
    </button>

    <div class="container print-doc-wrapper">
        <div class="print-content-body">
        <!-- Document header (form code left, title center, logo right; LTR strip on RTL page) -->
        <div dir="ltr" class="doc-header-bar">
            @if(!empty($labDeliveryFile))
                <span class="doc-header-docno">HOT - F04</span>
                <span class="doc-header-title">Samples Delivery Record</span>
            @else
                <span class="doc-header-docno">HOT - F03</span>
                <span class="doc-header-title">TEST Request</span>
            @endif
            <div class="doc-header-logo-wrap">
                @if(file_exists(public_path('images/idg_logo.jpg')))
                    <img src="{{ asset('images/idg_logo.jpg') }}" alt="IDG Logo">
                @else
                    <div style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:bold;color:#000;">IDG</div>
                @endif
            </div>
        </div>

        <!-- Customer Information -->
        @if(!empty($labDeliveryFile))
        <table class="info-table">
            <tr>
                <td class="label">كود العميل<br>Customer Code</td>
                <td class="value">{{ $formattedCustomer['customer_code'] ?? '-' }}</td>
                <td class="label">رقم سجل الاستلام<br>Receiving Record No</td>
                <td class="value">{{ $testRequest->receiving_record_no ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">تاريخ الاستلام<br>Received Date</td>
                <td class="value">
                    {{ $testRequest->received_date ? \Carbon\Carbon::parse($testRequest->received_date)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}
                </td>
                <td class="label">تم الاستلام بواسطة<br>Received By</td>
                <td class="value">{{ $testRequest->received_by ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">استلم في<br>Received In</td>
                <td class="value">{{ $testRequest->received_in ?? '-' }}</td>
                <td class="label">ملاحظات<br>Notes</td>
                <td class="value">{{ $testRequest->notes ? $testRequest->notes : '-' }}</td>
            </tr>
        </table>
        @else
        <table class="info-table">
            <tr>
                <td class="label">اسم العميل<br>Customer Name</td>
                <td class="value">{{ $formattedCustomer['full_name'] ?? '-' }}</td>
                <td class="label">رقم سجل الاستلام<br>Receiving Record No</td>
                <td class="value">{{ $testRequest->receiving_record_no ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">كود العميل<br>Customer Code</td>
                <td class="value">{{ $formattedCustomer['customer_code'] ?? '-' }}</td>
                <td class="label">تاريخ الاستلام<br>Received Date</td>
                <td class="value">
                    {{ $testRequest->received_date ? \Carbon\Carbon::parse($testRequest->received_date)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}
                </td>
            </tr>
            <tr>
                <td class="label">رقم الجوال<br>Mobile No</td>
                <td class="value">{{ $formattedCustomer['phone'] ?? '-' }}</td>
                <td class="label">البريد الإلكتروني<br>Email</td>
                <td class="value">{{ $formattedCustomer['email'] ?? '-' }}</td>
                <td class="label">تم الاستلام بواسطة<br>Received By</td>
                <td class="value">{{ $testRequest->received_by ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">المدينة/العنوان<br>City/Address</td>
                <td class="value">{{ $formattedCustomer['address'] ?? '-' }}</td>
                <td class="label">استلم في<br>Received In</td>
                <td class="value">{{ $testRequest->received_in ?? '-' }}</td>
            </tr>
        </table>
        @endif

        <!-- Items Table -->
        <div class="section-title">العناصر | Items</div>
        @php
            // Group artifacts by base code
            $groups = [];
            foreach ($artifacts as $artifact) {
                $code = $artifact->artifact_code;
                // Check if code has a sub-code (e.g., JR6365974581-1)
                if (preg_match('/-\d+$/', $code)) {
                    // Extract base code (e.g., JR6365974581 from JR6365974581-1)
                    $baseCode = preg_replace('/-\d+$/', '', $code);
                    
                    if (!isset($groups[$baseCode])) {
                        $groups[$baseCode] = [
                            'baseCode' => $baseCode,
                            'codes' => [],
                            'items' => []
                        ];
                    }
                    
                    $groups[$baseCode]['codes'][] = $code;
                    $groups[$baseCode]['items'][] = $artifact;
                } else {
                    // Single artifact without sub-code
                    $groups[$code] = [
                        'baseCode' => $code,
                        'codes' => [$code],
                        'items' => [$artifact]
                    ];
                }
            }
            
            // Convert to array and format for display
            $groupedArtifacts = [];
            foreach ($groups as $group) {
                $sortedCodes = $group['codes'];
                sort($sortedCodes);
                $firstArtifact = $group['items'][0];
                
                $displayCode = $group['baseCode'];
                if (count($sortedCodes) > 1) {
                    // Extract numbers from codes (e.g., JR6365974581-1 -> 1)
                    $numbers = [];
                    foreach ($sortedCodes as $code) {
                        if (preg_match('/-(\d+)$/', $code, $matches)) {
                            $numbers[] = intval($matches[1]);
                        }
                    }
                    sort($numbers);
                    
                    if (count($numbers) > 0) {
                        $minNum = min($numbers);
                        $maxNum = max($numbers);
                        $displayCode = $group['baseCode'] . ' (' . $minNum . ' - ' . $maxNum . ')';
                    }
                }
                
                $groupedArtifacts[] = [
                    'artifact' => $firstArtifact,
                    'display_code' => $displayCode,
                    'ids' => array_map(function($item) { return $item->id; }, $group['items']),
                    'count' => count($group['items'])
                ];
            }
        @endphp
        <div class="total-info">المجموع | Total: {{ count($groupedArtifacts) }} عنصر | items</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 10%;">الكود<br>Code</th>
                    <th style="width: 12%;">النوع<br>Type</th>
                    <th style="width: 15%;">الخدمة<br>Service</th>
                    <th style="width: 12%;">نوع التسليم<br>Delivery Type</th>
                    <th style="width: 12%;">تاريخ التسليم<br>Delivery Date</th>
                    <th style="width: 12%;">الوزن<br>Weight</th>
                    <th style="width: 15%;">ملاحظات<br>Notes</th>
                    <th style="width: 12%;">الحالة<br>Status</th>
                </tr>
            </thead>
            <tbody>
                @if(count($groupedArtifacts) > 0)
                    @foreach($groupedArtifacts as $index => $group)
                        @php
                            $artifact = $group['artifact'];
                            $weight = $artifact->weight ? $artifact->weight . ' ' . ($artifact->unit_type === 'carat' ? 'ct' : 'gm') : '-';
                            $status = ucfirst($artifact->status ?? 'pending');
                            $statusAr = '';
                            $currentStatus = $artifact->status ?? 'pending';
                            
                            switch($currentStatus) {
                                case 'pending': $statusAr = 'قيد الانتظار'; break;
                                case 'under_evaluation': $statusAr = 'قيد التقييم'; break;
                                case 'evaluated': $statusAr = 'تم التقييم'; break;
                                case 'certified': $statusAr = 'معتمد'; break;
                                case 'signed': $statusAr = 'موقع'; break;
                                default: $statusAr = $status;
                            }
                            
                            // حساب تاريخ الاستلام المتوقع
                            $expectedDate = '-';
                            if ($artifact->expected_date) {
                                $expectedDate = \Carbon\Carbon::parse($artifact->expected_date)->format('d/m/Y');
                            } elseif ($artifact->delivery_type) {
                                $today = \Carbon\Carbon::now();
                                switch($artifact->delivery_type) {
                                    case 'Regular':
                                        // بعد 7 أيام عمل (تجنب الجمعة)
                                        $date = $today->copy();
                                        $addedDays = 0;
                                        while ($addedDays < 7) {
                                            $date->addDay();
                                            if ($date->dayOfWeek !== 5) { // تجنب الجمعة
                                                $addedDays++;
                                            }
                                        }
                                        $expectedDate = $date->format('d/m/Y');
                                        break;
                                    case 'Express Service':
                                    case 'Same Day':
                                        // نفس اليوم - إذا كان جمعة، اجعله السبت
                                        $date = $today->copy();
                                        if ($date->dayOfWeek === 5) { // إذا كان جمعة
                                            $date->addDay(); // انتقل إلى السبت
                                        }
                                        $expectedDate = $date->format('d/m/Y');
                                        break;
                                    case '24 hours':
                                        // الغد (تجنب الجمعة)
                                        $date = $today->copy();
                                        $addedDays = 0;
                                        while ($addedDays < 1) {
                                            $date->addDay();
                                            if ($date->dayOfWeek !== 5) {
                                                $addedDays++;
                                            }
                                        }
                                        $expectedDate = $date->format('d/m/Y');
                                        break;
                                    case '48 hours':
                                        // بعد الغد (تجنب الجمعة)
                                        $date = $today->copy();
                                        $addedDays = 0;
                                        while ($addedDays < 2) {
                                            $date->addDay();
                                            if ($date->dayOfWeek !== 5) {
                                                $addedDays++;
                                            }
                                        }
                                        $expectedDate = $date->format('d/m/Y');
                                        break;
                                    case '72 hours':
                                        // بعد 3 أيام عمل (تجنب الجمعة)
                                        $date = $today->copy();
                                        $addedDays = 0;
                                        while ($addedDays < 3) {
                                            $date->addDay();
                                            if ($date->dayOfWeek !== 5) {
                                                $addedDays++;
                                            }
                                        }
                                        $expectedDate = $date->format('d/m/Y');
                                        break;
                                }
                            }
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-family: monospace;">{{ $group['display_code'] }}</td>
                            <td>{{ $artifact->type ? ($artifact->subtype ? $artifact->type . ' - ' . $artifact->subtype : $artifact->type) : '-' }}</td>
                            <td style="font-size: 8px;">{{ $artifact->service ?? '-' }}</td>
                            <td>{{ $artifact->delivery_type ?? '-' }}</td>
                            <td style="font-size: 8px;">{{ $expectedDate }}</td>
                            <td>{{ $weight }}</td>
                            <td style="font-size: 8px;">
                                {{ strlen($artifact->notes ?? '-') > 30 ? substr($artifact->notes, 0, 30) . '...' : ($artifact->notes ?? '-') }}
                            </td>
                            <td style="font-size: 8px;">{{ $statusAr }}<br>{{ $status }}</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 15px; color: #666;">
                            لا توجد عناصر مسجلة<br>No items found
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <!-- Terms and Conditions (not shown on lab delivery file) -->
        @if(empty($labDeliveryFile))
        <div style="margin-top: 15px;">
            <div class="section-title">الشروط والأحكام | Terms and Conditions</div>
            <table class="items-table terms-conditions-print" dir="ltr">
                <thead>
                    <tr style="background-color: #f3f4f6;">
                        <th style="width: 50%; text-align: left; padding: 8px;">Terms and Conditions:</th>
                        <th style="width: 50%; text-align: right; padding: 8px;" dir="rtl">:الشروط والأحكام</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Term 1 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            IDG Laboratory Reports represent an independent professional opinion based on non-destructive examinations performed on the submitted item(s) as received, using advanced laboratory equipment and the expert judgment of qualified gemologists. Reports are not guarantees, appraisals, or valuations. IDG Laboratory does not alter, clean, or remove settings without the Client's prior written authorization. Where the condition or mounting of an item limits examination, such limitations will be stated in the report, and IDG Laboratory shall not be liable for any resulting impact on the accuracy of the findings.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            تمثل تقارير مختبر IDG رأياً فنياً مستقلاً يستند إلى الفحص غير الإتلافي للعينات المقدمة بالحالة التي استلمها بها المختبر، وذلك باستخدام أجهزة مخبرية متقدمة والخبرة المهنية لأخصائيي الأحجار الكريمة المؤهلين. ولا تُعد هذه التقارير ضماناً أو تقييماً مالياً أو تثميناً للقيمة. لا يقوم مختبر IDG بتعديل أو تنظيف أو فك الأحجار أو أجزاء المجوهرات دون الحصول على موافقة خطية مسبقة من العميل. وفي حال كانت حالة العينة أو طريقة تركيبها تحد من إمكانية الفحص، فسيتم توضيح هذه القيود في التقرير، ولا يتحمل مختبر IDG أي مسؤولية عن أي تأثير قد تسببه تلك القيود على دقة أو شمولية نتائج الفحص.
                        </td>
                    </tr>
                    <!-- Term 2 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            IDG Laboratory issues Certificates / Reports only for items determined to be of natural origin. If examination indicates a synthetic origin, undisclosed treatment, or inconclusive results using non-destructive methods, IDG may issue a report stating the findings and any limitations or provide a Verification Letter. Any identified treatments will be disclosed in the Comments section of the report. IDG Laboratory shall not be liable for any consequences resulting from the Client's decision not to authorize additional testing.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يصدر مختبر IDG الشهادات أو التقارير فقط للعينات التي يثبت أنها ذات منشأ طبيعي. وإذا أظهر الفحص أن العينة ذات منشأ صناعي، أو تحتوي على معالجة غير مفصح عنها، أو تعذر التوصل إلى نتيجة حاسمة باستخدام طرق الفحص غير الإتلافية، فيجوز للمختبر إصدار تقرير يوضح نتائج الفحص والقيود ذات الصلة، أو إصدار خطاب تحقق حسب الحالة. يتم الإفصاح عن أي معالجات يتم اكتشافها في قسم الملاحظات في التقرير. ولا يتحمل مختبر IDG أي مسؤولية عن أي نتائج أو تبعات تترتب على قرار العميل بعدم الموافقة على إجراء اختبارات إضافية عند الحاجة.
                        </td>
                    </tr>
                    <!-- Term 3 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            The Client warrants that all submitted stone(s), diamond(s), jewellery, or articles are lawfully owned or submitted with the owner's authorization and are free from legal claims or disputes. IDG Laboratory's findings and Certificates/Reports apply only to the specific item(s) examined. Unless otherwise instructed in writing, the examined item(s) will be returned with the issued Certificate/Report. IDG Laboratory accepts no liability for the origin, ownership, or any legal disputes relating to the submitted item(s). The Client shall indemnify IDG Laboratory against any claims arising from false or incomplete ownership declarations.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يقر العميل ويضمن أن جميع الأحجار الكريمة أو الألماس أو المجوهرات أو القطع المقدمة مملوكة له ملكية نظامية أو تم تقديمها بموافقة وتفويض من مالكها، وأنها خالية من أي مطالبات أو نزاعات قانونية. وتنطبق نتائج الفحص والشهادات أو التقارير الصادرة عن مختبر IDG حصرياً على العينة أو العينات التي تم فحصها. وما لم يوجه العميل تعليمات خطية بخلاف ذلك، فسيتم إعادة العينة أو العينات المفحوصة مع الشهادة أو التقرير الصادر. ولا يتحمل مختبر IDG أي مسؤولية تتعلق بمنشأ العينة أو ملكيتها أو أي مطالبات أو نزاعات قانونية مرتبطة بها. كما يلتزم العميل بتعويض وإبراء ذمة مختبر IDG من أي مطالبات أو مسؤوليات تنشأ نتيجة تقديم معلومات غير صحيحة أو غير مكتملة بشأن ملكية العينة أو صلاحية تقديمها للمختبر.
                        </td>
                    </tr>
                    <!-- Term 4 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            Any information, documents, or materials provided to IDG Laboratory by a source other than the Client shall remain the confidential property of the original source. IDG Laboratory shall not disclose or use such information except as necessary to perform the requested services, with the original source's written authorization, or as required by law. The Client warrants that they are authorized to submit any third-party information and agrees to indemnify IDG Laboratory against any claims arising from unauthorized submission of such information. Unless otherwise instructed in writing, IDG Laboratory will return the examined item(s) with the issued Report(s). The Client must collect the item(s) within ninety (90) days of the Report issuance date. Unclaimed items may be subject to storage fees. Items remaining unclaimed for two (2) years may be disposed of in accordance with applicable law after reasonable attempts to contact the Client. IDG Laboratory shall not be liable for any loss or damage resulting from storage or the Client's failure to collect the item(s).
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            تظل أي معلومات أو مستندات أو مواد يتم تزويد مختبر IDG بها من قبل جهة غير العميل ملكاً سرياً للجهة الأصلية المقدمة لها. ولا يجوز لمختبر IDG الإفصاح عن تلك المعلومات أو استخدامها إلا بالقدر اللازم لتنفيذ الخدمات المطلوبة، أو بموجب موافقة خطية من الجهة الأصلية، أو إذا كان ذلك مطلوباً بموجب الأنظمة واللوائح المعمول بها. ويقر العميل ويضمن أنه مخول نظاماً بتقديم أي معلومات أو مستندات تخص أطرافاً أخرى، كما يلتزم بتعويض وإبراء ذمة مختبر IDG من أي مطالبات أو مسؤوليات تنشأ نتيجة تقديم معلومات أو مستندات تخص الغير دون الحصول على التفويض أو الصلاحية. ما لم يوجه العميل تعليمات خطية بخلاف ذلك، سيقوم مختبر IDG بإعادة العينة أو العينات المفحوصة مع التقرير أو التقارير الصادرة. ويلتزم العميل باستلام العينة أو العينات خلال تسعين (90) يوماً من تاريخ إصدار التقرير. ويجوز للمختبر فرض رسوم تخزين على العينات التي لا يتم استلامها خلال هذه المدة. أما العينات التي تبقى دون استلام لمدة سنتين (2)، فيجوز للمختبر التصرف فيها وفقاً للأنظمة واللوائح المعمول بها، وذلك بعد بذل محاولات معقولة للتواصل مع العميل. ولا يتحمل مختبر IDG أي مسؤولية عن أي خسائر أو أضرار تنتج عن التخزين أو فشل العميل في استلام العينة أو العينات.
                        </td>
                    </tr>
                    <!-- Term 5 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            IDG Lab's delivery timelines are estimates only. Turnaround may be delayed due to sample condition, extra analyses, client-approved work, specialist referrals, instrument downtime, logistics, customs, or force majeure. If a material delay occurs, the Lab will notify the client with a revised estimate. IDG Lab accepts no liability for any loss or cost arising from such delays unless otherwise agreed in writing.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            تُعد المدد الزمنية التي يحددها مختبر IDG لإنجاز الخدمات تقديرية فقط، وقد تتأخر عملية إصدار النتائج أو التقارير بسبب حالة العينة، أو الحاجة إلى إجراء تحاليل إضافية، أو أعمال يوافق عليها العميل، أو إحالة العينة إلى متخصصين، أو تعطل الأجهزة، أو مشكلات النقل أو التخليص الجمركي، أو حالات القوة القاهرة. وفي حال حدوث تأخير جوهري، سيقوم المختبر بإبلاغ العميل وتزويده بمدة زمنية تقديرية محدثة لإنجاز الخدمة. ولا يتحمل مختبر IDG أي مسؤولية عن أي خسائر أو تكاليف تنشأ نتيجة هذا التأخير، ما لم يتم الاتفاق على خلاف ذلك بموجب اتفاقية خطية.
                        </td>
                    </tr>
                    <!-- Term 6 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            The Client acknowledges that IDG Laboratory performs non-destructive examinations only. All findings, Certificates, Reports, and Verification Letters are based solely on non-destructive analyses and the professional judgment of qualified gemologists. IDG Laboratory does not warrant results that require destructive testing. Any destructive or invasive testing will only be performed with the Client's prior written authorization under a separate written agreement.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يقر العميل بأن مختبر IDG يجري الفحوصات باستخدام طرق غير إتلافية فقط. وتستند جميع النتائج والشهادات والتقارير وخطابات التحقق الصادرة عن المختبر حصرياً إلى الفحوصات غير الإتلافية وإلى الرأي المهني لأخصائيي الأحجار الكريمة المؤهلين. ولا يضمن مختبر IDG أي نتائج تتطلب إجراء اختبارات إتلافية. ولا يجوز إجراء أي اختبار إتلافي أو تدخل في العينة إلا بعد الحصول على موافقة خطية مسبقة من العميل وبموجب اتفاقية خطية منفصلة.
                        </td>
                    </tr>
                    <!-- Term 7 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            The IDG report bears the IDG Laboratory / SAAC / ILAC accreditation mark. The Client shall not reproduce, alter, edit, or use this report, any part of it, or the accreditation mark for advertising or promotional purposes without IDG Laboratory's prior written approval. IDG Laboratory reserves the right to declare a report void if unauthorized reproduction, alteration, or misuse is detected.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يحمل تقرير IDG شعار اعتماد مختبر IDG و SAAC و ILAC. ولا يجوز للعميل نسخ هذا التقرير أو إعادة إنتاجه أو تعديله أو تحريره أو استخدامه، كلياً أو جزئياً، أو استخدام شعار الاعتماد لأغراض إعلانية أو ترويجية دون الحصول على موافقة خطية مسبقة من مختبر IDG. ويحتفظ مختبر IDG بحقه في اعتبار التقرير لاغياً إذا تبين وجود أي نسخ أو تعديل أو استخدام غير مصرح به للتقرير أو لشعار الاعتماد.
                        </td>
                    </tr>
                    <!-- Term 8 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            Mounted items are examined at IDG Laboratory's discretion and only to the extent permitted by their setting. Some tests or measurements may be limited or not possible, and the report will state any examination limitations. IDG Laboratory shall not be liable for any reduction in accuracy resulting from such limitations.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يتم فحص القطع المركبة (المجوهرات المثبت بها أحجار) وفقاً لتقدير مختبر IDG، وبالقدر الذي تسمح به طريقة التركيب. وقد تكون بعض الاختبارات أو القياسات محدودة أو غير ممكنة بسبب التركيب، وسيتم توضيح جميع قيود الفحص في التقرير. ولا يتحمل مختبر IDG أي مسؤولية عن أي انخفاض في دقة النتائج أو محدودية الاستنتاجات الناتجة عن هذه القيود.
                        </td>
                    </tr>
                    <!-- Term 9 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            IDG Lab does not accept articles that are plated, coated, laminated or filled, as these fall outside the scope of ISO 23345:2021. Where coating or non-homogeneity is detected or suspected during the pre-test check, testing is stopped and the item is returned to the customer with the reason recorded. All reports state that results relate only to the items tested and to the surface analysed.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            لا يقبل مختبر IDG القطع المطلية أو المغطاة أو المصفحة أو المملوءة، لكونها تقع خارج نطاق تطبيق المواصفة ISO 23345:2021. وفي حال اكتشاف أو الاشتباه بوجود طلاء أو عدم تجانس في العينة أثناء الفحص الأولي قبل الاختبار، يتم إيقاف الفحص وإعادة القطعة إلى العميل، مع توثيق سبب الرفض. وتنص جميع التقارير على أن نتائج الفحص تنطبق فقط على العينة أو القطعة التي تم اختبارها، وعلى السطح الذي تم تحليله.
                        </td>
                    </tr>
                    <!-- Term 10 — acknowledgement -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            By submitting the item(s), I/We acknowledge and accept IDG Laboratory's Terms and Conditions governing the examination, testing, and issuance of Certificates/Reports for Diamonds, Coloured Stones, Jewellery, and Precious Metals. My/Our signature on this submission form constitutes full and binding acceptance of these Terms and Conditions. All applicable terms and conditions are available on our official website: idg.sa.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يُعد تقديم العينة أو العينات إلى مختبر IDG إقراراً/نقراً بأنني/بأننا قد اطلعنا على الشروط والأحكام الخاصة بالمختبر، وأوافق/نوافق عليها، والتي تنظم إجراءات الفحص والاختبار وإصدار الشهادات والتقارير الخاصة بالألماس والأحجار الكريمة الملونة والمجوهرات والمعادن الثمينة. ويعد توقيعي/توقيعنا على نموذج تقديم العينات موافقة نهائية وملزمة على جميع هذه الشروط والأحكام. جميع الشروط والأحكام المعمول بها متاحة على موقعنا الإلكتروني idg.sa.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif

        </div><!-- /.print-content-body -->

        <!-- Tail: pinned to bottom of sheet when printing -->
        @php
            $todayFormatted = \Carbon\Carbon::now()->format('d/m/Y');
            $deliveryDocCreated = $testRequest->created_at
                ? $testRequest->created_at->format('d/m/Y')
                : $todayFormatted;
        @endphp
        <div class="print-page-tail" dir="ltr">
        <div class="delivery-section">
            @if(!empty($redeliveryFromLabPrint))
            <table class="redelivery-footer-table" dir="ltr">
                <tbody>
                    <tr>
                        <td colspan="6" class="redelivery-section-heading">Delivered Report and Items: التقرير والعناصر المسلمة</td>
                    </tr>
                    <tr>
                        <td class="delivery-doc-label" style="width: 20%;">Delivered pieces:<br>القطع المسلمة</td>
                        <td class="redelivery-count-cell" style="width: 10%;">{{ isset($redelivery) ? $redelivery->delivered_pieces_count : ($evaluatedPiecesCount ?? 0) }}</td>
                        <td class="delivery-doc-label" style="width: 20%;">Pending Pieces:<br>القطع المتبقية</td>
                        <td class="redelivery-count-cell" style="width: 10%;">{{ isset($redelivery) ? $redelivery->remaining_pieces_count : ($pendingPiecesCount ?? 0) }}</td>
                        <td colspan="2" style="background: #fafafa;"></td>
                    </tr>
                    <tr>
                        <td class="delivery-doc-label">Delivered by:<br>المسلّم</td>
                        <td class="delivery-doc-value"></td>
                        <td class="delivery-doc-label">Signature:<br>التوقيع</td>
                        <td class="delivery-doc-value"><div class="delivery-doc-sig-box"></div></td>
                        <td class="delivery-doc-label">Date:<br>التاريخ</td>
                        <td class="delivery-doc-date">{{ $todayFormatted }}</td>
                    </tr>
                    <tr>
                        <td class="delivery-doc-label">Pending Item:<br>القطع المتبقية</td>
                        <td class="delivery-doc-value"></td>
                        <td class="delivery-doc-label">Signature:<br>التوقيع</td>
                        <td class="delivery-doc-value"><div class="delivery-doc-sig-box"></div></td>
                        <td class="delivery-doc-label">Date:<br>التاريخ</td>
                        <td class="delivery-doc-date">{{ $todayFormatted }}</td>
                    </tr>
                    <tr>
                        <td colspan="6" class="redelivery-section-heading">Received Report and Pieces: استلام التقرير والقطع</td>
                    </tr>
                    <tr>
                        <td class="delivery-doc-label">Received by:<br>المستلم</td>
                        <td class="delivery-doc-value"></td>
                        <td class="delivery-doc-label">Signature:<br>التوقيع</td>
                        <td class="delivery-doc-value"><div class="delivery-doc-sig-box"></div></td>
                        <td class="delivery-doc-label">Date:<br>التاريخ</td>
                        <td class="delivery-doc-date">{{ $todayFormatted }}</td>
                    </tr>
                </tbody>
            </table>
            @else
            <table class="delivery-doc-table" dir="ltr">
                <thead>
                    <tr>
                        <th colspan="6" class="delivery-doc-heading">Delivery Documentation | توثيق التسليم</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="delivery-doc-label">Received by:<br>أستلم بواسطة</td>
                        <td class="delivery-doc-value" style="width: 17%;"></td>
                        <td class="delivery-doc-label">Signature:<br>التوقيع</td>
                        <td class="delivery-doc-value" style="width: 20%;"><div class="delivery-doc-sig-box"></div></td>
                        <td class="delivery-doc-label">Date:<br>التاريخ</td>
                        <td class="delivery-doc-date" style="width: 13%;">{{ $deliveryDocCreated }}</td>
                    </tr>
                </tbody>
            </table>
            @endif
        </div>

        <div class="print-contact-footer">
            <div class="cfa">Gate 6, First Floor, Andalus Mall, Olaya Street, 12215, Riyadh, Saudi Arabia</div>
            <div class="cfd">Mobile: +966580583000<br>website: https://idg-lab.com.sa</div>
        </div>
        </div><!-- /.print-page-tail -->

    </div><!-- /.print-doc-wrapper -->

    <script>
        // Check if opened for auto-download (from session flash or URL param)
        const autoDownload = {{ session('auto_download') ? 'true' : 'false' }};
        
        // Check URL params as fallback
        const urlParams = new URLSearchParams(window.location.search);
        const autoDownloadFromUrl = urlParams.get('auto_download') === '1';
        
        const shouldAutoDownload = autoDownload || autoDownloadFromUrl;

        // Instructions box removed

        // Auto-print functionality
        window.addEventListener('load', function() {
            // تأخير طفيف للتأكد من تحميل الصورة والستايل بالكامل
            setTimeout(function() {
                // Focus على الصفحة للتأكد من تفعيل الطباعة
                window.focus();
                
                // If auto-download mode, trigger print dialog automatically
                if (shouldAutoDownload) {
                    window.print();
                }
            }, 500);
        });

        // إضافة keyboard shortcuts
        document.addEventListener('keydown', function(event) {
            // Ctrl+P للطباعة
            if (event.ctrlKey && event.key === 'p') {
                event.preventDefault();
                window.print();
            }
            // Escape للإغلاق
            if (event.key === 'Escape') {
                window.close();
            }
        });

        // Auto-close after printing if in auto-download mode
        if (shouldAutoDownload) {
            window.onafterprint = function() {
                // Don't auto-close to let user choose filename and location
                // setTimeout(function() {
                //     window.close();
                // }, 1000);
            };
        }
    </script>
</body>
</html>
