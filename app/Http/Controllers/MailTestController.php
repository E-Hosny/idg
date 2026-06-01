<?php

namespace App\Http\Controllers;

use App\Mail\TestMailgunMail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class MailTestController extends Controller
{
    public function show(): Response
    {
        $domain = (string) config('services.mailgun.domain');
        $isSandbox = str_contains($domain, 'sandbox');

        return Inertia::render('Dashboard/MailTest', [
            'mail' => [
                'mailer' => config('mail.default'),
                'from_address' => config('mail.from.address'),
                'from_name' => config('mail.from.name'),
                'domain' => $domain,
                'is_sandbox' => $isSandbox,
            ],
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => 'أدخل البريد الإلكتروني | Email is required.',
            'email.email' => 'بريد إلكتروني غير صالح | Invalid email address.',
        ]);

        try {
            Mail::to($validated['email'])->send(
                new TestMailgunMail(now()->toDateTimeString())
            );
        } catch (\Throwable $e) {
            $hint = $this->failureHint($e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['email' => 'فشل الإرسال: '.$e->getMessage().$hint.' | Send failed.']);
        }

        $message = 'تم إرسال رسالة الاختبار إلى '.$validated['email']
            .' | Test email sent to '.$validated['email'];

        if (str_contains((string) config('services.mailgun.domain'), 'sandbox')) {
            $message .= ' — تذكير: نطاق Sandbox يسمح فقط بالمستلمين المصرّح بهم في Mailgun.';
        }

        return back()->with('success', $message);
    }

    private function failureHint(string $message): string
    {
        if (! str_contains($message, 'Forbidden') && ! str_contains($message, '401')) {
            return '';
        }

        return ' — تحقق من: (1) MAILGUN_DOMAIN=mail.idg-lab.com.sa في .env (2) مفتاح API بصلاحية إرسال من النطاق الحقيقي وليس Sandbox فقط (أنشئ Private API key جديد من Mailgun → Settings → API keys).';
    }
}
