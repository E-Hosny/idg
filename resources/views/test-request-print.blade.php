<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            }

            .container {
                background: white;
                padding: 20px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                border-radius: 8px;
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

        /* Delivery Section */
        .delivery-section {
            margin-top: 15px;
        }

        .delivery-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #333;
        }

        .delivery-table td {
            border: 1px solid #333;
            padding: 8px;
            font-size: 11px;
        }

        .signature-box {
            background-color: #fff;
            border: 1px solid #999;
            height: 40px;
            margin-top: 5px;
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
            .delivery-table td {
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

    <div class="container">
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
            <table class="items-table" style="font-size: 8px;">
                <thead>
                    <tr style="background-color: #f3f4f6;">
                        <th style="width: 50%; text-align: left; padding: 8px;">Terms and Conditions:</th>
                        <th style="width: 50%; text-align: right; padding: 8px;" dir="rtl">:الشروط والأحكام</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Term 1 -->
                    <tr>
                        <td style="padding: 6px; vertical-align: top; text-align: left; line-height: 1.3;">
                            IDG Lab(herein after the laboratory) shall not be held responsible for any damage to the above listed Stone(s)/Diamond(s)/Jewellery or part of item, or loss of the stone(s)/ Diamond(s)/Jewellery or part of them while the relevant stone(s)/Diamond(s)/Jewellery are held by the laboratory, unless such damage is caused by IDG Lab or any of its employees
                        </td>
                        <td style="padding: 6px; vertical-align: top; text-align: right; line-height: 1.3;" dir="rtl">
                            لا يتحمل مختبر IDG (ويشار إليه فيما بعد بـ "المختبر") أي مسؤولية عن أي ضرر يلحق بالأحجار/الألماس/المجوهرات المذكورة أعلاه أو أي جزء منها، أو عن فقدانها كلياً أو جزئياً أثناء تواجدها في حيازة المختبر، ما لم يكن الضرر ناتجاً عن المختبر أو أحد موظفيه.
                        </td>
                    </tr>
                    
                    <!-- Term 2 -->
                    <tr>
                        <td style="padding: 6px; vertical-align: top; text-align: left; line-height: 1.3;">
                            IDG Lab is committed to issue Certificate Report(s) if the customer requests that, in one condition only: if the result of stone(s)/diamond(s)/ jewellery tested is natural, but if the testing results prove that stone is not natural, IDG Lab will only give an oral clarification if requested by customer and does not give any paper(s) document(s).
                        </td>
                        <td style="padding: 6px; vertical-align: top; text-align: right; line-height: 1.3;" dir="rtl">
                            يلتزم مختبر IDG بإصدار شهادة/تقرير بناءً على طلب العميل، وذلك بشرط واحد فقط: إذا كانت نتيجة الاختبار للحجر/الألماس/المجوهرات طبيعية، أما إذا أثبتت النتائج أن الحجر غير طبيعي، فسيكتفي فقط بتقديم توضيح شفهي بناءً على طلب العميل، ولن يتم إصدار أي مستندات خطية.
                        </td>
                    </tr>
                    
                    <!-- Term 3 -->
                    <tr>
                        <td style="padding: 6px; vertical-align: top; text-align: left; line-height: 1.3;">
                            - I/We are aware and agree to the special terms of the laboratory examination and certification of diamond(s), coloured stone(s), Jewelery, and Precious Metal(s). We confirm deposition of the above-mentioned stone(s)/diamond(s)/jewellery subject to the above-mentioned condition, and on that I/We signed.
                        </td>
                        <td style="padding: 6px; vertical-align: top; text-align: right; line-height: 1.3;" dir="rtl">
                            أنا/ نقر بأننا على علم ونوافق على الشروط الخاصة بفحص واعتماد الألماس/الأحجار الكريمة/المجوهرات/المعادن الثمينة في المختبر، ونؤكد إيداعنا لما يتبع الأحجار/الألماس/المجوهرات المذكورة أعلاه وفقاً للشروط المذكورة، وقد أقدمنا على ذلك.
                        </td>
                    </tr>
                    
                    <!-- Term 4 -->
                    <tr>
                        <td style="padding: 6px; vertical-align: top; text-align: left; line-height: 1.3;">
                            The stone(s)/diamond(s)/jewellery represents only itself and will be delivered to customer with Certificate/Report(s) issue, unless return is being requested. The laboratory is not responsible for the source of stone(s)/diamond(s)/jewellery.
                        </td>
                        <td style="padding: 6px; vertical-align: top; text-align: right; line-height: 1.3;" dir="rtl">
                            تمثل الأحجار/الألماس/المجوهرات نفسها فقط وسيتم تسليمها للعميل مع الشهادة/التقرير، ما لم يطلب إرجاعها. ولا يتحمل المختبر أي مسؤولية تجاه مصدر الأحجار/الألماس/المجوهرات.
                        </td>
                    </tr>
                    
                    <!-- Term 5 -->
                    <tr>
                        <td style="padding: 6px; vertical-align: top; text-align: left; line-height: 1.3;">
                            Any information provided to the laboratory, which has been obtained from sources other than the customer, is confidential and remains the exclusive property of the original source unless prior consent has been obtained from the original source.
                        </td>
                        <td style="padding: 6px; vertical-align: top; text-align: right; line-height: 1.3;" dir="rtl">
                            جميع المعلومات المقدمة للمختبر والتي تم الحصول عليها من مصادر غير العميل تعتبر معلومات سرية وتبقى ملكية حصرية للمصدر الأصلي ما لم يتم الحصول على موافقة مسبقة من ذلك المصدر الأصلي.
                        </td>
                    </tr>
                    
                    <!-- Term 6 -->
                    <tr>
                        <td style="padding: 6px; vertical-align: top; text-align: left; line-height: 1.3;">
                            Tested items are returned to the customer along with the final report and it will be collected within 90 days. the right laboratory reserves the right to dispose of the item, retain it for research purposes, charge a storage fee without further and any liability to the customer
                        </td>
                        <td style="padding: 6px; vertical-align: top; text-align: right; line-height: 1.3;" dir="rtl">
                            تعاد العناصر المختبرة إلى العميل مع التقرير النهائي، ويجب استلامها خلال 90 يوماً. يحتفظ المختبر بحقه في التخلص من العينة، أو الاحتفاظ بها لأغراض البحث، أو فرض رسوم تخزين دون أي تحمل إضافي أو مسؤولية.
                        </td>
                    </tr>
                    
                    <!-- Term 7 -->
                    <tr>
                        <td style="padding: 6px; vertical-align: top; text-align: left; line-height: 1.3;">
                            Delivery time is estimated if there is any delay we will inform you
                        </td>
                        <td style="padding: 6px; vertical-align: top; text-align: right; line-height: 1.3;" dir="rtl">
                            وقت التسليم تقديري، وفي حال حدوث أي تأخير سنقوم بإبلاغكم
                        </td>
                    </tr>
                    
                    <!-- Term 8 -->
                    <tr>
                        <td style="padding: 6px; vertical-align: top; text-align: left; line-height: 1.3;">
                            For more about IDGL's terms and conditions, please visit our website. https://idg-lab.com.sa/
                        </td>
                        <td style="padding: 6px; vertical-align: top; text-align: right; line-height: 1.3;" dir="rtl">
                            للمزيد من المعلومات حول الشروط والأحكام الخاصة بمختبر IDG، يرجى زيارة موقعنا الإلكتروني https://idg-lab.com.sa/
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Delivery Documentation -->
        <div class="delivery-section">
            <div class="section-title">توثيق التسليم | Delivery Documentation</div>
            <table class="delivery-table">
                <tr>
                    <td class="label" style="width: 20%;">تاريخ الاستلام<br>Received Date</td>
                    <td style="width: 30%; text-align: center; font-weight: bold;">
                        {{ $testRequest->received_date ? \Carbon\Carbon::parse($testRequest->received_date)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}
                    </td>
                    <td class="label" style="width: 20%;">حالة الطلب<br>Request Status</td>
                    <td style="width: 30%; text-align: center; font-weight: bold;">{{ ucfirst($testRequest->status ?? 'pending') }}</td>
                </tr>
                <tr>
                    <td class="label">توقيع التسليم<br>Delivery Signature</td>
                    <td><div class="signature-box"></div></td>
                    <td class="label">توقيع الاستلام<br>Reception Signature</td>
                    <td>
                        @if(file_exists(public_path('maram_sign.png')))
                            <img src="{{ asset('maram_sign.png') }}" alt="Signature" style="max-height: 40px; max-width: 200px; display: block; margin: 0 auto;">
                        @else
                            <div class="signature-box"></div>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="label">ملاحظات إضافية<br>Additional Notes</td>
                    <td colspan="3" style="height: 50px; vertical-align: top; padding-top: 8px;">
                        {{ $testRequest->notes ?? '' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

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
