<?php

namespace App\Http\Controllers;

use App\Mail\CustomerSurveyInvitationMail;
use App\Models\CustomerSurveyInvitation;
use App\Models\CustomerSurveyResponse;
use App\Models\TestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class CustomerSurveyController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureStaff();

        $status = $request->query('status', 'all');
        $complaint = $request->query('complaint', 'all');
        $search = trim((string) $request->query('search', ''));

        $query = CustomerSurveyInvitation::query()
            ->with([
                'response',
                'testRequest:id,receiving_record_no,qoyod_customer_id',
                'sender:id,name',
            ])
            ->orderByDesc('id');

        if ($status === 'completed') {
            $query->whereNotNull('completed_at');
        } elseif ($status === 'pending') {
            $query->whereNull('completed_at');
        }

        if ($complaint === 'yes') {
            $query->whereHas('response', fn ($q) => $q->where('has_complaint', true));
        } elseif ($complaint === 'no') {
            $query->whereHas('response', fn ($q) => $q->where('has_complaint', false));
        }

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                    ->orWhere('recipient_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhereHas('testRequest', fn ($t) => $t->where('receiving_record_no', 'like', "%{$search}%"))
                    ->orWhereHas('response', function ($r) use ($search) {
                        $r->where('customer_name', 'like', "%{$search}%")
                            ->orWhere('mobile', 'like', "%{$search}%")
                            ->orWhere('signer_name', 'like', "%{$search}%");
                    });
            });
        }

        $surveys = $query->paginate(15)->withQueryString()->through(fn ($invitation) => $this->present($invitation));

        $averages = CustomerSurveyResponse::query()
            ->selectRaw('ROUND(AVG(service_quality_rating), 1) as service_quality')
            ->selectRaw('ROUND(AVG(delivery_time_rating), 1) as delivery_time')
            ->selectRaw('ROUND(AVG(report_quality_rating), 1) as report_quality')
            ->first();

        $sent = CustomerSurveyInvitation::count();
        $completed = CustomerSurveyInvitation::whereNotNull('completed_at')->count();

        return Inertia::render('Dashboard/Surveys/Index', [
            'surveys' => $surveys,
            'filters' => [
                'status' => in_array($status, ['all', 'pending', 'completed'], true) ? $status : 'all',
                'complaint' => in_array($complaint, ['all', 'yes', 'no'], true) ? $complaint : 'all',
                'search' => $search,
            ],
            'stats' => [
                'sent' => $sent,
                'completed' => $completed,
                'pending' => $sent - $completed,
                'response_rate' => $sent > 0 ? (int) round(($completed / $sent) * 100) : 0,
                'complaints' => CustomerSurveyResponse::where('has_complaint', true)->count(),
                'service_quality' => $averages?->service_quality !== null ? (float) $averages->service_quality : null,
                'delivery_time' => $averages?->delivery_time !== null ? (float) $averages->delivery_time : null,
                'report_quality' => $averages?->report_quality !== null ? (float) $averages->report_quality : null,
            ],
        ]);
    }

    public function show(CustomerSurveyInvitation $survey)
    {
        $this->ensureStaff();

        $survey->load([
            'response',
            'testRequest:id,receiving_record_no,qoyod_customer_id,received_date,delivery_date',
            'sender:id,name',
        ]);

        return Inertia::render('Dashboard/Surveys/Show', [
            'survey' => $this->present($survey, true),
        ]);
    }

    public function send(Request $request, TestRequest $testRequest)
    {
        $this->ensureStaff();

        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $customerName = '';
        $customerPhone = null;
        try {
            $customer = (new \App\Services\QoyodService())->getCustomer($testRequest->qoyod_customer_id);
            if (is_array($customer)) {
                $customerName = (string) ($customer['name'] ?? $customer['display_name'] ?? '');
                $customerPhone = $customer['phone_number'] ?? $customer['phone'] ?? null;
            }
        } catch (\Throwable $e) {
            Log::warning('Survey send could not load Qoyod customer', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage(),
            ]);
        }

        $created = false;
        $invitation = CustomerSurveyInvitation::query()
            ->where('test_request_id', $testRequest->id)
            ->whereNull('completed_at')
            ->latest('id')
            ->first();

        if ($invitation) {
            $invitation->update([
                'customer_name' => $customerName !== '' ? $customerName : $invitation->customer_name,
                'customer_phone' => $customerPhone ?: $invitation->customer_phone,
                'recipient_email' => $validated['email'],
                'sent_by' => $request->user()?->id,
                'sent_at' => now(),
            ]);
        } else {
            $created = true;
            $invitation = CustomerSurveyInvitation::create([
                'token' => Str::random(48),
                'qoyod_customer_id' => $testRequest->qoyod_customer_id,
                'test_request_id' => $testRequest->id,
                'customer_name' => $customerName !== '' ? $customerName : null,
                'customer_phone' => $customerPhone,
                'recipient_email' => $validated['email'],
                'sent_by' => $request->user()?->id,
                'sent_at' => now(),
            ]);
        }

        try {
            Mail::to($validated['email'])->send(new CustomerSurveyInvitationMail(
                customerName: $customerName,
                surveyUrl: route('survey.show', ['token' => $invitation->token]),
                receivingRecordNo: $testRequest->receiving_record_no,
            ));
        } catch (\Throwable $e) {
            if ($created) {
                $invitation->delete();
            }
            Log::error('Failed to send customer survey email', [
                'test_request_id' => $testRequest->id,
                'email' => $validated['email'],
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'email' => 'تعذر إرسال البريد. تحقق من العنوان أو إعدادات البريد ثم أعد المحاولة.',
            ]);
        }

        return back()->with('success', 'تم إرسال الاستبيان إلى '.$validated['email']);
    }

    public function publicForm(string $token)
    {
        $invitation = CustomerSurveyInvitation::with('testRequest:id,receiving_record_no')
            ->where('token', $token)
            ->firstOrFail();

        return Inertia::render('Survey/Fill', [
            'invitation' => [
                'token' => $invitation->token,
                'customer_name' => $invitation->customer_name,
                'customer_phone' => $invitation->customer_phone,
                'receiving_record_no' => $invitation->testRequest?->receiving_record_no,
                'completed' => $invitation->isCompleted(),
            ],
        ]);
    }

    public function publicSubmit(Request $request, string $token)
    {
        $invitation = CustomerSurveyInvitation::where('token', $token)->firstOrFail();

        if ($invitation->isCompleted()) {
            return back()->with('info', 'already_submitted');
        }

        $language = $request->input('language');
        if (in_array($language, ['ar', 'en'], true)) {
            app()->setLocale($language);
        }

        $validated = $request->validate([
            'language' => ['required', 'in:ar,en'],
            'customer_name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'max:50'],
            'service_quality_rating' => ['required', 'integer', 'between:1,5'],
            'delivery_time_rating' => ['required', 'integer', 'between:1,5'],
            'report_quality_rating' => ['required', 'integer', 'between:1,5'],
            'signer_name' => ['required', 'string', 'max:255'],
            'has_complaint' => ['required', 'in:yes,no'],
            'complaint_details' => ['nullable', 'required_if:has_complaint,yes', 'string', 'max:5000'],
        ]);

        DB::transaction(function () use ($invitation, $validated) {
            $locked = CustomerSurveyInvitation::whereKey($invitation->id)->lockForUpdate()->first();
            if ($locked->isCompleted()) {
                return;
            }

            CustomerSurveyResponse::create([
                'invitation_id' => $locked->id,
                'language' => $validated['language'],
                'customer_name' => $validated['customer_name'],
                'mobile' => $validated['mobile'],
                'service_quality_rating' => $validated['service_quality_rating'],
                'delivery_time_rating' => $validated['delivery_time_rating'],
                'report_quality_rating' => $validated['report_quality_rating'],
                'signer_name' => $validated['signer_name'],
                'has_complaint' => $validated['has_complaint'] === 'yes',
                'complaint_details' => $validated['has_complaint'] === 'yes'
                    ? trim((string) ($validated['complaint_details'] ?? ''))
                    : null,
            ]);

            $locked->update(['completed_at' => now()]);
        });

        return redirect()->route('survey.show', ['token' => $token]);
    }

    private function ensureStaff(): void
    {
        $role = auth()->user()?->role;
        abort_unless(in_array($role, ['admin', 'receptionist'], true), 403);
    }

    private function present(CustomerSurveyInvitation $invitation, bool $detailed = false): array
    {
        $response = $invitation->response;
        $payload = [
            'id' => $invitation->id,
            'qoyod_customer_id' => $invitation->qoyod_customer_id,
            'customer_name' => $response?->customer_name ?: $invitation->customer_name,
            'customer_phone' => $invitation->customer_phone,
            'recipient_email' => $invitation->recipient_email,
            'sent_at' => optional($invitation->sent_at)->toDateTimeString(),
            'completed_at' => optional($invitation->completed_at)->toDateTimeString(),
            'status' => $invitation->isCompleted() ? 'completed' : 'pending',
            'sent_by' => $invitation->sender?->name,
            'receiving_record_no' => $invitation->testRequest?->receiving_record_no,
            'test_request_id' => $invitation->test_request_id,
            'has_complaint' => $response?->has_complaint,
            'service_quality_rating' => $response?->service_quality_rating,
            'delivery_time_rating' => $response?->delivery_time_rating,
            'report_quality_rating' => $response?->report_quality_rating,
            'language' => $response?->language,
        ];

        if ($detailed) {
            $payload['mobile'] = $response?->mobile;
            $payload['signer_name'] = $response?->signer_name;
            $payload['complaint_details'] = $response?->complaint_details;
            $payload['received_date'] = optional($invitation->testRequest?->received_date)->toDateString();
            $payload['delivery_date'] = optional($invitation->testRequest?->delivery_date)->toDateString();
        }

        return $payload;
    }
}
