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

        /* Header Section */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #333;
            margin-bottom: 15px;
        }

        .header-table td {
            border: 1px solid #333;
            padding: 8px;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
        }

        .logo-cell {
            background-color: #f5f5f5;
            width: 12%;
        }

        .logo-cell img {
            width: 50px;
            height: 50px;
            display: block;
            margin: 0 auto;
        }

        .title-cell {
            background-color: #f5f5f5;
            font-size: 16px;
            font-weight: bold;
            width: 40%;
        }

        .info-header {
            background-color: #e5e5e5;
            font-weight: bold;
            font-size: 10px;
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
            
            .header-table td,
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
    
    <!-- Instructions (hidden when printing or in auto-download mode) -->
    <div id="instructions" class="no-print" style="position: fixed; top: 70px; left: 20px; background: #fef3c7; border: 2px solid #f59e0b; padding: 15px; border-radius: 8px; max-width: 350px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); z-index: 999;">
        <div style="font-weight: bold; margin-bottom: 8px; color: #92400e; font-size: 14px;">
            📥 لحفظ كملف PDF / To Save as PDF:
        </div>
        <ol style="margin: 0; padding-right: 20px; color: #78350f; font-size: 13px; line-height: 1.6;">
            <li>اضغط Ctrl+P أو زر "طباعة" أعلاه</li>
            <li>اختر "Save as PDF" من القائمة</li>
            <li>اضغط "Save" لحفظ الملف</li>
        </ol>
        <div style="margin-top: 8px; padding-top: 8px; border-top: 1px solid #f59e0b; font-size: 12px; color: #92400e;">
            ✨ الجودة ممتازة والعربية واضحة!
        </div>
    </div>

    <div class="container">
        <!-- Header Table -->
        <table class="header-table">
            <tr>
                <td class="title-cell" rowspan="3" colspan="3">
                    <div style="font-size: 18px; font-weight: bold;">Test Request</div>
                    <div style="font-size: 16px; margin-top: 4px;">طلب اختبار</div>
                </td>
                <td class="info-header">Prepared by<br>تم التحضير بواسطة</td>
                <td class="info-header">Approved by<br>تم الاعتماد بواسطة</td>
                <td class="logo-cell" rowspan="3">
                    @if(file_exists(public_path('images/idg_logo.jpg')))
                        <img src="{{ asset('images/idg_logo.jpg') }}" alt="IDG Logo">
                    @else
                        <div style="font-size: 12px; font-weight: bold;">IDG</div>
                    @endif
                    <div style="font-size: 8px; margin-top: 4px;">IDG</div>
                </td>
            </tr>
            <tr>
                <td style="font-weight: bold;">Enas Ibrahim</td>
                <td style="font-weight: bold;">Sultan Aldosari</td>
            </tr>
            <tr>
                <td style="font-size: 9px; color: #666;">Lab. Management Supervisor</td>
                <td style="font-size: 9px; color: #666;">Laboratory Manager</td>
            </tr>
            <tr style="background-color: #e5e5e5;">
                <td class="info-header">Document Number<br>رقم المستند</td>
                <td class="info-header">Document Classification<br>تصنيف المستند</td>
                <td class="info-header">Document Level<br>مستوى المستند</td>
                <td class="info-header">Issue No., Revision No<br>رقم الإصدار والمراجعة</td>
                <td class="info-header">Document Date<br>تاريخ المستند</td>
                <td class="info-header">Issue Date<br>تاريخ الإصدار</td>
            </tr>
            <tr>
                <td style="font-weight: bold;">{{ $testRequest->receiving_record_no ?? 'HOT-F03' }}</td>
                <td>Control</td>
                <td>Document</td>
                <td>002, 001</td>
                <td>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
                <td>{{ \Carbon\Carbon::now()->format('d/m/Y') }}</td>
            </tr>
        </table>

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
                <td class="label">تاريخ التسليم<br>Delivery Date</td>
                <td class="value">
                    {{ $testRequest->delivery_date ? \Carbon\Carbon::parse($testRequest->delivery_date)->format('d/m/Y') : '-' }}
                </td>
            </tr>
            <tr>
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
        <div class="total-info">المجموع | Total: {{ $artifacts->count() }} عنصر | items</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">#</th>
                    <th style="width: 12%;">الكود<br>Code</th>
                    <th style="width: 15%;">النوع<br>Type</th>
                    <th style="width: 18%;">الخدمة<br>Service</th>
                    <th style="width: 12%;">نوع التسليم<br>Delivery Type</th>
                    <th style="width: 10%;">الوزن<br>Weight</th>
                    <th style="width: 10%;">السعر<br>Price</th>
                    <th style="width: 13%;">ملاحظات<br>Notes</th>
                    <th style="width: 5%;">الحالة<br>Status</th>
                </tr>
            </thead>
            <tbody>
                @if($artifacts->count() > 0)
                    @foreach($artifacts as $index => $artifact)
                        @php
                            $weight = $artifact->weight ? $artifact->weight . ' ' . ($artifact->unit_type === 'carat' ? 'ct' : 'gm') : '-';
                            $price = $artifact->price ? 'SAR ' . number_format($artifact->price, 2) : '-';
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
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-family: monospace;">{{ $artifact->artifact_code ?? '-' }}</td>
                            <td>{{ $artifact->type ?? '-' }}</td>
                            <td style="font-size: 8px;">{{ $artifact->service ?? '-' }}</td>
                            <td>{{ $artifact->delivery_type ?? '-' }}</td>
                            <td>{{ $weight }}</td>
                            <td style="font-weight: bold;">{{ $price }}</td>
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

        <!-- Delivery Documentation -->
        <div class="delivery-section">
            <div class="section-title">توثيق التسليم | Delivery Documentation</div>
            <table class="delivery-table">
                <tr>
                    <td class="label" style="width: 20%;">تاريخ التسليم<br>Delivery Date</td>
                    <td style="width: 30%; text-align: center; font-weight: bold;">
                        {{ $testRequest->delivery_date ? \Carbon\Carbon::parse($testRequest->delivery_date)->format('d/m/Y') : '-' }}
                    </td>
                    <td class="label" style="width: 20%;">تاريخ الاستلام<br>Received Date</td>
                    <td style="width: 30%; text-align: center; font-weight: bold;">
                        {{ $testRequest->received_date ? \Carbon\Carbon::parse($testRequest->received_date)->format('d/m/Y') : \Carbon\Carbon::now()->format('d/m/Y') }}
                    </td>
                </tr>
                <tr>
                    <td class="label">طريقة التسليم<br>Delivery Method</td>
                    <td style="text-align: center;">-</td>
                    <td class="label">حالة الطلب<br>Request Status</td>
                    <td style="text-align: center; font-weight: bold;">{{ ucfirst($testRequest->status ?? 'pending') }}</td>
                </tr>
                <tr>
                    <td class="label">توقيع التسليم<br>Delivery Signature</td>
                    <td><div class="signature-box"></div></td>
                    <td class="label">توقيع الاستلام<br>Reception Signature</td>
                    <td><div class="signature-box"></div></td>
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

        // Hide instructions if in auto-download mode
        if (shouldAutoDownload) {
            const instructions = document.getElementById('instructions');
            if (instructions) {
                instructions.style.display = 'none';
            }
        }

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
