<!DOCTYPE html>
<html lang="{{ $locale === 'ar' ? 'ar' : 'en' }}" dir="{{ $locale === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $subjectLine }}</title>
</head>
<body style="font-family: {{ $locale === 'ar' ? 'Tajawal, ' : '' }}'Segoe UI', Tahoma, sans-serif; line-height: 1.6; color: #111; max-width: 560px; margin: 0 auto; padding: 24px; text-align: {{ $locale === 'ar' ? 'right' : 'left' }};">
    <div style="border-bottom: 3px solid #166534; padding-bottom: 12px; margin-bottom: 20px;">
        <strong style="font-size: 18px; color: #166534;">IDG</strong>
    </div>

    <p style="font-size: 15px; margin: 0 0 20px;">{{ $body }}</p>

    @if(!empty($receivingRecordNo))
        <p style="font-size: 13px; color: #444; margin: 0 0 8px;">
            <strong>{{ $labelReceiving }}:</strong>
            <span style="font-family: monospace;">{{ $receivingRecordNo }}</span>
        </p>
    @endif

    @if(!empty($actorName))
        <p style="font-size: 13px; color: #666; margin: 0 0 16px;">
            {{ $labelBy }}: {{ $actorName }}
        </p>
    @endif

    <p style="font-size: 11px; color: #999; margin-top: 24px; border-top: 1px solid #eee; padding-top: 12px;">
        {{ $footer }}
    </p>
</body>
</html>
