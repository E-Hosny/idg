<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quote {{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            font-size: 12px;
        }
        
        .container {
            max-width: 80%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        
        .company-logo {
            width: 100px;
            height: auto;
            margin-bottom: 10px;
        }
        
        .quote-title {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .quote-number {
            font-size: 16px;
            color: #666;
        }
        
        .quote-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .quote-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .quote-dates {
            display: table-cell;
            width: 50%;
            text-align: left;
        }
        
        .info-item {
            margin-bottom: 10px;
        }
        
        .label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }
        
        .customer-section {
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin: 20px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 1px solid #ddd;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        
        .items-table th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        
        .items-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        
        .total-section {
            margin-top: 30px;
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
        }
        
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 10px;
        }
        
        .total-row .label {
            display: table-cell;
            width: 70%;
            text-align: right;
            padding-right: 20px;
        }
        
        .total-row .amount {
            display: table-cell;
            width: 30%;
            text-align: left;
            font-weight: bold;
            color: #007bff;
        }
        
        .grand-total {
            font-size: 16px;
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
        }
        
        .grand-total .label {
            color: white;
        }
        
        .grand-total .amount {
            color: white;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            border-top: 1px solid #ddd;
            padding-top: 20px;
            font-size: 10px;
            color: #666;
        }
        
        .notes-section {
            margin-top: 30px;
            padding: 15px;
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            border-radius: 5px;
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 10px;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="quote-title">عرض سعر</div>
            <div class="quote-number">{{ __('Quote') }} #{{ $quote['reference'] ?? $quote['id'] }}</div>
        </div>
        
        <!-- Quote Details -->
        <div class="quote-details">
            <div class="quote-info">
                <div class="section-title">{{ __('Quote Details') }}</div>
                <div class="info-item">
                    <span class="label">{{ __('Quote Number') }}:</span>
                    {{ $quote['reference'] ?? $quote['id'] }}
                </div>
                <div class="info-item">
                    <span class="label">{{ __('Status') }}:</span>
                    {{ $quote['status'] ?? 'Draft' }}
                </div>
                <div class="info-item">
                    <span class="label">{{ __('Issue Date') }}:</span>
                    {{ \Carbon\Carbon::parse($quote['issue_date'])->format('Y/m/d') }}
                </div>
                <div class="info-item">
                    <span class="label">{{ __('Expiry Date') }}:</span>
                    {{ \Carbon\Carbon::parse($quote['expiry_date'])->format('Y/m/d') }}
                </div>
            </div>
        </div>
        
        <!-- Customer Information -->
        @if($customer)
        <div class="customer-section">
            <div class="section-title">{{ __('Customer Information') }}</div>
            <div class="info-item">
                <span class="label">{{ __('Customer Name') }}:</span>
                {{ $customer['display_name'] ?? $customer['name'] ?? 'N/A' }}
            </div>
            @if($customer['email'])
            <div class="info-item">
                <span class="label">{{ __('Email') }}:</span>
                {{ $customer['email'] }}
            </div>
            @endif
            @if($customer['phone'])
            <div class="info-item">
                <span class="label">{{ __('Phone') }}:</span>
                {{ $customer['phone'] }}
            </div>
            @endif
        </div>
        @endif
        
        <!-- Quote Items -->
        <div class="section-title">{{ __('Quote Items') }}</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>{{ __('Item') }}</th>
                    <th>{{ __('Description') }}</th>
                    <th>{{ __('Quantity') }}</th>
                    <th>{{ __('Unit Price') }}</th>
                    <th>{{ __('Subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote['line_items'] ?? [] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['product_name'] ?? __('Product') }} #{{ $item['product_id'] ?? '' }}</td>
                    <td>{{ $item['quantity'] }}</td>
                    <td>SAR {{ number_format($item['unit_price'], 2) }}</td>
                    <td>SAR {{ number_format($item['quantity'] * $item['unit_price'], 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Total Section -->
        <div class="total-section">
            @php
                $subtotal = array_sum(array_map(function($item) {
                    return $item['quantity'] * $item['unit_price'];
                }, $quote['line_items'] ?? []));
                $tax = $subtotal * 0.15; // 15% VAT
                $total = $subtotal + $tax;
            @endphp
            
            <div class="total-row">
                <span class="label">{{ __('Subtotal') }}:</span>
                <span class="amount">SAR {{ number_format($subtotal, 2) }}</span>
            </div>
            @if($tax > 0)
            <div class="total-row">
                <span class="label">{{ __('Tax') }} (15%):</span>
                <span class="amount">SAR {{ number_format($tax, 2) }}</span>
            </div>
            @endif
            <div class="total-row">
                <span class="label">{{ __('Total Amount') }}:</span>
                <span class="amount">SAR {{ number_format($quote['total_amount'] ?? $total, 2) }}</span>
            </div>
        </div>
        
        <!-- Notes -->
        @if(isset($quote['notes']) && !empty($quote['notes']))
        <div class="notes-section">
            <div class="notes-title">{{ __('Notes') }}:</div>
            <div>{{ $quote['notes'] }}</div>
        </div>
        @endif
        
        @if(isset($quote['terms_conditions']) && !empty($quote['terms_conditions']))
        <div class="notes-section">
            <div class="notes-title">{{ __('Terms & Conditions') }}:</div>
            <div>{{ $quote['terms_conditions'] }}</div>
        </div>
        @endif
        
        <!-- Footer -->
        <div class="footer">
            <p>{{ __('This quote is valid until') }} {{ \Carbon\Carbon::parse($quote['expiry_date'])->format('Y/m/d') }}</p>
            <p>{{ __('Generated on') }} {{ now()->format('Y/m/d H:i') }}</p>
        </div>
    </div>
</body>
</html>
