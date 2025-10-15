<?php

namespace App\Http\Controllers;

use App\Models\TestRequest;
use App\Models\Artifact;
use App\Services\QoyodService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestRequestController extends Controller
{
    /**
     * Display the test request for a customer
     */
    public function show($customerId)
    {
        try {
            \Log::info('Showing customer test request', ['customer_id' => $customerId]);
            
            // Get customer info from Qoyod
            $qoyodService = new QoyodService();
            $customer = $qoyodService->getCustomer($customerId);
            
            \Log::info('Customer data from Qoyod', ['customer' => $customer]);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Check if there's an existing test request for this customer
            $testRequest = TestRequest::where('qoyod_customer_id', $customerId)->first();
            
            // If no test request exists, create one
            if (!$testRequest) {
                $testRequest = TestRequest::create([
                    'qoyod_customer_id' => $customerId,
                    'receiving_record_no' => TestRequest::generateReceivingRecordNo(),
                    'received_date' => now()->toDateString(),
                    'received_in' => 'Dashboard',
                    'received_by' => auth()->user() ? auth()->user()->name : null,
                    'status' => 'pending'
                ]);
                
                \Log::info('Created new test request', ['test_request' => $testRequest]);
            }

            // Get artifacts for this test request
            $artifacts = Artifact::where('qoyod_customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Update artifacts to link with test request if not already linked
            foreach ($artifacts as $artifact) {
                if (!$artifact->test_request_id) {
                    $artifact->update(['test_request_id' => $testRequest->id]);
                }
            }

            \Log::info('Artifacts found', ['count' => $artifacts->count()]);

            // Format customer data to match Reception format structure
            $formattedCustomer = [
                'id' => $customer['id'] ?? $customerId,
                'full_name' => $customer['name'] ?? $customer['display_name'] ?? '',
                'company_name' => $customer['organization'] ?? null,
                'customer_code' => 'CUS' . str_pad($customerId, 3, '0', STR_PAD_LEFT),
                'phone' => $customer['phone_number'] ?? $customer['phone'] ?? null,
                'email' => $customer['email'] ?? $customer['email_address'] ?? null,
                'address' => $customer['address'] ?? null,
                'qoyod_customer_id' => $customerId,
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
            \Log::error('Error showing customer test request', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => 'An error occurred while showing test request.']);
        }
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
    public function storeArtifact(Request $request, $customerId)
    {
        try {
            // Get or create test request
            $testRequest = TestRequest::where('qoyod_customer_id', $customerId)->first();
            
            if (!$testRequest) {
                $testRequest = TestRequest::create([
                    'qoyod_customer_id' => $customerId,
                    'receiving_record_no' => TestRequest::generateReceivingRecordNo(),
                    'received_date' => now()->toDateString(),
                    'received_in' => 'Dashboard',
                    'received_by' => auth()->user() ? auth()->user()->name : null,
                    'status' => 'pending'
                ]);
            }

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
            $validated['qoyod_customer_id'] = $customerId;
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
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to add artifact.']);
        }
    }
}