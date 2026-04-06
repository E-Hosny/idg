<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <title>طلب اختبار - {{ $testRequest->receiving_record_no }}</title>
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

        /* Terms & Conditions: readable + justified (matches formal document layout) */
        .terms-conditions-print {
            font-size: 9px;
        }

        .terms-conditions-print tbody td {
            vertical-align: top !important;
            text-align: justify !important;
            line-height: 1.45 !important;
        }

        .terms-conditions-print thead th {
            text-align: center !important;
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
            <span class="doc-header-docno">HOT - F03</span>
            <span class="doc-header-title">TEST Request</span>
            <div class="doc-header-logo-wrap">
                @if(file_exists(public_path('images/idg_logo.jpg')))
                    <img src="{{ asset('images/idg_logo.jpg') }}" alt="IDG Logo">
                @else
                    <div style="width:48px;height:48px;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:bold;color:#000;">IDG</div>
                @endif
            </div>
        </div>

        <!-- Customer Information -->
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

        <!-- Terms and Conditions -->
        <div style="margin-top: 15px;">
            <div class="section-title">الشروط والأحكام | Terms and Conditions</div>
            <table class="items-table terms-conditions-print">
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
                            IDG Laboratory Reports provide an independent, professional opinion on the characteristics of the submitted item(s) at the time of examination. Reports are not guarantees, valuations, or appraisals, consistent with international laboratory standards. Reports represent the expert judgment of qualified gemologists using advanced instrumentation and internationally accepted grading systems. IDG examines items as received, without altering, cleaning, or removing settings. Where full access to all facets or inclusions is prevented by mounting, grading accuracy may be affected. In such cases, the Laboratory is not responsible for any limitations affecting the examination results.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يصدر مختبر IDG تقارير تتضمن رأيًا مهنيًا مستقلًا بشأن خصائص القطعة أو القطع المقدمة، وذلك في وقت إجراء الفحص. ولا تُعدّ هذه التقارير ضمانًا، كما لا تُعتبر تحديدًا للقيمة أو تقييمًا سعريًا، وذلك بما يتوافق مع معايير المختبرات الدولية.<br><br>
                            وتعكس هذه التقارير خبرة وتقدير أخصائيي الأحجار الكريمة المؤهلين، اعتمادًا على أجهزة متطورة وأنظمة تصنيف معترف بها دوليًا. ويقوم المختبر بفحص القطع بالحالة التي تُرد بها، دون إجراء أي تعديل أو تنظيف أو فكّ للتثبيت.<br><br>
                            وفي حال تعذّر الوصول الكامل إلى جميع الأوجه أو الشوائب بسبب وجود التثبيت، فقد تتأثر دقة التصنيف. وفي مثل هذه الحالات، لا يتحمل المختبر مسؤولية أي قيود تؤثر في نتائج الفحص.
                        </td>
                    </tr>
                    <!-- Term 2 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            IDG Laboratory may issue a Certificate/Report upon the client’s request only when the tested Stone(s), Diamond(s), and/or Jewellery are determined to be of natural origin.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يصدر مختبر IDG شهادة أو تقرير للقطع المقدمة بناءً على طلب العميل، وذلك فقط عندما يثبت الفحص أن الأحجار أو الألماس و/أو المجوهرات المقدمة ذات منشأ طبيعي.
                        </td>
                    </tr>
                    <!-- Term 3 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            The submitted Stone(s), Diamond(s), and/or Jewellery represent only the specific item(s) examined and will be returned to the client together with the issued Certificate/Report, unless the client requests otherwise. The Laboratory bears no responsibility for the origin, ownership, or source of the submitted item(s). The client confirms that the submitted item is legally owned and free of disputes.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            الأحجار أو الألماس أو المجوهرات المقدمة تمثل فقط العناصر المحددة التي تم فحصها وسيتم إعادتها للعميل مع الشهادة/التقرير الصادر، ما لم يطلب العميل خلاف ذلك. لا يتحمل المختبر أي مسؤولية عن أصل أو ملكية أو مصدر العنصر المقدم. يؤكد العميل أن المنتج المقدم مملوك قانونيا وخالي من النزاعات.
                        </td>
                    </tr>
                    <!-- Term 4 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            Any information provided to the Laboratory that originates from sources other than the client shall be treated as confidential and shall remain the exclusive property of the original source. Such information shall not be disclosed or used by the Laboratory without the prior written consent of the original source.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            أي معلومات تقدم للمختبر وتنشأ من مصادر غير العميل ستعامل بسرية وتظل ملكية حصرية للمصدر الأصلي. لا يجوز الكشف عن هذه المعلومات أو استخدامها من قبل المختبر دون موافقة خطية مسبقة من المصدر الأصلي.
                        </td>
                    </tr>
                    <!-- Term 5 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            All tested item(s) shall be returned to the client together with the Report(s). The client is required to collect the item(s) within ninety (90) days from the date of report issuance. Should the item(s) remain uncollected beyond this period, the Laboratory reserves the right, at its sole discretion, to charge a storage fee, retain the item(s) for research or educational purposes, or dispose of the item(s) without any further notice or liability to the client.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            يجب إعادة جميع العناصر المختبرة إلى العميل مع التقرير. يطلب من العميل استلام العناصر خلال (90) يوما من تاريخ إصدار التقرير. إذا بقيت العناصر غير المستلمة بعد هذه الفترة، يحتفظ المختبر بالحق، حسب تقديره الوحيد، في فرض رسوم تخزين، أو الاحتفاظ بالعناصر لأغراض البحث أو التعليم، أو التخلص من العناصر دون أي إشعار أو مسؤولية إضافية تجاه العميل.
                        </td>
                    </tr>
                    <!-- Term 6 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            Delivery timelines provided by the Laboratory are estimates only. In the event of any delay, the Laboratory will notify the client accordingly.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            جداول التسليم للتقارير التي يحددها المختبر هي تقديرات فقط. في حال حدوث أي تأخير، سيقوم المختبر بإبلاغ العميل بناء على ذلك.
                        </td>
                    </tr>
                    <!-- Term 7 -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            Submission of any item to IDG constitutes acceptance of these Terms &amp; Conditions. For more about IDGL’s terms and conditions, please visit our website. https://idg-lab.com.sa/
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            تقديم أي عنصر إلى IDG يعد قبولا لهذه الشروط والأحكام. لمزيد من المعلومات حول شروط وأحكام IDGL، يرجى زيارة موقعنا الإلكتروني. https://idg-lab.com.sa
                        </td>
                    </tr>
                    <!-- Term 8 — declaration -->
                    <tr class="terms-conditions-row">
                        <td style="padding: 8px 6px;">
                            I/We acknowledge and agree to the Laboratory’s special terms governing the examination and certification/report of Diamond(s), Coloured Stone(s), Jewellery, and Precious Metal(s). By submitting the above-mentioned item(s), I/We confirm that they are deposited under these stated conditions, and my/our signature constitutes full acceptance of these terms.
                        </td>
                        <td style="padding: 8px 6px;" dir="rtl">
                            أقر وأوافق على الشروط الخاصة بالمختبر التي تحكم فحص واعتماد/تقرير الألماس (أو الأحجار) الملونة، والمجوهرات، والمعادن الثمينة. من خلال تقديم العنصر المذكور أعلاه، أؤكد أنا/نحن أنها مودعة بموجب هذه الشروط، وتوقيعي يشكل قبولا كاملا لهذه الشروط.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        </div><!-- /.print-content-body -->

        <!-- Tail: pinned to bottom of sheet when printing -->
        @php
            $deliveryDocCreated = $testRequest->created_at
                ? $testRequest->created_at->format('d/m/Y')
                : now()->format('d/m/Y');
        @endphp
        <div class="print-page-tail" dir="ltr">
        <div class="delivery-section">
            <table class="delivery-doc-table" dir="ltr">
                <thead>
                    <tr>
                        <th colspan="6" class="delivery-doc-heading">Delivery Documentation | توثيق التسليم</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="delivery-doc-label">Delivered by:<br>سلّم بواسطة</td>
                        <td class="delivery-doc-value" style="width: 17%;"></td>
                        <td class="delivery-doc-label">Signature:<br>التوقيع</td>
                        <td class="delivery-doc-value" style="width: 20%;"><div class="delivery-doc-sig-box"></div></td>
                        <td class="delivery-doc-label">Date:<br>التاريخ</td>
                        <td class="delivery-doc-date" style="width: 13%;">{{ $deliveryDocCreated }}</td>
                    </tr>
                    <tr>
                        <td class="delivery-doc-label">Received by:<br>أستلم بواسطة</td>
                        <td class="delivery-doc-value"></td>
                        <td class="delivery-doc-label">Signature:<br>التوقيع</td>
                        <td class="delivery-doc-value" style="text-align: center;">
                            <div class="delivery-doc-sig-box" style="display: flex; align-items: center; justify-content: center; padding: 4px;">
                                @if(file_exists(public_path('maram_sign.png')))
                                    <img src="{{ asset('maram_sign.png') }}" alt="" style="max-height: 48px; max-width: 100%; object-fit: contain;">
                                @endif
                            </div>
                        </td>
                        <td class="delivery-doc-label">Date:<br>التاريخ</td>
                        <td class="delivery-doc-date">{{ $deliveryDocCreated }}</td>
                    </tr>
                </tbody>
            </table>
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
