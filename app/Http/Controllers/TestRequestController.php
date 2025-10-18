<?php

namespace App\Http\Controllers;

use App\Models\TestRequest;
use App\Models\Artifact;
use App\Services\QoyodService;
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
                'received_in' => 'Dashboard',
                'received_by' => auth()->user() ? auth()->user()->name : null,
                'status' => 'pending'
            ]);
            
            \Log::info('Created new test request', ['test_request' => $testRequest]);

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
                'address' => $customer['address'] ?? null,
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
                'service' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'unit_type' => 'nullable|in:carat,gram',
                'price' => 'nullable|numeric|min:0',
                'notes' => 'nullable|string',
                'delivery_type' => 'nullable|string|max:255'
            ]);

            // Add required fields
            $validated['qoyod_customer_id'] = $testRequest->qoyod_customer_id;
            $validated['test_request_id'] = $testRequest->id;
            $validated['status'] = 'pending';

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
     * Show print page for test request (replaces PDF download with browser print)
     */
    public function showPrintPage(TestRequest $testRequest)
    {
        try {
            \Log::info('Showing test request print page', ['test_request_id' => $testRequest->id]);
            
            // Get customer info from Qoyod
            $qoyodService = new QoyodService();
            $customer = $qoyodService->getCustomer($testRequest->qoyod_customer_id);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $testRequest->qoyod_customer_id]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get artifacts for this test request
            $artifacts = Artifact::where('test_request_id', $testRequest->id)
                ->orderBy('created_at', 'desc')
                ->get();

            // Format customer data
            $formattedCustomer = [
                'id' => $customer['id'] ?? $testRequest->qoyod_customer_id,
                'full_name' => $customer['name'] ?? $customer['display_name'] ?? '',
                'company_name' => $customer['organization'] ?? null,
                'customer_code' => 'CUS' . str_pad($testRequest->qoyod_customer_id, 3, '0', STR_PAD_LEFT),
                'phone' => $customer['phone_number'] ?? $customer['phone'] ?? null,
                'email' => $customer['email'] ?? $customer['email_address'] ?? null,
                'address' => $customer['address'] ?? null,
                'qoyod_customer_id' => $testRequest->qoyod_customer_id,
                'status' => $customer['status'] ?? 'active'
            ];

            return view('test-request-print', compact('testRequest', 'formattedCustomer', 'artifacts'));

        } catch (\Exception $e) {
            \Log::error('Error showing test request print page', [
                'test_request_id' => $testRequest->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while showing print page: ' . $e->getMessage()]);
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

            // Ensure directory exists
            $uploadDir = 'test-requests/signed';
            Storage::disk('public')->makeDirectory($uploadDir);

            // Delete old signed document if exists
            if ($testRequest->signed_document_path && Storage::disk('public')->exists($testRequest->signed_document_path)) {
                Storage::disk('public')->delete($testRequest->signed_document_path);
                \Log::info('Deleted old signed document', ['old_path' => $testRequest->signed_document_path]);
            }

            // Store the signed document
            $file = $request->file('signed_document');
            $originalName = $file->getClientOriginalName();
            $filename = 'signed-test-request-' . $testRequest->receiving_record_no . '-' . time() . '.pdf';
            
            $path = $file->storeAs($uploadDir, $filename, 'public');
            
            if (!$path) {
                throw new \Exception('Failed to store uploaded file');
            }

            // Update test request with signed document path
            $updated = $testRequest->update([
                'signed_document_path' => $path,
                'status' => 'signed'
            ]);

            if (!$updated) {
                // Clean up uploaded file if database update fails
                Storage::disk('public')->delete($path);
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

}