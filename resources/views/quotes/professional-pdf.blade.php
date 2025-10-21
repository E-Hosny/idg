<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض سعر - {{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</title>
    
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Times New Roman', 'serif', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            direction: rtl;
            background: #fff;
            margin: 0;
            padding: 0;
        }
        
        .document-container {
            width: 100%;
            max-width: 100%;
            background: #fff;
        }
        
        /* Header Section */
        .header {
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .logo-image {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: 2px solid #000;
            object-fit: cover;
        }
        
        .logo-text {
            text-align: right;
        }
        
        .logo-title {
            font-size: 24px;
            font-weight: bold;
            color: #000;
            margin-bottom: 2px;
        }
        
        .logo-subtitle {
            font-size: 10px;
            color: #000;
            font-weight: bold;
        }
        
        .document-title {
            text-align: center;
            margin-bottom: 10px;
        }
        
        .title-main {
            font-size: 28px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        
        .title-sub {
            font-size: 16px;
            color: #000;
            font-weight: bold;
        }
        
        .quote-number {
            font-size: 16px;
            color: #000;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .quote-details {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 11px;
        }
        
        .quote-detail {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quote-detail-label {
            font-weight: bold;
        }
        
        .quote-detail-value {
            color: #000;
        }
        
        /* Information Blocks */
        .info-blocks {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 20px;
        }
        
        .info-block {
            flex: 1;
            border: 1px solid #000;
            padding: 15px;
            background: #fff;
        }
        
        .info-block-title {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .info-block-content {
            font-size: 11px;
            line-height: 1.5;
        }
        
        .info-item {
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: bold;
            color: #000;
        }
        
        .info-value {
            color: #000;
        }
        
        /* Items Table */
        .items-section {
            margin-bottom: 20px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        .items-table th {
            background: #f0f0f0;
            color: #000;
            padding: 8px 4px;
            font-weight: bold;
            text-align: center;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        .items-table td {
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #000;
            font-size: 10px;
        }
        
        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .product-description {
            text-align: right;
            max-width: 200px;
        }
        
        .product-name {
            font-weight: bold;
            color: #000;
        }
        
        .product-id {
            font-size: 9px;
            color: #666;
            font-style: italic;
        }
        
        /* Totals Section */
        .totals-section {
            margin-bottom: 20px;
        }
        
        .totals-title {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .totals-grid {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }
        
        .total-item {
            flex: 1;
            text-align: center;
            padding: 10px;
            border: 1px solid #000;
            background: #fff;
        }
        
        .total-label {
            font-size: 10px;
            font-weight: bold;
            color: #000;
            margin-bottom: 5px;
        }
        
        .total-value {
            font-size: 14px;
            font-weight: bold;
            color: #000;
        }
        
        /* Payment Information */
        .payment-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #000;
            background: #fff;
        }
        
        .payment-title {
            font-size: 14px;
            font-weight: bold;
            color: #000;
            margin-bottom: 10px;
            text-align: center;
        }
        
        .payment-info {
            font-size: 11px;
            line-height: 1.5;
        }
        
        .payment-item {
            margin-bottom: 3px;
        }
        
        .payment-label {
            font-weight: bold;
            color: #000;
        }
        
        .payment-value {
            color: #000;
        }
        
        /* Currency formatting */
        .currency {
            font-weight: bold;
        }
        
        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .document-container {
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="document-container">
        <!-- Header Section -->
        <div class="header">
            <div class="header-top">
                <div class="logo-section">
                    @if(file_exists(public_path('images/idg_logo.jpg')))
                        <img class="logo-image" src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/idg_logo.jpg'))) }}" alt="IDG Logo" />
                    @else
                        <div class="logo-image" style="background: #f0f0f0; display: flex; align-items: center; justify-content: center; font-size: 20px; font-weight: bold;">IDG</div>
                    @endif
                    <div class="logo-text">
                        <div class="logo-title">IDG</div>
                        <div class="logo-subtitle">INTERNATIONAL DIAMOND & GEMSTONE LABORATORY</div>
                    </div>
                </div>
            </div>
            
            <div class="document-title">
                <div class="title-main">عرض سعر</div>
                <div class="title-sub">Quotations</div>
                <div class="quote-number">#{{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</div>
            </div>
            
            <div class="quote-details">
                <div class="quote-detail">
                    <span class="quote-detail-label">رقم المرجع :</span>
                    <span class="quote-detail-value">{{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</span>
                </div>
                <div class="quote-detail">
                    <span class="quote-detail-label">تاريخ الاصدار :</span>
                    <span class="quote-detail-value">{{ \Carbon\Carbon::parse($quote['issue_date'] ?? now())->format('Y-m-d') }}</span>
                </div>
                <div class="quote-detail">
                    <span class="quote-detail-label">تاريخ التوريد :</span>
                    <span class="quote-detail-value">{{ \Carbon\Carbon::parse($quote['expiry_date'] ?? now()->addDays(30))->format('Y-m-d') }}</span>
                </div>
            </div>
        </div>
        
        <!-- Information Blocks -->
        <div class="info-blocks">
            <div class="info-block">
                <div class="info-block-title">Lab. Information</div>
                <div class="info-block-content">
                    <div class="info-item">
                        <span class="info-label">شركة فرع شركة مختبر اي دي جي لتحليل الالماس والاحجار الكريمة ش ذ م م</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">عنوان</span><br>
                        <span class="info-value">Adress : Gate 6, First Floor, Andalus Mall, Olaya Street, 12215. Riyadh, Saudi Arabia</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">الجوال</span><br>
                        <span class="info-value">+966580583000</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">السجل التجاري</span><br>
                        <span class="info-value">1010984664</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">الرقم الضريبي</span><br>
                        <span class="info-value">312404527400003</span>
                    </div>
                </div>
            </div>
            
            <div class="info-block">
                <div class="info-block-title">Customer Information</div>
                <div class="info-block-content">
                    <div class="info-item">
                        <span class="info-label">اسم العميل</span><br>
                        <span class="info-value">
                            @if($customer)
                                {{ $customer['display_name'] ?? $customer['name'] ?? 'غير محدد' }}
                            @else
                                غير محدد
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">العنوان</span><br>
                        <span class="info-value">
                            @if($customer && isset($customer['address']) && !empty($customer['address']))
                                {{ $customer['address'] }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">الجوال</span><br>
                        <span class="info-value">
                            @if($customer && isset($customer['phone']) && !empty($customer['phone']))
                                {{ $customer['phone'] }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">الرقم الضريبي</span><br>
                        <span class="info-value">
                            @if($customer && isset($customer['tax_number']) && !empty($customer['tax_number']))
                                {{ $customer['tax_number'] }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Items Table -->
        @if(isset($quote['line_items']) && count($quote['line_items']) > 0)
        <div class="items-section">
            <table class="items-table">
                <thead>
                    <tr>
                        <th>م</th>
                        <th>الوصف</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الخصم</th>
                        <th>الإجمالي قبل الضريبة</th>
                        <th>الضريبة %</th>
                        <th>قيمة الضريبة</th>
                        <th>الإجمالي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote['line_items'] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="product-description">
                            @php
                                $product_name = 'منتج غير محدد';
                                $product_name_ar = '';
                                $product_id = $item['product_id'] ?? 'غير محدد';
                                
                                // حاول الحصول على اسم المنتج من البيانات المتاحة
                                if(isset($products) && is_array($products) && isset($products[$item['product_id']])) {
                                    $product_data = $products[$item['product_id']];
                                    $product_name = $product_data['name'] ?? $product_data['display_name'] ?? $product_data['title'] ?? $product_name;
                                    $product_name_ar = $product_data['name_ar'] ?? $product_data['description_ar'] ?? '';
                                } elseif(isset($item['product_name']) && !empty($item['product_name'])) {
                                    $product_name = $item['product_name'];
                                    $product_name_ar = $item['product_name_ar'] ?? '';
                                } elseif(isset($item['product_display_name']) && !empty($item['product_display_name'])) {
                                    $product_name = $item['product_display_name'];
                                } elseif(isset($item['description']) && !empty($item['description'])) {
                                    $product_name = $item['description'];
                                } elseif(isset($item['title']) && !empty($item['title'])) {
                                    $product_name = $item['title'];
                                }
                            @endphp
                            <div class="product-name">{{ $product_name }}</div>
                            @if(!empty($product_name_ar))
                                <div class="product-name-ar">{{ $product_name_ar }}</div>
                            @endif
                            <div class="product-id">ID: {{ $product_id }}</div>
                        </td>
                        <td>{{ $item['quantity'] ?? 1 }}</td>
                        <td class="currency">{{ number_format($item['unit_price'] ?? 0, 2) }} ريال</td>
                        <td>{{ $item['discount_percentage'] ?? 0 }}%<br>{{ number_format($item['total_discount'] ?? 0, 2) }} ريال</td>
                        <td class="currency">{{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0), 2) }} ريال</td>
                        <td>%{{ $item['vat_percentage'] ?? 15 }}</td>
                        <td class="currency">{{ number_format(((($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0)) * ($item['vat_percentage'] ?? 15)) / 100, 2) }} ريال</td>
                        <td class="currency">{{ number_format((($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0)) + (((($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0)) * ($item['vat_percentage'] ?? 15)) / 100), 2) }} ريال</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        
        <!-- Totals Section -->
        <div class="totals-section">
            <div class="totals-title">ملخص المبالغ:</div>
            <div class="totals-grid">
                @php
                    $calculated_subtotal = 0;
                    if(isset($quote['line_items'])) {
                        foreach($quote['line_items'] as $item) {
                            $item_subtotal = ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0);
                            $calculated_subtotal += $item_subtotal;
                        }
                    }
                    $tax_rate = 0.15; // 15% VAT
                    $tax_amount = $calculated_subtotal * $tax_rate;
                    $total_amount = $calculated_subtotal + $tax_amount;
                @endphp
                
                <div class="total-item">
                    <div class="total-label">الإجمالي بعد الخصم</div>
                    <div class="total-value">{{ number_format($quote['subtotal'] ?? $calculated_subtotal, 2) }} ريال</div>
                </div>
                <div class="total-item">
                    <div class="total-label">ضريبة القيمة المضافة 15%</div>
                    <div class="total-value">{{ number_format($quote['tax_amount'] ?? $tax_amount, 2) }} ريال</div>
                </div>
                <div class="total-item">
                    <div class="total-label">الإجمالي شامل ضريبة القيمة المضافة</div>
                    <div class="total-value">{{ number_format($quote['total_amount'] ?? $total_amount, 2) }} ريال</div>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        <div class="payment-section">
            <div class="payment-title">معلومات الدفع</div>
            <div class="payment-info">
                <div class="payment-item">
                    <span class="payment-label">البنك Bank:</span>
                    <span class="payment-value">بنك الانماء Alinma Bank</span>
                </div>
                <div class="payment-item">
                    <span class="payment-label">رقم الحساب :</span>
                    <span class="payment-value">68205460090000</span>
                </div>
                <div class="payment-item">
                    <span class="payment-label">الايبان :</span>
                    <span class="payment-value">SA0805000068205460090000</span>
                </div>
                <div class="payment-item">
                    <span class="payment-label">اسم الحساب:</span>
                    <span class="payment-value">شركة فرع شركة مختبر أي دي جي لتحليل الألماس والاحجار الكريمة</span>
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>