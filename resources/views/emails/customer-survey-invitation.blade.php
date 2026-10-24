<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>IDG — Customer Feedback</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:'Segoe UI',Tahoma,sans-serif;color:#111827;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6;padding:24px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellspacing="0" cellpadding="0" style="max-width:560px;background:#ffffff;border:1px solid #d1d5db;border-radius:12px;overflow:hidden;">
                    <tr>
                        <td style="background:#166534;color:#ffffff;padding:18px 24px;text-align:center;">
                            <div style="font-size:20px;font-weight:700;">IDG Laboratory</div>
                            <div style="font-size:14px;margin-top:4px;">استبيان آراء العملاء · Customer Feedback</div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;text-align:right;direction:rtl;">
                            <p style="margin:0 0 12px;font-size:16px;">مرحباً {{ $customerName !== '' ? $customerName : 'عميلنا الكريم' }}،</p>
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.7;">
                                شكراً لثقتكم بمختبر IDG. نرجو تخصيص أقل من دقيقتين لتقييم تجربتكم. رأيكم يساعدنا على تحسين الخدمة.
                            </p>
                            @if(!empty($receivingRecordNo))
                                <p style="margin:0 0 16px;font-size:13px;color:#374151;">
                                    رقم سجل الاستلام: <strong style="font-family:monospace;">{{ $receivingRecordNo }}</strong>
                                </p>
                            @endif
                            <p style="text-align:center;margin:20px 0;">
                                <a href="{{ $surveyUrl }}" style="display:inline-block;background:#166534;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;">
                                    تعبئة الاستبيان
                                </a>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 24px 24px;text-align:left;direction:ltr;border-top:1px solid #e5e7eb;">
                            <p style="margin:16px 0 12px;font-size:15px;line-height:1.7;">
                                Hello {{ $customerName !== '' ? $customerName : 'there' }},
                            </p>
                            <p style="margin:0 0 12px;font-size:15px;line-height:1.7;">
                                Thank you for choosing IDG Laboratory. This short survey takes less than two minutes and helps us improve our service.
                            </p>
                            <p style="text-align:center;margin:20px 0;">
                                <a href="{{ $surveyUrl }}" style="display:inline-block;background:#166534;color:#ffffff;text-decoration:none;padding:12px 28px;border-radius:8px;font-weight:700;">
                                    Open the survey
                                </a>
                            </p>
                            <p style="margin:0;font-size:12px;color:#6b7280;word-break:break-all;">{{ $surveyUrl }}</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
