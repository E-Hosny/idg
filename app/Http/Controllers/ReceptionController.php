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
        $data = $request->validate([
            // Client
            'full_name' => 'required|string|max:255',
            'national_id' => 'nullable|string|max:50',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:255',
            'nationality' => 'nullable|string|max:100',
            'address' => 'nullable|string|max:255',
            'client_notes' => 'nullable|string|max:1000',
            // Artifact
            'artifact_name' => 'required|string|max:255',
            'artifact_description' => 'nullable|string|max:2000',
            'artifact_type' => 'required|string|max:100',
            'origin_country' => 'nullable|string|max:100',
            'year_made' => 'nullable|string|max:20',
            'materials' => 'nullable|string|max:255',
            'weight' => 'nullable|string|max:50',
            'dimensions' => 'nullable|string|max:50',
            'condition' => 'required|string|max:50',
            'artifact_notes' => 'nullable|string|max:1000',
        ]);

        try {
            \DB::beginTransaction();

            $client = Client::create([
                'full_name' => $data['full_name'],
                'national_id' => $data['national_id'] ?? null,
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'nationality' => $data['nationality'] ?? null,
                'address' => $data['address'] ?? null,
                'notes' => $data['client_notes'] ?? null,
            ]);

            $artifact = Artifact::create([
                'client_id' => $client->id,
                'artifact_code' => Artifact::generateArtifactCode(),
                'title' => [
                    'en' => $data['artifact_name'],
                    'ar' => $data['artifact_name']
                ],
                'description' => $data['artifact_description'] ? [
                    'en' => $data['artifact_description'],
                    'ar' => $data['artifact_description']
                ] : null,
                'origin_country' => $data['origin_country'] ?? null,
                'period' => $data['year_made'] ?? null,
                'material' => $data['materials'] ?? null,
                'dimensions' => $data['dimensions'] ?? null,
                'condition' => $data['condition'],
                'internal_notes' => $data['artifact_notes'] ?? null,
                'status' => 'pending',
            ]);

            \DB::commit();

            return redirect()->route('reception.index')->with('success', __('Client and artifact registered successfully.'));
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->withErrors(['error' => __('An error occurred while saving. Please try again.')]);
        }
    }
} 