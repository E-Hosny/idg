<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: white;
            color: #333;
            line-height: 1.6;
        }
        
        .certificate {
            max-width: 210mm;
            margin: 0 auto;
            background: white;
            position: relative;
        }
        
        .header {
            background-color: #047857;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo-section {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .logo {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2px;
        }
        
        .logo img {
            width: 46px;
            height: 46px;
            border-radius: 50%;
        }
        
        .lab-info h1 {
            font-size: 20px;
            color: white;
            font-weight: bold;
            margin-bottom: 2px;
        }
        
        .lab-info h2 {
            font-size: 16px;
            color: white;
            font-weight: 600;
        }
        
        .dates {
            text-align: right;
            font-size: 12px;
            color: #6b7280;
            background: #f9fafb;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
        
        .dates div {
            margin-bottom: 5px;
        }
        
        .dates strong {
            color: #374151;
        }
        
        .report-section {
            display: flex;
            justify-content: space-between;
            margin-bottom: 40px;
            gap: 40px;
        }
        
        .report-info {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            padding: 20px;
            border-radius: 12px;
            border-left: 4px solid #059669;
            flex: 1;
        }
        
        .report-info div {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        
        .report-info .label {
            font-weight: 600;
            color: #374151;
        }
        
        .report-info .value {
            font-weight: bold;
            color: #059669;
        }
        
        .identification {
            text-align: center;
            position: relative;
        }
        
        .id-box {
            background: linear-gradient(135deg, #059669, #10b981);
            padding: 30px 20px;
            border-radius: 15px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            transform: rotate(1deg);
        }
        
        .id-inner {
            background: white;
            padding: 20px;
            border-radius: 10px;
            transform: rotate(-1deg);
        }
        
        .id-label {
            font-size: 10px;
            color: #059669;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 10px;
        }
        
        .id-value {
            font-size: 28px;
            font-weight: bold;
            color: #1f2937;
        }
        
        .properties {
            background: #fefefe;
            border-radius: 15px;
            border: 1px solid #e5e7eb;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        
        .properties h3 {
            text-align: center;
            font-size: 20px;
            color: #1f2937;
            margin-bottom: 20px;
            position: relative;
        }
        
        .properties h3:after {
            content: '';
            width: 60px;
            height: 2px;
            background: linear-gradient(90deg, #059669, #10b981);
            position: absolute;
            bottom: -5px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .properties-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }
        
        .property {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 15px;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s;
        }
        
        .property:hover {
            background: #f0fdf4;
            border-radius: 8px;
        }
        
        .property.conclusion {
            background: linear-gradient(135deg, #ecfdf5, #d1fae5);
            border-left: 4px solid #059669;
            border-radius: 8px;
            font-weight: bold;
        }
        
        .property-label {
            font-weight: 600;
            color: #374151;
        }
        
        .property-value {
            font-weight: 500;
            color: #1f2937;
        }
        
        .property-value.weight {
            font-weight: bold;
            color: #059669;
            font-size: 16px;
        }
        
        .property-value.conclusion {
            font-weight: bold;
            color: #059669;
            font-size: 16px;
        }
        
        .comments {
            background: linear-gradient(135deg, #fffbeb, #fef3c7);
            border-left: 4px solid #f59e0b;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .comments h4 {
            color: #1f2937;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .comments p {
            color: #374151;
            font-style: italic;
        }
        
        .footer {
            border-top: 2px solid #059669;
            padding-top: 30px;
            margin-top: 40px;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: 1fr 2fr 1fr;
            gap: 30px;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .qr-code {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border: 2px solid #d1d5db;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            color: #6b7280;
            font-weight: bold;
        }
        
        .signature {
            text-align: center;
        }
        
        .signature-line {
            width: 120px;
            height: 60px;
            border-bottom: 2px solid #6b7280;
            margin: 0 auto 10px;
            position: relative;
        }
        
        .signature-line:after {
            content: '';
            width: 8px;
            height: 8px;
            background: #059669;
            border-radius: 50%;
            position: absolute;
            bottom: -4px;
            left: 50%;
            transform: translateX(-50%);
        }
        
        .signature-text {
            font-size: 12px;
            font-weight: 600;
            color: #374151;
        }
        
        .signature-subtitle {
            font-size: 10px;
            color: #6b7280;
            margin-top: 5px;
        }
        

        
        .contact-info {
            background: linear-gradient(135deg, #f9fafb, #f3f4f6);
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            font-size: 10px;
            color: #6b7280;
        }
        
        .contact-section h5 {
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 8px;
        }
        
        .contact-section div {
            margin-bottom: 3px;
        }
        
        .website {
            color: #059669;
            font-weight: 600;
        }
        
        .disclaimer {
            font-size: 9px;
            color: #9ca3af;
            margin-top: 8px;
        }
    </style>
</head>
<body>
    <div class="certificate">
        <!-- Header -->
        <div class="header">
            <div class="logo-section">
                <div class="logo">
                    <img src="{{ public_path('images/idg_logo.jpg') }}" alt="IDG Logo">
                </div>
                <div class="lab-info">
                    <h1>IDG Laboratory</h1>
                    <h2>Gemstone Report</h2>
                </div>
            </div>
            
            <div class="dates">
                <div><strong>Received Date:</strong> {{ $certificate->received_date->format('d/m/Y') }}</div>
                <div><strong>Report Date:</strong> {{ $certificate->report_date->format('d/m/Y') }}</div>
                <div><strong>Test Date:</strong> {{ $certificate->test_date->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Report Details -->
        <div class="report-section">
            <div class="report-info">
                <div>
                    <span class="label">Report No.:</span>
                    <span class="value">{{ $certificate->certificate_number }}</span>
                </div>
                <div>
                    <span class="label">Test Method:</span>
                    <span class="value">{{ $certificate->test_method ?? 'SOPOI' }}</span>
                </div>
            </div>
            
            <div class="identification">
                <div class="id-box">
                    <div class="id-inner">
                        <div class="id-label">IDENTIFICATION</div>
                        <div class="id-value">{{ $certificate->identification }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gemstone Properties -->
        <div class="properties">
            <h3>Gemstone Properties</h3>
            <div class="properties-grid">
                <!-- Left Column -->
                <div>
                    <div class="property">
                        <span class="property-label">Shape/Cut:</span>
                        <span class="property-value">{{ $certificate->shape_cut ?? '-' }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Weight:</span>
                        <span class="property-value weight">{{ $certificate->weight ? $certificate->weight . ' Ct' : '-' }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Color:</span>
                        <span class="property-value">{{ $certificate->color ?? '-' }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Dimensions:</span>
                        <span class="property-value">{{ $certificate->dimensions ?? '0.00 x 0.00 x 0.00 mm' }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Transparency:</span>
                        <span class="property-value">{{ $certificate->transparency ?? 'Transparent' }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Phenomena:</span>
                        <span class="property-value">{{ $certificate->phenomena ?? '-' }}</span>
                    </div>
                </div>

                <!-- Right Column -->
                <div>
                    <div class="property">
                        <span class="property-label">Species:</span>
                        <span class="property-value">{{ $certificate->species ?? '-' }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Variety:</span>
                        <span class="property-value">{{ $certificate->variety ?? '-' }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Group:</span>
                        <span class="property-value">{{ $certificate->group ?? '-' }}</span>
                    </div>
                    <div class="property">
                        <span class="property-label">Origin Opinion:</span>
                        <span class="property-value">{{ $certificate->origin_opinion ?? 'Not requested' }}</span>
                    </div>
                    <div class="property conclusion">
                        <span class="property-label">Conclusion:</span>
                        <span class="property-value conclusion">{{ $certificate->conclusion }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments -->
        @if($certificate->comments)
        <div class="comments">
            <h4>Comments</h4>
            <p>{{ $certificate->comments }}</p>
        </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-grid">
                <!-- QR Code -->
                <div class="qr-code">
                    @if($certificate->qr_code_path && file_exists(public_path('storage/' . $certificate->qr_code_path)))
                        <img src="{{ public_path('storage/' . $certificate->qr_code_path) }}" alt="QR Code" style="width: 50px; height: 50px;">
                    @else
                        QR CODE
                    @endif
                </div>

                <!-- Signature -->
                <div class="signature">
                    <div class="signature-line"></div>
                    <div class="signature-text">Signature</div>
                    <div class="signature-subtitle">Authorized Gemologist</div>
                </div>

                <!-- Empty space for balance -->
                <div style="width: 80px;"></div>
            </div>

            <!-- Contact Information -->
            <div class="contact-info">
                <div class="contact-section">
                    <h5>Contact Information</h5>
                    <div>Address: IDG Laboratory, Saudi Arabia</div>
                    <div>Phone: +966 XX XXX XXXX</div>
                    <div>Email: info@idg-lab.com.sa</div>
                </div>
                <div class="contact-section" style="text-align: right;">
                    <h5>Certification Details</h5>
                    <div>Visit website for Terms & Conditions</div>
                    <div class="website">www.idg-lab.com.sa</div>
                    <div class="disclaimer">This certificate is valid only for the described item</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Break for Back Cover -->
    <div style="page-break-before: always;"></div>

    <!-- Back Cover -->
    <div class="certificate-back">
        <!-- Simple border -->
        <div style="border: 2px solid #ffd700; margin: 20px; padding: 20px; border-radius: 10px;">
        
        <div class="back-container">
            <!-- Logo Section -->
            <div class="back-logo-circle">
                <img src="{{ public_path('images/idg_logo.jpg') }}" alt="IDG" class="back-logo-img" />
            </div>
            
            <!-- Lab Name -->
            <h1 class="lab-name">IDG Laboratory</h1>
            
            <!-- Report Type -->
            <div class="report-type">
                @php
                    $reportType = match($certificate->identification) {
                        'DIAMOND' => 'Diamond Report',
                        'SAPPHIRE', 'RUBY', 'EMERALD' => 'Gemstone Report', 
                        'JEWELLERY', 'JEWELRY' => 'Jewelry Report',
                        default => 'Gemstone Report'
                    };
                @endphp
                {{ $reportType }}
            </div>
        </div>
        
        <!-- Footer -->
        <div class="back-footer">
            <div class="website-info">www.idg-lab.com.sa</div>
            <div class="location">Riyadh, Saudi Arabia</div>
        </div>
        </div>
        </div>
    </div>

    <style>
        /* Back Cover Styles - Simplified for PDF */
        .certificate-back {
            background-color: #047857;
            color: white;
            text-align: center;
            padding: 100px 50px;
            page-break-before: always;
            min-height: 500px;
        }
        

        
        .back-container {
            text-align: center;
        }
        
        .back-logo-circle {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            border: 3px solid #ffd700;
            margin: 0 auto 30px;
            padding: 10px;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .back-logo-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        
        .lab-name {
            font-size: 36pt;
            font-weight: bold;
            color: #ffd700;
            margin: 20px 0;
            letter-spacing: 1px;
        }
        
        .report-type {
            font-size: 24pt;
            font-weight: 300;
            color: white;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 40px;
        }
        
        .back-footer {
            margin-top: 80px;
            text-align: center;
        }
        
        .website-info {
            font-size: 14pt;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 8px;
        }
        
        .location {
            font-size: 12pt;
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Remove animations for PDF */
        .certificate-back::before,
        .certificate-back::after {
            display: none;
        }
    </style>
</body>
</html> 