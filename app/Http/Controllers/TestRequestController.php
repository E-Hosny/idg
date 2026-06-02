<?php

namespace App\Http\Controllers;

use App\Models\TestRequest;
use App\Models\TestRequestRedelivery;
use App\Models\Artifact;
use App\Services\QoyodService;
use App\Services\FileService;
use App\Services\WorkflowNotificationService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;

class TestRequestController extends Controller
{
    /**
     * List all test requests for a customer
     */
    public function listAllRequests($customerId)
    {
        try {
            \Log::info('Listing all test requests for customer', ['customer_id' => $customerId]);
            
            // Get customer info from Qoyod
            $qoyodService = new QoyodService();
            $customer = $qoyodService->getCustomer($customerId);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get all test requests for this customer
            $testRequests = TestRequest::where('qoyod_customer_id', $customerId)
                ->with(['redeliveries' => fn ($q) => $q->orderByDesc('id')])
                ->withCount([
                    'artifacts as pending_pieces_count' => function ($q) {
                        $q->whereIn('status', ['pending', 'under_evaluation']);
                    },
                    'artifacts as evaluated_pieces_count' => function ($q) {
                        $q->whereIn('status', ['evaluated', 'certified']);
                    },
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Format customer data
            $formattedCustomer = [
                'id' => $customer['id'] ?? $customerId,
                'name' => $customer['name'] ?? $customer['display_name'] ?? '',
                'display_name' => $customer['name'] ?? $customer['display_name'] ?? '',
                'organization' => $customer['organization'] ?? null,
                'phone_number' => $customer['phone_number'] ?? $customer['phone'] ?? null,
                'email' => $customer['email'] ?? $customer['email_address'] ?? null,
            ];

            return Inertia::render('Dashboard/Customers/TestRequestsList', [
                'customer' => $formattedCustomer,
                'testRequests' => $testRequests,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error listing test requests', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => 'An error occurred while listing test requests.']);
        }
    }

    /**
     * Create a new test request for a customer
     */
    public function createNew($customerId)
    {
        try {
            \Log::info('Creating new test request for customer', ['customer_id' => $customerId]);
            
            // Get customer info from Qoyod
            $qoyodService = new QoyodService();
            $customer = $qoyodService->getCustomer($customerId);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Create new test request
            $testRequest = TestRequest::create([
                'qoyod_customer_id' => $customerId,
                'receiving_record_no' => TestRequest::generateReceivingRecordNo(),
                'received_date' => now()->toDateString(),
                'received_in' => 'الرياض',
                'received_by' => auth()->user() ? auth()->user()->name : null,
                'status' => 'pending'
            ]);
            
            \Log::info('Created new test request', ['test_request' => $testRequest]);

            app(WorkflowNotificationService::class)->notifyLab(
                'test_request_created',
                $testRequest,
                auth()->user()
            );

            // Redirect to the new test request
            return redirect()->route('dashboard.test-requests.show', $testRequest->id)
                ->with('success', 'تم إنشاء طلب اختبار جديد بنجاح! | New test request created successfully!');

        } catch (\Exception $e) {
            \Log::error('Error creating new test request', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while creating test request.']);
        }
    }

    /**
     * Display a specific test request
     */
    public function show(TestRequest $testRequest)
    {
        try {
            \Log::info('Showing test request', ['test_request_id' => $testRequest->id]);
            
            // Get customer info from Qoyod
            $qoyodService = new QoyodService();
            $customer = $qoyodService->getCustomer($testRequest->qoyod_customer_id);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $testRequest->qoyod_customer_id]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get artifacts for this test request
            // If no artifacts linked to this specific request, get all customer artifacts
            $artifacts = Artifact::where('test_request_id', $testRequest->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // If no artifacts found for this request, get unlinked customer artifacts
            if ($artifacts->isEmpty()) {
                $artifacts = Artifact::where('qoyod_customer_id', $testRequest->qoyod_customer_id)
                    ->whereNull('test_request_id')
                    ->orderBy('created_at', 'desc')
                    ->get();
                
                // Auto-link these artifacts to this test request
                foreach ($artifacts as $artifact) {
                    $artifact->update(['test_request_id' => $testRequest->id]);
                }
            }

            \Log::info('Artifacts found', ['count' => $artifacts->count()]);

            // Format customer data
            $formattedCustomer = [
                'id' => $customer['id'] ?? $testRequest->qoyod_customer_id,
                'full_name' => $customer['name'] ?? $customer['display_name'] ?? '',
                'company_name' => $customer['organization'] ?? null,
                'customer_code' => 'CUS' . str_pad($testRequest->qoyod_customer_id, 3, '0', STR_PAD_LEFT),
                'phone' => $customer['phone_number'] ?? $customer['phone'] ?? null,
                'email' => $customer['email'] ?? $customer['email_address'] ?? null,
                'address' => 'الرياض',
                'qoyod_customer_id' => $testRequest->qoyod_customer_id,
                'status' => $customer['status'] ?? 'active'
            ];

            return Inertia::render('Dashboard/Customers/TestRequest', [
                'testRequest' => $testRequest,
                'customer' => $formattedCustomer,
                'artifacts' => $artifacts,
                'received_by' => $testRequest->received_by,
                'receiving_record_no' => $testRequest->receiving_record_no,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing test request', [
                'test_request_id' => $testRequest->id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => 'An error occurred while showing test request.']);
        }
    }

    /**
     * Legacy method for backward compatibility - redirects to create new
     */
    public function legacyShow($customerId)
    {
        // Check if customer has any test requests
        $testRequest = TestRequest::where('qoyod_customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->first();

        if ($testRequest) {
            // Redirect to the most recent test request
            return redirect()->route('dashboard.test-requests.show', $testRequest->id);
        }

        // No test requests exist, create a new one
        return redirect()->route('dashboard.customers.test-requests.create', $customerId);
    }

    /**
     * Update the test request
     */
    public function update(Request $request, TestRequest $testRequest)
    {
        try {
            $validated = $request->validate([
                'received_in' => 'nullable|string|max:255',
                'delivery_date' => 'nullable|date',
                'status' => 'required|in:pending,under_evaluation,evaluated,certified,delivered',
                'notes' => 'nullable|string'
            ]);

            $testRequest->update($validated);

            \Log::info('Test request updated', [
                'test_request_id' => $testRequest->id,
                'updated_data' => $validated
            ]);

            return redirect()->back()->with('success', 'Test request updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating test request', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to update test request.']);
        }
    }

    /**
     * Store a new artifact for the test request
     */
    public function storeArtifact(Request $request, TestRequest $testRequest)
    {
        try {
            $validated = $request->validate([
                'type' => 'required|string|max:255',
                'subtype' => 'nullable|string|max:255',
                'service' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'weight_unit' => 'nullable|in:ct,gm',
                'unit_type' => 'nullable|in:carat,gram',
                'price' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'delivery_type' => 'nullable|string|max:255'
            ]);

            // Convert weight_unit to unit_type if provided
            if (isset($validated['weight_unit'])) {
                $validated['unit_type'] = $validated['weight_unit'] === 'ct' ? 'carat' : 'gram';
                unset($validated['weight_unit']);
            }

            // Calculate expected date based on delivery type
            if (isset($validated['delivery_type'])) {
                $validated['expected_date'] = $this->calculateExpectedDate($validated['delivery_type']);
            }

            // Add required fields
            $validated['qoyod_customer_id'] = $testRequest->qoyod_customer_id;
            $validated['test_request_id'] = $testRequest->id;
            $validated['status'] = 'pending';
            $validated['artifact_code'] = Artifact::generateArtifactCode($validated['type']);

            $artifact = Artifact::create($validated);

            \Log::info('Artifact created for test request', [
                'artifact_id' => $artifact->id,
                'test_request_id' => $testRequest->id
            ]);

            return redirect()->back()->with('success', 'Artifact added successfully.');
        } catch (\Exception $e) {
            \Log::error('Error creating artifact for test request', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to add artifact.']);
        }
    }

    /**
     * Shared data for test-request print views (full print vs lab delivery file).
     */
    protected function buildTestRequestPrintData(TestRequest $testRequest): array
    {
        $qoyodService = new QoyodService();
        $customer = $qoyodService->getCustomer($testRequest->qoyod_customer_id);

        if (!$customer) {
            throw new \RuntimeException('Customer not found in Qoyod.');
        }

        $artifacts = Artifact::where('test_request_id', $testRequest->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $evaluatedPiecesCount = Artifact::where('test_request_id', $testRequest->id)
            ->whereIn('status', ['evaluated', 'certified'])
            ->count();
        $pendingPiecesCount = Artifact::where('test_request_id', $testRequest->id)
            ->whereIn('status', ['pending', 'under_evaluation'])
            ->count();

        $formattedCustomer = [
            'id' => $customer['id'] ?? $testRequest->qoyod_customer_id,
            'full_name' => $customer['name'] ?? $customer['display_name'] ?? '',
            'company_name' => $customer['organization'] ?? null,
            'customer_code' => 'CUS' . str_pad($testRequest->qoyod_customer_id, 3, '0', STR_PAD_LEFT),
            'phone' => $customer['phone_number'] ?? $customer['phone'] ?? null,
            'email' => $customer['email'] ?? $customer['email_address'] ?? null,
            'address' => 'الرياض',
            'qoyod_customer_id' => $testRequest->qoyod_customer_id,
            'status' => $customer['status'] ?? 'active',
        ];

        return compact(
            'testRequest',
            'formattedCustomer',
            'artifacts',
            'evaluatedPiecesCount',
            'pendingPiecesCount'
        );
    }

    /**
     * Show print page for test request (replaces PDF download with browser print)
     */
    public function showPrintPage(TestRequest $testRequest)
    {
        try {
            \Log::info('Showing test request print page', ['test_request_id' => $testRequest->id]);

            $data = $this->buildTestRequestPrintData($testRequest);

            return view('test-request-print', array_merge($data, ['labDeliveryFile' => false]));
        } catch (\RuntimeException $e) {
            \Log::warning('Test request print: ' . $e->getMessage(), ['test_request_id' => $testRequest->id]);

            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('Error showing test request print page', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while showing print page: ' . $e->getMessage()]);
        }
    }

    /**
     * Laboratory delivery file: same layout as print but no PII, no terms, Samples Delivery Record header.
     */
    public function showLabDeliveryPrint(TestRequest $testRequest)
    {
        try {
            \Log::info('Showing lab delivery print page', ['test_request_id' => $testRequest->id]);

            $data = $this->buildTestRequestPrintData($testRequest);

            return view('test-request-print', array_merge($data, ['labDeliveryFile' => true]));
        } catch (\RuntimeException $e) {
            \Log::warning('Lab delivery print: ' . $e->getMessage(), ['test_request_id' => $testRequest->id]);

            return redirect()->route($this->labWorkflowErrorFallbackRoute())
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('Error showing lab delivery print page', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while showing lab delivery file: ' . $e->getMessage()]);
        }
    }

    /**
     * Create a new redelivery batch (document) with explicit delivered / remaining counts.
     */
    public function storeRedelivery(Request $request, TestRequest $testRequest)
    {
        $validated = $request->validate([
            'delivered_pieces_count' => 'required|integer|min:0',
            'remaining_pieces_count' => 'required|integer|min:0',
        ], [
            'delivered_pieces_count.required' => 'أدخل عدد القطع المسلمة | Enter delivered pieces count.',
            'remaining_pieces_count.required' => 'أدخل عدد القطع المتبقية | Enter remaining pieces count.',
        ]);

        $redelivery = TestRequestRedelivery::create([
            'test_request_id' => $testRequest->id,
            'delivered_pieces_count' => $validated['delivered_pieces_count'],
            'remaining_pieces_count' => $validated['remaining_pieces_count'],
        ]);

        \Log::info('Redelivery batch created', [
            'test_request_id' => $testRequest->id,
            'redelivery_id' => $redelivery->id,
        ]);

        app(WorkflowNotificationService::class)->notifyReception(
            'redelivery_created',
            $testRequest,
            auth()->user(),
            ['batch_id' => $redelivery->id]
        );

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Redelivery document created.',
                'redelivery' => $redelivery,
                'print_url' => route('dashboard.test-requests.redeliveries.print', [
                    'testRequest' => $testRequest->id,
                    'redelivery' => $redelivery->id,
                ]),
            ]);
        }

        return back()->with('success', 'تم إنشاء مستند إعادة التسليم | Redelivery document created.');
    }

    /**
     * Redelivery from lab to reception: one print per batch; footer uses stored counts.
     */
    public function showRedeliveryPrint(TestRequest $testRequest, TestRequestRedelivery $redelivery)
    {
        try {
            if ((int) $redelivery->test_request_id !== (int) $testRequest->id) {
                abort(404);
            }

            \Log::info('Showing redelivery print page', [
                'test_request_id' => $testRequest->id,
                'redelivery_id' => $redelivery->id,
            ]);

            $data = $this->buildTestRequestPrintData($testRequest);

            return view('test-request-print', array_merge($data, [
                'labDeliveryFile' => true,
                'redeliveryFromLabPrint' => true,
                'redelivery' => $redelivery,
            ]));
        } catch (\RuntimeException $e) {
            \Log::warning('Redelivery print: ' . $e->getMessage(), ['test_request_id' => $testRequest->id]);

            return redirect()->route($this->labWorkflowErrorFallbackRoute())
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            \Log::error('Error showing redelivery print page', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while showing redelivery document: ' . $e->getMessage()]);
        }
    }

    /**
     * Download test request as PDF directly - uses print page with auto-download
     */
    public function downloadPdfDirect(TestRequest $testRequest)
    {
        // Redirect to print page with auto_download parameter
        // This will open the print dialog automatically with perfect quality
        return redirect()->route('dashboard.test-requests.print', $testRequest->id)
            ->with('auto_download', true);
    }


    /**
     * Upload signed document
     */
    public function uploadSignedDocument(Request $request, TestRequest $testRequest)
    {
        try {
            \Log::info('Starting upload process', [
                'test_request_id' => $testRequest->id,
                'has_file' => $request->hasFile('signed_document'),
                'request_method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]);

            // Validate input
            $validated = $request->validate([
                'signed_document' => [
                    'required',
                    'file',
                    'mimes:pdf',
                    'max:10240'
                ]
            ], [
                'signed_document.required' => 'Please select a PDF file to upload.',
                'signed_document.file' => 'The uploaded file is not valid.',
                'signed_document.mimes' => 'Only PDF files are allowed.',
                'signed_document.max' => 'File size must be less than 10MB.'
            ]);

            // Delete old signed document if exists
            if ($testRequest->signed_document_path) {
                delete_file_anywhere($testRequest->signed_document_path);
                \Log::info('Deleted old signed document', ['old_path' => $testRequest->signed_document_path]);
            }

            // Store the signed document to Spaces
            $file = $request->file('signed_document');
            $originalName = $file->getClientOriginalName();
            $filename = 'signed-test-request-' . $testRequest->receiving_record_no . '-' . time() . '.pdf';
            $uploadDir = 'test-requests/signed';
            
            $path = upload_file($file, $uploadDir, $filename);
            
            if (!$path) {
                throw new \Exception('Failed to store uploaded file to Spaces');
            }

            // Update test request with signed document path
            $updated = $testRequest->update([
                'signed_document_path' => $path,
                'status' => 'signed'
            ]);

            if (!$updated) {
                // Clean up uploaded file if database update fails
                delete_file_anywhere($path);
                throw new \Exception('Failed to update database record');
            }

            \Log::info('Signed document uploaded successfully', [
                'test_request_id' => $testRequest->id,
                'original_filename' => $originalName,
                'stored_path' => $path,
                'file_size' => $file->getSize()
            ]);

            return back()->with('success', 'تم رفع المستند الموقع بنجاح! | Signed document uploaded successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed during upload', [
                'test_request_id' => $testRequest->id,
                'validation_errors' => $e->errors()
            ]);

            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            \Log::error('Upload failed with exception', [
                'test_request_id' => $testRequest->id,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors(['error' => 'فشل في رفع الملف: ' . $e->getMessage() . ' | Upload failed: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Upload signed lab delivery document (PDF) — stored on Spaces via upload_file().
     */
    public function uploadLabDeliverySigned(Request $request, TestRequest $testRequest)
    {
        try {
            $request->validate([
                'lab_delivery_signed_document' => [
                    'required',
                    'file',
                    'mimes:pdf',
                    'max:10240',
                ],
            ], [
                'lab_delivery_signed_document.required' => 'يرجى اختيار ملف PDF | Please select a PDF file.',
                'lab_delivery_signed_document.mimes' => 'يُسمح بملفات PDF فقط | Only PDF files are allowed.',
                'lab_delivery_signed_document.max' => 'حجم الملف أقل من 10 ميجابايت | File size must be less than 10MB.',
            ]);

            if ($testRequest->lab_delivery_signed_document_path) {
                delete_file_anywhere($testRequest->lab_delivery_signed_document_path);
            }

            $file = $request->file('lab_delivery_signed_document');
            $filename = 'lab-delivery-' . $testRequest->receiving_record_no . '-' . time() . '.pdf';
            $uploadDir = 'test-requests/lab-delivery-signed';

            $path = upload_file($file, $uploadDir, $filename);

            if (! $path) {
                throw new \Exception('Failed to store lab delivery file to Spaces');
            }

            if (! $testRequest->update(['lab_delivery_signed_document_path' => $path])) {
                delete_file_anywhere($path);
                throw new \Exception('Failed to update database record');
            }

            \Log::info('Lab delivery signed document uploaded', [
                'test_request_id' => $testRequest->id,
                'path' => $path,
            ]);

            app(WorkflowNotificationService::class)->notifyLab(
                'lab_delivery_uploaded',
                $testRequest->fresh(),
                auth()->user()
            );

            return back()->with('success', 'تم رفع ملف التسليم للمختبر بنجاح | Lab delivery file uploaded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Lab delivery upload failed', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Upload signed PDF for a specific redelivery batch — Spaces via upload_file().
     */
    public function uploadRedeliverySigned(Request $request, TestRequest $testRequest, TestRequestRedelivery $redelivery)
    {
        try {
            if ((int) $redelivery->test_request_id !== (int) $testRequest->id) {
                abort(404);
            }

            $request->validate([
                'signed_document' => [
                    'required',
                    'file',
                    'mimes:pdf',
                    'max:10240',
                ],
            ], [
                'signed_document.required' => 'يرجى اختيار ملف PDF | Please select a PDF file.',
                'signed_document.mimes' => 'يُسمح بملفات PDF فقط | Only PDF files are allowed.',
                'signed_document.max' => 'حجم الملف أقل من 10 ميجابايت | File size must be less than 10MB.',
            ]);

            if ($redelivery->signed_document_path) {
                delete_file_anywhere($redelivery->signed_document_path);
            }

            $file = $request->file('signed_document');
            $filename = 'redelivery-lab-' . $testRequest->receiving_record_no . '-r' . $redelivery->id . '-' . time() . '.pdf';
            $uploadDir = 'test-requests/redelivery-from-lab-signed';

            $path = upload_file($file, $uploadDir, $filename);

            if (! $path) {
                throw new \Exception('Failed to store redelivery file to Spaces');
            }

            if (! $redelivery->update(['signed_document_path' => $path])) {
                delete_file_anywhere($path);
                throw new \Exception('Failed to update database record');
            }

            \Log::info('Redelivery batch document uploaded', [
                'test_request_id' => $testRequest->id,
                'redelivery_id' => $redelivery->id,
                'path' => $path,
            ]);

            app(WorkflowNotificationService::class)->notifyReception(
                'redelivery_file_uploaded',
                $testRequest,
                auth()->user(),
                ['batch_id' => $redelivery->id]
            );

            return back()->with('success', 'تم رفع ملف إعادة التسليم بنجاح | Redelivery file uploaded successfully.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Redelivery upload failed', [
                'test_request_id' => $testRequest->id,
                'redelivery_id' => $redelivery->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Delete a test request
     */
    public function destroy(TestRequest $testRequest)
    {
        try {
            \Log::info('Deleting test request', ['test_request_id' => $testRequest->id]);

            if ($testRequest->signed_document_path) {
                delete_file_anywhere($testRequest->signed_document_path);
            }
            if ($testRequest->lab_delivery_signed_document_path) {
                delete_file_anywhere($testRequest->lab_delivery_signed_document_path);
            }

            $testRequest->load('redeliveries');
            foreach ($testRequest->redeliveries as $r) {
                $r->delete();
            }

            // Delete associated artifacts
            $testRequest->artifacts()->delete();

            // Delete test request
            $testRequest->delete();
            
            \Log::info('Test request deleted successfully', ['test_request_id' => $testRequest->id]);
            
            return redirect()->back()->with('success', 'Test request deleted successfully. | تم حذف طلب الاختبار بنجاح.');
        } catch (\Exception $e) {
            \Log::error('Error deleting test request', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()->withErrors(['error' => 'Failed to delete test request. | فشل حذف طلب الاختبار.']);
        }
    }

    /**
     * Calculate expected delivery date based on delivery type
     */
    private function calculateExpectedDate($deliveryType)
    {
        $today = \Carbon\Carbon::now();
        
        switch ($deliveryType) {
            case 'Regular':
                // بعد 7 أيام عمل (تجنب الجمعة)
                return $this->addBusinessDays($today, 7);
            case 'Express Service':
            case 'Same Day':
                // نفس اليوم - إذا كان جمعة، اجعله السبت
                return $this->skipFridayIfNeeded($today);
            case '24 hours':
                // الغد (تجنب الجمعة)
                return $this->addBusinessDays($today, 1);
            case '48 hours':
                // بعد الغد (تجنب الجمعة)
                return $this->addBusinessDays($today, 2);
            case '72 hours':
                // بعد 3 أيام عمل (تجنب الجمعة)
                return $this->addBusinessDays($today, 3);
            default:
                return null;
        }
    }

    /**
     * Add business days (skip Fridays)
     */
    private function addBusinessDays($startDate, $days)
    {
        $date = $startDate->copy();
        $addedDays = 0;
        
        while ($addedDays < $days) {
            $date->addDay();
            // Skip Friday (5 = Friday in Carbon)
            if ($date->dayOfWeek !== 5) {
                $addedDays++;
            }
        }
        
        return $date;
    }

    /**
     * Skip Friday if the given date is Friday
     */
    private function skipFridayIfNeeded($date)
    {
        $resultDate = $date->copy();
        // If it's Friday (5), move to Saturday (6)
        if ($resultDate->dayOfWeek === 5) {
            $resultDate->addDay();
        }
        return $resultDate;
    }

    /**
     * Lab role cannot access dashboard.customers; use Items page when redirecting after print errors.
     */
    private function labWorkflowErrorFallbackRoute(): string
    {
        return auth()->user()?->role === 'lab' ? 'dashboard.artifacts' : 'dashboard.customers';
    }

}