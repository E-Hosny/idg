<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>عرض السعر - {{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</title>
    
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
            font-family: 'Times New Roman', serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            direction: rtl;
            background: #fff;
        }
        
        .pdf-container {
            width: 100%;
            max-width: 100%;
            padding: 10px;
            background: #fff;
            overflow: visible;
        }
        
        /* Header Section */
        .header {
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 3px solid #333;
            position: relative;
        }
        
        .logo-section {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .logo-with-text {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            background: linear-gradient(135deg, #fffef8 0%, #f7f6f0 100%);
            border: 3px solid #d4af37;
            border-radius: 20px;
            padding: 15px 25px;
            box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3);
            max-width: 400px;
        }
        
        .logo-image, .logo-placeholder {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #d4af37;
            object-fit: cover;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
        }
        
        .logo-placeholder {
            background: linear-gradient(135deg, #fffef8 0%, #f7f6f0 100%);
        }
        
        .logo-text {
            text-align: center;
        }
        
        .logo-title {
            font-size: 20px;
            margin-bottom: 5px;
            letter-spacing: 1px;
            font-weight: 900;
            color: #d4af37;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
        }
        
        .logo-subtitle {
            font-size: 12px;
            line-height: 1.2;
            margin-bottom: 3px;
            font-weight: bold;
            color: #8b4513;
        }
        
        
        .quote-main-title {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border: 3px solid #333;
            border-radius: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .quote-title {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
            letter-spacing: 2px;
        }
        
        .quote-subtitle {
            font-size: 14px;
            color: #666;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .quote-number {
            font-size: 18px;
            color: #333;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .company-info {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        .company-details {
            background: #f8f9fa;
            padding: 25px;
            border-left: 6px solid #333;
            border-radius: 10px;
            box-shadow: 2px 3px 8px rgba(0,0,0,0.08);
        }
        
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .company-address {
            font-size: 13px;
            line-height: 1.5;
            margin-bottom: 8px;
            color: #555;
        }
        
        .tax-number {
            font-size: 12px;
            font-weight: bold;
            color: #333;
        }
        
        .customer-details {
            background: #f8f9fa;
            padding: 25px;
            border-left: 6px solid #333;
            border-radius: 10px;
            box-shadow: 2px 3px 8px rgba(0,0,0,0.08);
        }
        
        .client-title {
            font-size: 16px;
            font-weight: bold;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 8px;
        }
        
        .client-name {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            text-align: center;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .quote-details-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 30px;
            background: #f8f9fa;
            padding: 25px;
            border: 2px solid #333;
            border-radius: 10px;
            box-shadow: 2px 3px 8px rgba(0,0,0,0.08);
        }
        
        .detail-item {
            text-align: center;
            padding: 10px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 1px 2px 4px rgba(0,0,0,0.05);
        }
        
        .detail-label {
            font-size: 11px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .detail-value {
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }
        
        .quote-reference {
            color: #333;
        }
        
        .quote-date-issue {
            color: #666;
        }
        
        .quote-date-expiry {
            color: #555;
        }
        
        /* Quote Items Section */
        .items-section {
            background: #f8f9fa;
            padding: 25px;
            border: 2px solid #333;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 2px 3px 8px rgba(0,0,0,0.08);
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            margin-bottom: 18px;
            text-align: center;
            padding-bottom: 10px;
            border-bottom: 3px solid #333;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
            letter-spacing: 1px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .items-table th {
            background: linear-gradient(135deg, #d4af37 0%, #b8860b 100%);
            color: #fff;
            padding: 15px 10px;
            font-weight: bold;
            text-align: center;
            font-size: 12px;
            letter-spacing: 0.5px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .items-table th:first-child {
            border-top-right-radius: 10px;
        }
        
        .items-table th:last-child {
            border-top-left-radius: 10px;
        }
        
        .items-table td {
            padding: 12px 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
            background: #fff;
        }
        
        .items-table tr:nth-child(even) {
            background: #f9f9f9;
        }
        
        .items-table tr:last-child td {
            border-bottom: none;
        }
        
        .product-column {
            text-align: right;
            max-width: 200px;
        }
        
        .product-name {
            font-weight: bold;
            color: #333;
            margin-bottom: 3px;
        }
        
        .product-name-ar {
            font-size: 11px;
            color: #666;
            margin-bottom: 3px;
        }
        
        .product-id {
            font-size: 10px;
            color: #999;
            font-style: italic;
        }
        
        .quantity-unit {
            font-weight: bold;
            color: #333;
        }
        
        .unit-price {
            font-weight: bold;
            color: #333;
        }
        
        .discount {
            color: #666;
        }
        
        .vat-percentage {
            color: #555;
        }
        
        .total-before-vat,
        .vat-amount,
        .total-amount {
            font-weight: bold;
            color: #333;
        }
        
        /* Totals Section */
        .totals-section {
            background: #f8f9fa;
            padding: 25px;
            border: 3px solid #333;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        
        .totals-title {
            font-size: 18px;
            font-weight: bold;
            color: #333;
            text-align: center;
            margin-bottom: 15px;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        .totals-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            text-align: center;
        }
        
        .total-item {
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 1px 2px 4px rgba(0,0,0,0.05);
            border: 1px solid #333;
        }
        
        .total-label {
            font-size: 12px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
            text-transform: uppercase;
        }
        
        .total-value {
            font-size: 20px;
            font-weight: bold;
            color: #333;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.1);
        }
        
        /* Quote Status Badge */
        .quote-status {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
            box-shadow: 2px 3px 8px rgba(0,0,0,0.1);
        }
        
        .status-quoted {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .status-draft {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .status-invoiced {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .status-sent {
            background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        .status-expired {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }
        
        
        /* Print Styles */
        @media print {
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            
            .pdf-container {
                margin: 0;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="pdf-container">
        <!-- Header Section -->
        <div class="header">
            <!-- Logo Section -->
            <div class="logo-section">
                <div class="logo-with-text">
                    @if(file_exists(public_path('images/idg_logo.jpg')))
                        <img class="logo-image" src="data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/idg_logo.jpg'))) }}" alt="IDG Logo" />
                    @else
                        <div class="logo-image logo-placeholder">
                            <span style="font-size: 24px; color: #d4af37;">IDG</span>
                        </div>
                    @endif
                    <div class="logo-text">
                        <div class="logo-title">IDG Laboratory</div>
                        <div class="logo-subtitle">International Diamond & Gemstone</div>
                    </div>
                </div>
            </div>
            
            <!-- Quote Title -->
            <div class="quote-main-title">
                <div class="quote-title">عرض سعر</div>
                <div class="quote-subtitle">Quotation</div>
                <div class="quote-number">#{{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</div>
            </div>
        </div>
        
        <!-- Company Information -->
        <div class="company-info">
            <div class="company-details">
                <div class="company-name">فرع شركة مختبر اي دي جي لتحليل الألماس والأحجار الكريمة ش ذ م م</div>
                <div class="company-address">العليا, 6776, الرياض، حي الورود, 12215, السعودية</div>
                <div class="tax-number">الرقم الضريبي: 312404527400003</div>
            </div>
            
            <div class="customer-details">
                <div class="client-title">تفاصيل بيانات العميل</div>
                <div class="client-name">
                    @if($customer)
                        {{ $customer['display_name'] ?? $customer['name'] ?? 'غير محدد' }}
                    @else
                        غير محدد
                    @endif
                </div>
                @if($customer && isset($customer['email']) && !empty($customer['email']))
                <div style="margin-top: 10px; font-size: 13px; color: #666;">
                    البريد الإلكتروني: {{ $customer['email'] }}
                </div>
                @endif
                @if($customer && isset($customer['phone']) && !empty($customer['phone']))
                <div style="font-size: 13px; color: #666;">
                    الهاتف: {{ $customer['phone'] }}
                </div>
                @endif
            </div>
        </div>
        
        <!-- Quote Details Grid -->
        <div class="quote-details-grid">
            <div>
                <div class="detail-label">المرجع</div>
                <div class="detail-value quote-reference">{{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="detail-label">تاريخ الإصدار</div>
                <div class="detail-value quote-date-issue">{{ \Carbon\Carbon::parse($quote['issue_date'] ?? now())->format('Y-m-d') }}</div>
            </div>
            <div>
                <div class="detail-label">تاريخ الانتهاء</div>
                <div class="detail-value quote-date-expiry">{{ \Carbon\Carbon::parse($quote['expiry_date'] ?? now()->addDays(30))->format('d-m-Y') }}</div>
            </div>
        </div>
        
        <!-- Quote Items -->
        @if(isset($quote['line_items']) && count($quote['line_items']) > 0)
        <div class="items-section">
            <div class="section-title">بنود عرض السعر</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المنتج</th>
                        <th>الكمية</th>
                        <th>سعر الوحدة</th>
                        <th>الخصم</th>
                        <th>الإجمالي قبل الضريبة</th>
                        <th>% VAT</th>
                        <th>VAT</th>
                        <th>المجموع النهائي</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quote['line_items'] as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="product-column"> 
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
                            <div class="product-id">#{{ $product_id }}</div>
                        </td>
                        <td class="quantity-unit">{{ $item['quantity'] }} قطعة</td>
                        <td class="unit-price">{{ number_format($item['unit_price'], 2) }}</td>
                        <td class="discount">{{ $item['discount_percentage'] ?? 0 }}% {{ number_format($item['total_discount'] ?? 0, 2) }}</td>
                        <td class="total-before-vat">{{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0), 2) }}</td>
                        <td class="vat-percentage">{{ $item['vat_percentage'] ?? 15 }}.0</td>
                        <td class="vat-amount">{{ number_format(((($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0)) * ($item['vat_percentage'] ?? 15)) / 100, 2) }}</td>
                        <td class="total-amount">{{ number_format((($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0)) + (((($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0) - ($item['total_discount'] ?? 0)) * ($item['vat_percentage'] ?? 15)) / 100), 2) }}</td>
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
                    <div class="total-label">الإجمالي قبل الضريبة</div>
                    <div class="total-value">{{ number_format($quote['subtotal'] ?? $calculated_subtotal, 2) }}</div>
                </div>
                <div class="total-item">
                    <div class="total-label">إجمالي الضريبة</div>
                    <div class="total-value">{{ number_format($quote['tax_amount'] ?? $tax_amount, 2) }}</div>
                </div>
                <div class="total-item">
                    <div class="total-label">المجموع:</div>
                    <div class="total-value">{{ number_format($quote['total_amount'] ?? $total_amount, 2) }}</div>
                </div>
            </div>
        </div>
        
    </div>
</body>
</html>