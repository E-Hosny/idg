<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Quote {{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
        }
        .quote-title {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .quote-number {
            font-size: 16px;
            color: #666;
        }
        .section {
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-row {
            margin-bottom: 8px;
            display: table;
            width: 100%;
        }
        .label {
            display: table-cell;
            width: 30%;
            font-weight: bold;
        }
        .value {
            display: table-cell;
            width: 70%;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
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
        .total-section {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-top: 20px;
        }
        .total-row {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }
        .total-label {
            display: table-cell;
            width: 70%;
            text-align: right;
            padding-right: 20px;
        }
        .total-amount {
            display: table-cell;
            width: 30%;
            font-weight: bold;
            color: #007bff;
        }
        .grand-total {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border-radius: 5px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="quote-title">Quote</div>
        <div class="quote-number">#{{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</div>
    </div>
    
    <!-- Quote Details -->
    <div class="section">
        <div class="section-title">Quote Details</div>
        <div class="info-row">
            <span class="label">Quote Number:</span>
            <span class="value">{{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Status:</span>
            <span class="value">{{ $quote['status'] ?? 'Draft' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Issue Date:</span>
            <span class="value">{{ $quote['issue_date'] ?? 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="label">Expiry Date:</span>
            <span class="value">{{ $quote['expiry_date'] ?? 'N/A' }}</span>
        </div>
    </div>
    
    <!-- Customer Information -->
    @if($customer)
    <div class="section">
        <div class="section-title">Customer Information</div>
        <div class="info-row">
            <span class="label">Customer Name:</span>
            <span class="value">{{ $customer['display_name'] ?? $customer['name'] ?? 'N/A' }}</span>
        </div>
        @if($customer['email'])
        <div class="info-row">
            <span class="label">Email:</span>
            <span class="value">{{ $customer['email'] }}</span>
        </div>
        @endif
        @if($customer['phone'])
        <div class="info-row">
            <span class="label">Phone:</span>
            <span class="value">{{ $customer['phone'] }}</span>
        </div>
        @endif
    </div>
    @endif
    
    <!-- Quote Items -->
    @if(isset($quote['line_items']) && count($quote['line_items']) > 0)
    <div class="section">
        <div class="section-title">Quote Items</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote['line_items'] as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['product_name'] ?? 'Product #' . ($item['product_id'] ?? '') }}</td>
                    <td>{{ $item['quantity'] ?? 0 }}</td>
                    <td>SAR {{ number_format($item['unit_price'] ?? 0, 2) }}</td>
                    <td>SAR {{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <div class="section">
        <div class="section-title">No Items Found</div>
        <p>This quote has no line items.</p>
    </div>
    @endif
    
    <!-- Total Section -->
    <div class="total-section">
        @php
            $subtotal = 0;
            if(isset($quote['line_items'])) {
                foreach($quote['line_items'] as $item) {
                    $subtotal += ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
                }
            }
            $tax = $subtotal * 0.15; // 15% VAT
            $total = $quote['total_amount'] ?? ($subtotal + $tax);
        @endphp
        
        <div class="total-row">
            <span class="total-label">Subtotal:</span>
            <span class="total-amount">SAR {{ number_format($subtotal, 2) }}</span>
        </div>
        <div class="total-row">
            <span class="total-label">Tax (15%):</span>
            <span class="total-amount">SAR {{ number_format($tax, 2) }}</span>
        </div>
        <div class="total-row grand-total">
            <span class="total-label">Total Amount:</span>
            <span class="total-amount">SAR {{ number_format($total, 2) }}</span>
        </div>
    </div>
    
    <!-- Notes -->
    @if(isset($quote['notes']) && !empty($quote['notes']))
    <div class="section">
        <div class="section-title">Notes</div>
        <p>{{ $quote['notes'] }}</p>
    </div>
    @endif
    
    @if(isset($quote['terms_conditions']) && !empty($quote['terms_conditions']))
    <div class="section">
        <div class="section-title">Terms & Conditions</div>
        <p>{{ $quote['terms_conditions'] }}</p>
    </div>
    @endif
    
    <!-- Footer -->
    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
        <p>This quote is valid until {{ $quote['expiry_date'] ?? 'N/A' }}</p>
        <p>Generated on {{ date('Y/m/d H:i') }}</p>
    </div>
</body>
</html>
