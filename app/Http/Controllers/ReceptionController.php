<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Artifact;
use Illuminate\Http\Request;
use Inertia\Inertia;

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
            'artifacts' => 'required|array|min:1',
            'artifacts.*.type' => 'required|string|max:100',
            'artifacts.*.service' => 'nullable|string|max:100',
            'artifacts.*.weight' => 'nullable|string|max:50',
            'artifacts.*.notes' => 'nullable|string|max:1000',
            'artifacts.*.delivery_type' => 'nullable|string|max:100',
        ]);

        \Log::info('Validation passed, creating client...');

        try {
            \DB::beginTransaction();

            $lastClient = \App\Models\Client::orderByDesc('id')->first();
            $nextCode = 'C' . str_pad(($lastClient ? ($lastClient->id + 1) : 1), 3, '0', STR_PAD_LEFT);

            $receivedDate = now()->toDateString();

            $client = Client::create([
                'full_name' => $data['full_name'],
                'company_name' => $data['company_name'] ?? null,
                'customer_code' => $nextCode,
                'phone' => $data['mobile'],
                'email' => $data['email'] ?? null,
                'address' => $data['city'] ?? null,
                'received_date' => $receivedDate,
                'delivery_date' => $data['delivery_date'] ?? null,
                'created_by' => auth()->id(),
            ]);

            \Log::info('Client created:', $client->toArray());

            foreach ($data['artifacts'] as $artifactData) {
                \App\Models\Artifact::create([
                    'client_id' => $client->id,
                    'artifact_code' => \App\Models\Artifact::generateArtifactCode(),
                    'type' => $artifactData['type'],
                    'service' => $artifactData['service'] ?? null,
                    'weight' => $artifactData['weight'] ?? null,
                    'notes' => $artifactData['notes'] ?? null,
                    'delivery_type' => $artifactData['delivery_type'] ?? null,
                    'status' => 'pending',
                    'title' => json_encode(['en' => '', 'ar' => '']),
                    'description' => json_encode(['en' => '', 'ar' => '']),
                    'category_id' => null,
                ]);
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
        
        // Log for debugging
        \Log::info('ShowClient Debug', [
            'client_id' => $client->id,
            'created_by' => $client->created_by,
            'receivedBy' => $receivedBy,
        ]);
        
        return \Inertia\Inertia::render('Reception/ShowClient', [
            'client' => $client,
            'received_by' => $receivedBy,
        ]);
    }
} 