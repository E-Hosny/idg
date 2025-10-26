<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Artifact;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Services\PricingService;

class ReceptionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index()
    {
        $clients = Client::with('artifacts')->orderByDesc('created_at')->paginate(15);
        return Inertia::render('Reception/Index', [
            'clients' => $clients,
        ]);
    }

    public function createClient()
    {
        return Inertia::render('Reception/NewClient');
    }

    public function storeClient(Request $request)
    {
        \Log::info('Received client data:', $request->all());
        
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'delivery_date' => 'nullable|date',
            'received_in' => 'nullable|string|max:255',
            'artifacts' => 'required|array|min:1',
            'artifacts.*.type' => 'required|string|max:100',
            'artifacts.*.service' => 'nullable|string|max:100',
            'artifacts.*.weight' => 'nullable|string|max:50',
            'artifacts.*.weight_unit' => 'nullable|in:ct,gm',
            'artifacts.*.notes' => 'nullable|string|max:1000',
            'artifacts.*.delivery_type' => 'nullable|string|max:100',
        ]);

        \Log::info('Validation passed, creating client...');

        try {
            \DB::beginTransaction();

            $lastClient = \App\Models\Client::orderByDesc('id')->first();
            $nextCode = 'C' . str_pad(($lastClient ? ($lastClient->id + 1) : 1), 3, '0', STR_PAD_LEFT);

            $today = date('ymd');
            $lastToday = \App\Models\Client::where('receiving_record_no', 'like', $today.'%')->orderByDesc('receiving_record_no')->first();
            $nextSeq = $lastToday ? ((int)substr($lastToday->receiving_record_no, 6) + 1) : 1;
            $nextRecordNo = $today . str_pad($nextSeq, 4, '0', STR_PAD_LEFT);

            $receivedDate = now()->toDateString();

            $client = Client::create([
                'full_name' => $data['full_name'],
                'company_name' => $data['company_name'] ?? null,
                'customer_code' => $nextCode,
                'receiving_record_no' => $nextRecordNo,
                'phone' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'address' => $data['city'] ?? null,
                'received_date' => $receivedDate,
                'delivery_date' => $data['delivery_date'] ?? null,
                'received_in' => $data['received_in'] ?? 'الرياض',
                'created_by' => auth()->id(),
            ]);

            \Log::info('Client created:', $client->toArray());

            foreach ($data['artifacts'] as $artifactData) {
                \Log::info('Creating artifact with data:', $artifactData);
                
                // Generate artifact code with retry mechanism
                $artifactCode = null;
                $maxRetries = 5;
                $retryCount = 0;
                
                while ($retryCount < $maxRetries) {
                    try {
                        $artifactCode = \App\Models\Artifact::generateArtifactCode($artifactData['type']);
                        \Log::info("Generated artifact code: {$artifactCode}");
                        break;
                    } catch (\Exception $e) {
                        $retryCount++;
                        \Log::warning("Failed to generate artifact code, attempt {$retryCount}: " . $e->getMessage());
                        if ($retryCount >= $maxRetries) {
                            throw new \Exception('Failed to generate unique artifact code after multiple attempts');
                        }
                        // Small delay before retry
                        usleep(100000); // 0.1 seconds
                    }
                }
                
                // حساب السعر تلقائياً
                $price = null;
                if ($artifactData['type'] && $artifactData['service'] && $artifactData['weight']) {
                    $weight = floatval($artifactData['weight']);
                    $price = PricingService::calculatePrice(
                        $artifactData['type'],
                        $artifactData['service'],
                        $weight
                    );
                }

                $artifact = \App\Models\Artifact::create([
                    'client_id' => $client->id,
                    'artifact_code' => $artifactCode,
                    'type' => $artifactData['type'],
                    'service' => $artifactData['service'] ?? null,
                    'weight' => $artifactData['weight'] ?? null,
                    'weight_unit' => $artifactData['weight_unit'] ?? null,
                    'price' => $price,
                    'notes' => $artifactData['notes'] ?? null,
                    'delivery_type' => $artifactData['delivery_type'] ?? null,
                    'status' => 'pending',
                    'title' => ['en' => '', 'ar' => ''],
                    'description' => ['en' => '', 'ar' => ''],
                    'category_id' => null,
                ]);
                \Log::info('Artifact created:', $artifact->toArray());
            }

            \DB::commit();
            \Log::info('Client and artifacts saved successfully');

            return redirect()->route('reception.index')->with('success', 'Client and artifacts registered successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error saving client:', ['error' => $e->getMessage()]);
            return back()->withErrors(['error' => 'An error occurred while saving. Please try again.']);
        }
    }

    public function showClient(\App\Models\Client $client)
    {
        $client->load('artifacts');
        $receivedBy = $client->created_by ? \App\Models\User::find($client->created_by)?->name : null;
        
        return \Inertia\Inertia::render('Reception/ShowClient', [
            'client' => $client,
            'received_by' => $receivedBy,
            'receiving_record_no' => $client->receiving_record_no,
        ]);
    }

    public function createArtifact($clientId)
    {
        return \Inertia\Inertia::render('Reception/AddArtifact', [
            'client_id' => (int) $clientId,
        ]);
    }

    public function storeArtifact(Request $request, $clientId)
    {
        $data = $request->validate([
            'type' => 'required|string|max:100',
            'service' => 'nullable|string|max:100',
            'weight' => 'nullable|string|max:50',
            'weight_unit' => 'nullable|in:ct,gm',
            'delivery_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);
        // حساب السعر تلقائياً
        $price = null;
        if ($data['type'] && $data['service'] && $data['weight']) {
            $weight = floatval($data['weight']);
            $price = PricingService::calculatePrice(
                $data['type'],
                $data['service'],
                $weight
            );
        }

        \Log::info('Creating single artifact with data:', $data);
        $artifact = \App\Models\Artifact::create([
            'client_id' => $clientId,
            'artifact_code' => \App\Models\Artifact::generateArtifactCode($data['type']),
            'type' => $data['type'],
            'service' => $data['service'] ?? null,
            'weight' => $data['weight'] ?? null,
            'weight_unit' => $data['weight_unit'] ?? null,
            'price' => $price,
            'delivery_type' => $data['delivery_type'] ?? null,
            'notes' => $data['notes'] ?? null,
            'status' => 'pending',
            'title' => json_encode(['en' => '', 'ar' => '']),
            'description' => json_encode(['en' => '', 'ar' => '']),
            'category_id' => null,
        ]);
        \Log::info('Single artifact created:', $artifact->toArray());
        return redirect()->route('reception.show-client', $clientId)->with('success', 'Artifact added successfully.');
    }

    /**
     * حساب السعر في الوقت الفعلي
     */
    public function calculatePrice(Request $request)
    {
        \Log::info('calculatePrice endpoint called with:', $request->all());

        $data = $request->validate([
            'type' => 'required|string',
            'service' => 'required|string',
            'weight' => 'required|numeric|min:0',
        ]);

        \Log::info('Validated data:', $data);

        $price = PricingService::calculatePrice(
            $data['type'],
            $data['service'],
            $data['weight']
        );

        $weightRange = PricingService::getWeightRange(
            $data['type'],
            $data['service'],
            $data['weight']
        );

        $response = [
            'price' => $price,
            'weight_range' => $weightRange,
            'currency' => 'SAR'
        ];

        \Log::info('calculatePrice response:', $response);

        return response()->json($response);
    }

    /**
     * اختبار حساب السعر - للتشخيص
     */
    public function testPricing(Request $request)
    {
        \Log::info('testPricing called');
        
        // اختبار البيانات المرسلة
        \Log::info('Request data:', $request->all());
        
        // اختبار البحث المباشر
        $pricing = \App\Models\Pricing::where('artifact_type', 'Colored Gemstones')
            ->where('service_type', 'Regular - ID Report')
            ->where('min_weight', '<=', 2.0)
            ->where(function($query) {
                $query->where('max_weight', '>=', 2.0)
                      ->orWhereNull('max_weight');
            })
            ->orderBy('min_weight', 'desc')
            ->first();
            
        if ($pricing) {
            \Log::info('Direct query found pricing:', $pricing->toArray());
        } else {
            \Log::warning('Direct query found no pricing');
        }
        
        // اختبار جميع البيانات المتاحة
        $allPricing = \App\Models\Pricing::all(['artifact_type', 'service_type', 'min_weight', 'max_weight', 'price']);
        \Log::info('All pricing data:', $allPricing->toArray());
        
        return response()->json([
            'test_pricing' => $pricing ? $pricing->toArray() : null,
            'total_pricing_records' => $allPricing->count(),
            'sample_records' => $allPricing->take(5)->toArray()
        ]);
    }

    /**
     * حذف العميل وجميع العناصر المرتبطة به
     */
    public function deleteClient(Client $client)
    {
        try {
            \DB::beginTransaction();

            // حذف جميع العناصر المرتبطة بالعميل أولاً
            $client->artifacts()->delete();

            // حذف العميل
            $client->delete();

            \DB::commit();

            return redirect()->route('reception.index')->with('success', 'Client and all associated items have been deleted successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Error deleting client: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Error deleting client. Please try again.']);
        }
    }

    /**
     * عرض صفحة تعديل العميل
     */
    public function editClient(Client $client)
    {
        return Inertia::render('Reception/EditClient', [
            'client' => $client,
        ]);
    }

    /**
     * تحديث بيانات العميل
     */
    public function updateClient(Request $request, Client $client)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'mobile' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'city' => 'nullable|string|max:100',
            'delivery_date' => 'nullable|date',
            'received_in' => 'nullable|string|max:255',
        ]);

        try {
            $client->update([
                'full_name' => $data['full_name'],
                'company_name' => $data['company_name'] ?? null,
                'phone' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'address' => $data['city'] ?? null,
                'delivery_date' => $data['delivery_date'] ?? null,
                'received_in' => $data['received_in'] ?? 'الرياض',
            ]);

            return redirect()->route('reception.show-client', $client->id)->with('success', 'Client information updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating client: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Error updating client. Please try again.']);
        }
    }

    /**
     * عرض صفحة تعديل العنصر
     */
    public function editArtifact(Artifact $artifact)
    {
        return Inertia::render('Reception/EditArtifact', [
            'artifact' => $artifact,
        ]);
    }

    /**
     * تحديث بيانات العنصر
     */
    public function updateArtifact(Request $request, Artifact $artifact)
    {
        $data = $request->validate([
            'type' => 'required|string|max:100',
            'service' => 'nullable|string|max:100',
            'weight' => 'nullable|string|max:50',
            'weight_unit' => 'nullable|in:ct,gm',
            'price' => 'nullable|string|max:50',
            'delivery_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            $artifact->update($data);

            return redirect()->route('reception.show-client', $artifact->client_id)->with('success', 'Item information updated successfully.');
        } catch (\Exception $e) {
            \Log::error('Error updating artifact: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Error updating item. Please try again.']);
        }
    }

    /**
     * حذف العنصر
     */
    public function deleteArtifact(Artifact $artifact)
    {
        try {
            $clientId = $artifact->client_id;
            $artifact->delete();

            return redirect()->route('reception.show-client', $clientId)->with('success', 'Item deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Error deleting artifact: ' . $e->getMessage());
            
            return back()->withErrors(['error' => 'Error deleting item. Please try again.']);
        }
    }
} 