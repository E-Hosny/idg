<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Print Quote') }} {{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}</title>
    <style>
        @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .page-break { page-break-before: always; }
            @page {
                size: A4 landscape;
                margin: 10mm;
            }
        }
        
        @media screen {
            body {
                background: #f0f0f0;
                padding: 20px;
                font-family: Arial, sans-serif;
                margin: 0;
                overflow-x: hidden;
            }
            .print-container {
                background: white;
                box-shadow: 0 10px 20px rgba(0,0,0,0.1);
                border-radius: 8px;
                overflow: visible;
                max-height: 100vh;
                overflow-y: auto;
            }
            .print-actions {
                background: #2d5a27;
                padding: 15px 20px;
                color: white;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .print-title {
                font-size: 18px;
                font-weight: bold;
            }
            .print-buttons {
                display: flex;
                gap: 10px;
            }
            .btn {
                padding: 8px 16px;
                border: none;
                border-radius: 4px;
                cursor: pointer;
                font-size: 14px;
                text-decoration: none;
                display: inline-flex;
                align-items: center;
                gap: 5px;
            }
            .btn-primary {
                background: #007bff;
                color: white;
            }
            .btn-success {
                background: #28a745;
                color: white;
            }
            .btn:hover {
                opacity: 0.9;
            }
            @media (max-width: 768px) {
                body { padding: 10px; }
                .print-buttons {
                    flex-direction: column;
                    gap: 5px;
                }
                .btn {
                    padding: 6px 12px;
                    font-size: 12px;
                }
            }
        }
    </style>
</head>
<body>
    <!-- Print Actions Header -->
    <div class="print-container">
        <div class="print-actions no-print">
            <div class="print-title">
                {{ __('Print Quote') }} #{{ $quote['reference'] ?? $quote['id'] ?? 'N/A' }}
            </div>
            <div class="print-buttons">
                <button onclick="window.print()" class="btn btn-primary">
                    <i class="fas fa-print"></i>
                    {{ __('Print') }}
                </button>
                <a href="{{ url()->previous() }}" class="btn btn-success">
                    <i class="fas fa-arrow-left"></i>
                    {{ __('Back') }}
                </a>
            </div>
        </div>
        
        <!-- Include the professional PDF content -->
        @include('quotes.professional-pdf')
    </div>

    <!-- Add FontAwesome if not already loaded -->
    <script>
        if (!document.querySelector('link[href*="fontawesome"]')) {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
            document.head.appendChild(link);
        }
    </script>
</body>
</html>
