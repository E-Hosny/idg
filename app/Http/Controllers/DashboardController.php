<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artifact;
use App\Models\Category;
use App\Models\ArtifactEvaluation;
use App\Models\DiamondEvaluation;
use App\Models\User;
use Carbon\Carbon;
use Inertia\Inertia;
use App\Services\PricingService;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get dashboard statistics
        $stats = [
            'total_artifacts' => Artifact::count(),
            'pending_evaluations' => Artifact::whereIn('status', ['pending', 'under_evaluation'])->count(),
            'evaluated_artifacts' => Artifact::whereIn('status', ['evaluated', 'certified'])->count(),
            'certified_artifacts' => Artifact::where('status', 'certified')->count(),
            'total_categories' => Category::count(),
            'total_evaluators' => User::count(),
        ];

        // Recent artifacts
        $recentArtifacts = Artifact::with(['category', 'assignedTo'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Pending evaluations assigned to current user
        $myPendingEvaluations = Artifact::where('assigned_to', auth()->id())
            ->whereIn('status', ['pending', 'under_evaluation'])
            ->with(['category'])
            ->orderBy('evaluation_deadline', 'asc')
            ->limit(5)
            ->get();

        // Evaluation statistics by month (last 6 months)
        $evaluationStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $evaluationStats[] = [
                'month' => $date->format('M Y'),
                'completed' => ArtifactEvaluation::whereMonth('evaluation_date', $date->month)
                    ->whereYear('evaluation_date', $date->year)
                    ->where('is_final', true)
                    ->count(),
            ];
        }

        // Category distribution
        $categoryStats = Category::withCount('artifacts')
            ->orderBy('artifacts_count', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard/Index', [
            'stats' => $stats,
            'recentArtifacts' => $recentArtifacts,
            'myPendingEvaluations' => $myPendingEvaluations,
            'evaluationStats' => $evaluationStats,
            'categoryStats' => $categoryStats,
        ]);
    }

    public function artifacts(Request $request)
    {
        $query = Artifact::with(['category', 'assignedTo', 'client', 'testRequest']);
        
        // إذا تم تحديد status معين، فلتر حسب status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        // إذا تم تحديد view=pending، عرض القطع غير المقيمة فقط
        elseif ($request->get('view') === 'pending') {
            $query->whereIn('status', ['pending', 'under_evaluation']);
        }
        // وإلا عرض جميع القطع (Total Artifacts)
        
        $artifacts = $query->orderBy('created_at', 'desc')->paginate(15);
        
        // تحديد نوع العرض للصفحة
        $viewType = $request->get('view', 'all'); // all, pending
        
        return Inertia::render('Dashboard/Artifacts/Index', [
            'artifacts' => $artifacts,
            'viewType' => $viewType,
        ]);
    }

    public function evaluations()
    {
        // جلب التقييمات العامة
        $generalEvaluations = ArtifactEvaluation::with(['artifact.client', 'evaluator'])
            ->orderBy('evaluation_date', 'desc')
            ->get();

        // جلب تقييمات الألماس
        $diamondEvaluations = DiamondEvaluation::with(['artifact.client', 'evaluator'])
            ->orderBy('created_at', 'desc')
            ->get();

        // دمج التقييمات وترتيبها
        $allEvaluations = collect();
        
        foreach ($generalEvaluations as $eval) {
            $allEvaluations->push([
                'id' => $eval->id,
                'type' => 'general',
                'artifact' => $eval->artifact,
                'evaluator' => $eval->evaluator,
                'evaluation_date' => $eval->evaluation_date,
                'status' => $eval->is_final ? 'completed' : 'draft',
                'result' => $eval->result,
                'comments' => $eval->comments,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        }

        foreach ($diamondEvaluations as $eval) {
            $allEvaluations->push([
                'id' => $eval->id,
                'type' => 'diamond',
                'artifact' => $eval->artifact,
                'evaluator' => $eval->evaluator,
                'evaluation_date' => $eval->test_date,
                'status' => $eval->status,
                'result' => $eval->result,
                'comments' => $eval->comments,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        }

        // ترتيب التقييمات حسب التاريخ
        $sortedEvaluations = $allEvaluations->sortByDesc('evaluation_date')->values();

        // تقسيم الصفحات يدوياً
        $currentPage = request()->get('page', 1);
        $perPage = 15;
        $currentPageEvaluations = $sortedEvaluations->slice(($currentPage - 1) * $perPage, $perPage)->values();

        return Inertia::render('Dashboard/Evaluations/Index', [
            'evaluations' => [
                'data' => $currentPageEvaluations,
                'current_page' => $currentPage,
                'last_page' => ceil($sortedEvaluations->count() / $perPage),
                'total' => $sortedEvaluations->count(),
            ],
        ]);
    }

    public function categories()
    {
        $categories = Category::withCount('artifacts')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(15);

        return Inertia::render('Dashboard/Categories/Index', [
            'categories' => $categories,
        ]);
    }

    public function analytics()
    {
        // Advanced analytics for the system
        $monthlyStats = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('M Y'),
                'new_artifacts' => Artifact::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'completed_evaluations' => ArtifactEvaluation::whereMonth('evaluation_date', $date->month)
                    ->whereYear('evaluation_date', $date->year)
                    ->where('is_final', true)
                    ->count(),
            ];
        }

        $statusDistribution = [
            'pending' => Artifact::where('status', 'pending')->count(),
            'under_evaluation' => Artifact::where('status', 'under_evaluation')->count(),
            'evaluated' => Artifact::where('status', 'evaluated')->count(),
            'certified' => Artifact::where('status', 'certified')->count(),
            'rejected' => Artifact::where('status', 'rejected')->count(),
        ];

        $conditionDistribution = [
            'excellent' => Artifact::where('condition', 'excellent')->count(),
            'very_good' => Artifact::where('condition', 'very_good')->count(),
            'good' => Artifact::where('condition', 'good')->count(),
            'fair' => Artifact::where('condition', 'fair')->count(),
            'poor' => Artifact::where('condition', 'poor')->count(),
        ];

        return Inertia::render('Dashboard/Analytics', [
            'monthlyStats' => $monthlyStats,
            'statusDistribution' => $statusDistribution,
            'conditionDistribution' => $conditionDistribution,
        ]);
    }

    public function evaluate(Artifact $artifact)
    {
        // تحديد صفحة التقييم المناسبة بناءً على نوع القطعة
        $evaluationPage = match ($artifact->type) {
            'Colored Gemstones', 'Other Colored Gemstones' => 'Dashboard/Artifacts/Evaluate',
            'Colorless Diamonds' => 'Dashboard/Artifacts/EvaluateDiamond',
            'Jewellery' => 'Dashboard/Artifacts/EvaluateJewellery',
            default => 'Dashboard/Artifacts/Evaluate'
        };

        // جلب التقييم الحالي إذا كان موجوداً
        $existingEvaluation = null;
        if ($artifact->type === 'Colorless Diamonds') {
            $existingEvaluation = $artifact->diamondEvaluations()
                ->where('evaluator_id', auth()->id())
                ->latest()
                ->first();
        } elseif ($artifact->type === 'Jewellery') {
            // للمجوهرات، احمّل من جدول jewellery_evaluations
            $existingEvaluation = $artifact->jewelleryEvaluations()
                ->where('evaluator_id', auth()->id())
                ->latest()
                ->first();
        }

        return Inertia::render($evaluationPage, [
            'artifact' => $artifact,
            'existingEvaluation' => $existingEvaluation,
        ]);
    }

    public function storeEvaluation(Request $request, Artifact $artifact)
    {
        \Log::info('storeEvaluation called:', [
            'artifact_id' => $artifact->id,
            'artifact_type' => $artifact->type,
            'artifact_code' => $artifact->artifact_code,
            'user_id' => auth()->id(),
            'request_method' => $request->method(),
            'request_url' => $request->url()
        ]);

        // التحقق من أن المستخدم مسجل دخول
        if (!auth()->check()) {
            \Log::error('User not authenticated');
            return redirect()->route('login');
        }

        try {
            // التحقق من نوع القطعة وحفظ التقييم المناسب
            if ($artifact->type === 'Colorless Diamonds') {
                \Log::info('Processing diamond evaluation');
                $this->storeDiamondEvaluation($request, $artifact);
                $message = 'Diamond evaluation saved successfully!';
            } elseif ($artifact->type === 'Jewellery') {
                \Log::info('Processing jewellery evaluation');
                $this->storeJewelleryEvaluation($request, $artifact);
                $message = 'Jewellery evaluation saved successfully!';
            } else {
                \Log::info('Processing general evaluation for type: ' . $artifact->type);
                // Colored Gemstones & Other Colored Gemstones
                $this->storeGeneralEvaluation($request, $artifact);
                $message = 'Evaluation saved successfully!';
            }

            \Log::info('Evaluation saved successfully, updating artifact status');

            // تحديث حالة القطعة حسب نوع التقييم
            if ($artifact->type === 'Colorless Diamonds') {
                // للألماس، تحقق من status
                if ($request->status === 'completed') {
                    $artifact->update([
                        'status' => 'evaluated',
                        'assigned_to' => auth()->id(),
                    ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
                    \Log::info('Diamond artifact status updated to evaluated');
                } else {
                    $artifact->update([
                        'status' => 'under_evaluation',
                        'assigned_to' => auth()->id(),
                    ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
                    \Log::info('Diamond artifact status updated to under_evaluation');
                }
            } else {
                // للأنواع الأخرى، التقييم النهائي يعني evaluated
                $artifact->update([
                    'status' => 'evaluated',
                    'assigned_to' => auth()->id(),
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
                \Log::info('General artifact status updated to evaluated');
            }

            // تحديث إضافي للحالة للتأكد من التحديث الصحيح
            $this->updateArtifactStatus($artifact, $artifact->type === 'Colorless Diamonds' ? 'diamond' : 'general');

            \Log::info('Evaluation process completed successfully');

            return redirect()
                ->route('dashboard.artifacts')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Evaluation save error: ' . $e->getMessage(), [
                'artifact_id' => $artifact->id,
                'artifact_type' => $artifact->type,
                'user_id' => auth()->id(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            return back()->withErrors(['error' => 'An error occurred while saving the evaluation. Please try again.']);
        }
    }

    /**
     * Update artifact status based on evaluation completion
     */
    private function updateArtifactStatus(Artifact $artifact, $evaluationType = 'general')
    {
        try {
            if ($evaluationType === 'diamond') {
                // للألماس، تحقق من وجود تقييم مكتمل
                $hasCompletedEvaluation = $artifact->diamondEvaluations()
                    ->where('status', 'completed')
                    ->exists();
            } else {
                // للأنواع الأخرى، تحقق من وجود تقييم نهائي
                $hasCompletedEvaluation = $artifact->evaluations()
                    ->where('is_final', true)
                    ->exists();
            }

            if ($hasCompletedEvaluation) {
                $artifact->update([
                    'status' => 'evaluated',
                    'assigned_to' => auth()->id(),
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
                \Log::info('Artifact status updated to evaluated:', [
                    'artifact_id' => $artifact->id,
                    'artifact_code' => $artifact->artifact_code
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating artifact status:', [
                'artifact_id' => $artifact->id,
                'error' => $e->getMessage()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        }
    }

    private function storeDiamondEvaluation(Request $request, Artifact $artifact)
    {
        // التحقق من صحة البيانات
        $validatedData = $request->validate([
            // Job Information
            'test_date' => 'required|date',
            'test_location' => 'nullable|string|max:255',
            'item_product_id' => 'nullable|string|max:255',
            'receiving_record' => 'nullable|string|max:255',
            'prepared_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            
            // Stone Information
            'weight' => 'nullable|numeric|min:0',
            'shape' => 'nullable|string|max:255',
            'laser_inscription' => 'nullable|in:Yes,No',
            
            // Lab-Grown Diamond Screen
            'hpht_screen' => 'nullable|in:Natural,Lab-Grown',
            'cvd_check' => 'nullable|in:Natural,Lab-Grown',
            
            // Proportion Grade
            'diameter' => 'nullable|numeric|min:0',
            'total_depth' => 'nullable|numeric|min:0',
            'table' => 'nullable|numeric|min:0',
            'star_facet' => 'nullable|numeric|min:0',
            'crown_angle' => 'nullable|numeric|min:0',
            'crown_height' => 'nullable|numeric|min:0',
            'girdle_thickness_min' => 'nullable|numeric|min:0',
            'girdle_thickness_max' => 'nullable|numeric|min:0',
            'pavilion_depth' => 'nullable|numeric|min:0',
            'pavilion_angle' => 'nullable|numeric|min:0',
            'lower_girdle' => 'nullable|numeric|min:0',
            'culet_size' => 'nullable|string|max:255',
            'girdle_condition' => 'nullable|string|max:255',
            'culet_condition' => 'nullable|string|max:255',
            
            // Grade Information
            'polish' => 'nullable|in:Excellent,Very Good,Good,Fair,Poor',
            'symmetry' => 'nullable|in:Excellent,Very Good,Good,Fair,Poor',
            'cut' => 'nullable|in:Excellent,Very Good,Good,Fair,Poor',
            
            // Visual Inspection
            'clarity' => 'nullable|in:FL,IF,VVS1,VVS2,VS1,VS2,SI1,SI2,I1,I2,I3',
            'colour' => 'nullable|in:D,E,F,G,H,I,J,K,L,M,N,O,P,Q,R,S,T,U,V,W,X,Y,Z',
            
            // Fluorescence
            'fluorescence_strength' => 'nullable|in:V. Str.,Str.,Med.,Faint,None',
            'fluorescence_colour' => 'nullable|string|max:255',
            
            // Result
            'result' => 'nullable|in:Pass,Fail,Reject',
            'stone_type' => 'nullable|in:Natural,Synthetic,Imitation,Treated',
            
            // Comments
            'comments' => 'nullable|string',
            
            // Grader
            'grader_name' => 'nullable|string|max:255',
            'grader_date' => 'nullable|date',
            'grader_signature' => 'nullable|string|max:255',
            
            // Analytical Equipment
            'analytical_name' => 'nullable|string|max:255',
            'analytical_date' => 'nullable|date',
            'analytical_signature' => 'nullable|string|max:255',
            
            // Retaining Information
            'retaining_place' => 'nullable|string|max:255',
            'retaining_by' => 'nullable|string|max:255',
            'retaining_date' => 'nullable|date',
            'retaining_signature' => 'nullable|string|max:255',
            
            // Reporting Information
            'report_done' => 'nullable|in:Yes,No',
            'label_done' => 'nullable|in:Yes,No',
            'report_done_by' => 'nullable|string|max:255',
                         'report_date' => 'nullable|date',
             'checked_by' => 'nullable|string|max:255',
             'report_number' => 'nullable|string|max:255',
             
             // Status
             'status' => 'required|in:draft,completed',
         ]);

        // إضافة البيانات الإضافية
        $validatedData['artifact_id'] = $artifact->id;
        $validatedData['evaluator_id'] = auth()->id();
        $validatedData['table_percentage'] = $validatedData['table'] ?? null;
        // الـ status يأتي من الـ form

        // البحث عن تقييم موجود أو إنشاء جديد
        $evaluation = DiamondEvaluation::updateOrCreate(
            [
                'artifact_id' => $artifact->id,
                'evaluator_id' => auth()->id(),
            ],
            $validatedData
        );

        return $evaluation;
    }

    private function storeGeneralEvaluation(Request $request, Artifact $artifact)
    {
        \Log::info('storeGeneralEvaluation called with:', [
            'artifact_id' => $artifact->id,
            'artifact_type' => $artifact->type,
            'request_data' => $request->all()
        ]);

        // التحقق من صحة البيانات للتقييم العام
        try {
            $validatedData = $request->validate([
                'test_date' => 'nullable|date',
                'test_location' => 'nullable|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'colour' => 'nullable|string|max:100',
                'transparency' => 'nullable|string|max:50',
                'lustre' => 'nullable|string|max:50',
                'tone' => 'nullable|string|max:50',
                'phenomena' => 'nullable|string|max:255',
                'saturation' => 'nullable|string|max:50',
                'measurements' => 'nullable|string|max:100',
                'shape_cut' => 'nullable|string|max:100',
                'pleochroism' => 'nullable|string|max:50',
                'optic_character' => 'nullable|string|max:50',
                'refractive_index' => 'nullable|array',
                'ri_result' => 'nullable|string|max:255',
                'inclusion' => 'nullable|string|max:1000',
                'weight_air' => 'nullable|numeric|min:0',
                'weight_water' => 'nullable|numeric|min:0',
                'sg_result' => 'nullable|string|max:255',
                'fluorescence_long' => 'nullable|string|max:50',
                'fluorescence_short' => 'nullable|string|max:50',
                'result' => 'nullable|string|max:255',
                'variety' => 'nullable|string|max:100',
                'species_group' => 'nullable|string|max:100',
                'comments' => 'nullable|string|max:1000',
                'grader_name' => 'nullable|string|max:255',
                'grader_date' => 'nullable|date',
                'analytical_interpretation' => 'nullable|string|max:2000',
                'retaining_place' => 'nullable|string|max:255',
                'retained_by' => 'nullable|string|max:255',
                'retained_date' => 'nullable|date',
                'report_done' => 'nullable|boolean',
                'label_done' => 'nullable|boolean',
                'checked_by' => 'nullable|string|max:255',
                'checked_date' => 'nullable|date',
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            \Log::info('Validation passed, validated data:', $validatedData);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed:', [
                'errors' => $e->errors(),
                'request_data' => $request->all()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            throw $e;
        }

        // إضافة البيانات الإضافية
        $validatedData['artifact_id'] = $artifact->id;
        $validatedData['evaluator_id'] = auth()->id();
        $validatedData['evaluation_date'] = now();
        $validatedData['is_final'] = true;
        $validatedData['detailed_notes'] = ['en' => '', 'ar' => ''];
        $validatedData['supporting_documents'] = [];

        \Log::info('Final data to be saved:', $validatedData);

        // حفظ التقييم
        try {
            $evaluation = ArtifactEvaluation::create($validatedData);
            \Log::info('General evaluation saved successfully:', $evaluation->toArray());
            return $evaluation;
        } catch (\Exception $e) {
            \Log::error('Error creating evaluation:', [
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'data' => $validatedData
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            throw $e;
        }
    }

    private function storeJewelleryEvaluation(Request $request, Artifact $artifact)
    {
        // التحقق من صحة البيانات للمجوهرات
        $validatedData = $request->validate([
            // Job Information
            'test_date' => 'nullable|date',
            'test_location' => 'nullable|string|max:255',
            'item_product_id' => 'nullable|string|max:255',
            'receiving_record' => 'nullable|string|max:255',
            'prepared_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            
            // Product Information
            'product_number' => 'nullable|string|max:255',
            'style_number' => 'nullable|string|max:255',
            'ref_number' => 'nullable|string|max:255',
            'gross_weight' => 'nullable|numeric|min:0',
            'net_weight' => 'nullable|numeric|min:0',
            'product_types' => 'nullable|array',
            'metal_types' => 'nullable|array',
            'stamps' => 'nullable|array',
            
            // Lab-Grown Diamond Screen
            'hpht_screen' => 'nullable|in:Natural,Lab-Grown',
            'hpht_diamond_pcs' => 'nullable|integer|min:0',
            'cvd_check' => 'nullable|in:Natural,Lab-Grown',
            'cvd_diamond_pcs' => 'nullable|integer|min:0',
            'need_unmount' => 'nullable|in:Yes,No',
            'unmount_reason' => 'nullable|string|max:255',
            
            // XRF Data
            'xrf_data' => 'nullable|array',
            
            // Diamond/s - Side Stones
            'side_stones_weight_type' => 'nullable|string|max:255',
            'side_stones_weight' => 'nullable|numeric|min:0',
            'side_stones_pieces' => 'nullable|integer|min:0',
            'side_stones_shapes' => 'nullable|array',
            'side_stones_colours' => 'nullable|array',
            'side_stones_clarities' => 'nullable|array',
            
            // Diamond/s - Centre Stone
            'centre_stone_weight' => 'nullable|numeric|min:0',
            'centre_stone_shape' => 'nullable|string|max:255',
            'centre_stone_colour' => 'nullable|string|max:255',
            'centre_stone_clarity' => 'nullable|string|max:255',
            
            // Colored Stones Information
            'coloured_stones_weight' => 'nullable|numeric|min:0',
            'coloured_stones_shape' => 'nullable|string|max:255',
            'coloured_stones_count' => 'nullable|integer|min:0',
            'coloured_stones_group' => 'nullable|string|max:255',
            'coloured_stones_species' => 'nullable|string|max:255',
            'coloured_stones_conclusion' => 'nullable|in:Natural,Synthetic',
            'coloured_stones_note' => 'nullable|string|max:1000',
            
            // Result
            'result' => 'nullable|string|max:255',
            'comments' => 'nullable|text',
            
            // Grader
            'grader_name' => 'nullable|string|max:255',
            'grader_date' => 'nullable|date',
            'grader_signature' => 'nullable|string|max:255',
            
            // Analytical Equipment
            'analytical_name' => 'nullable|string|max:255',
            'analytical_date' => 'nullable|date',
            'analytical_signature' => 'nullable|string|max:255',
            
            // Product Photography
            'image1_taken_by' => 'nullable|string|max:255',
            'image1_date' => 'nullable|date',
            'image1_signature' => 'nullable|string|max:255',
            'image2_taken_by' => 'nullable|string|max:255',
            'image2_date' => 'nullable|date',
            'image2_signature' => 'nullable|string|max:255',
            
            // Retaining Information
            'retaining_place' => 'nullable|string|max:255',
            'retaining_by' => 'nullable|string|max:255',
            'retaining_date' => 'nullable|date',
            'retaining_signature' => 'nullable|string|max:255',
            
            // Reporting Information
            'report_done' => 'nullable|string|max:255',
            'label_done' => 'nullable|string|max:255',
            'report_done_by' => 'nullable|string|max:255',
            'checked_by' => 'nullable|string|max:255',
            'report_number' => 'nullable|string|max:255',
            
            // Metal Analysis
            'metal_analysis' => 'nullable|array',
        ]);

        // إضافة البيانات الإضافية
        $validatedData['artifact_id'] = $artifact->id;
        $validatedData['evaluator_id'] = auth()->id();
        $validatedData['status'] = 'draft';
        $validatedData['is_final'] = true;

        // حفظ التقييم في الجدول الجديد
        try {
            $evaluation = \App\Models\JewelleryEvaluation::create($validatedData);
            \Log::info('Jewellery evaluation saved successfully:', $evaluation->toArray());
            return $evaluation;
        } catch (\Exception $e) {
            \Log::error('Error creating jewellery evaluation:', [
                'error' => $e->getMessage(),
                'data' => $validatedData
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            throw $e;
        }
    }

    public function showEvaluation(Artifact $artifact)
    {
        // تحديد نوع التقييم المناسب بناءً على نوع القطعة
        $evaluation = null;
        $evaluationPage = 'Dashboard/Artifacts/EvaluationView';

        if ($artifact->type === 'Colorless Diamonds') {
            $evaluation = $artifact->diamondEvaluations()
                ->with('evaluator')
                ->latest()
                ->first();
            $evaluationPage = 'Dashboard/Artifacts/DiamondEvaluationView';
        } elseif ($artifact->type === 'Jewellery') {
            // للمجوهرات، احمّل من جدول jewellery_evaluations
            $evaluation = $artifact->jewelleryEvaluations()
                ->with('evaluator')
                ->latest()
                ->first();
            $evaluationPage = 'Dashboard/Artifacts/EvaluationView';
        } else {
            // للأنواع الأخرى
            $evaluation = $artifact->evaluations()
                ->with('evaluator')
                ->where('is_final', true)
                ->latest()
                ->first();
        }

        if (!$evaluation) {
            return back()->withErrors(['error' => 'No evaluation found for this artifact.']);
        }

        return Inertia::render($evaluationPage, [
            'artifact' => $artifact->load('client'),
            'evaluation' => $evaluation,
        ]);
    }

    public function evaluatedArtifacts()
    {
        $artifacts = Artifact::with(['client', 'category', 'latestCertificate', 'testRequest'])
            ->whereIn('status', ['evaluated', 'certified'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return Inertia::render('Dashboard/Artifacts/EvaluatedIndex', [
            'artifacts' => $artifacts,
        ]);
    }

    public function customers(Request $request)
    {
        try {
            $qoyodService = new \App\Services\QoyodService();
            
            // Get page and per_page from request
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 50);
            
            // Fetch customers from Qoyod API
            $response = $qoyodService->getCustomers($page, $perPage);
            
            $customers = $response['data'] ?? [];
            $meta = $response['meta'] ?? [];
            
            return Inertia::render('Dashboard/Customers/Index', [
                'customers' => $customers,
                'meta' => $meta,
                'currentPage' => $page,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching customers from Qoyod', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            
            // Return empty list on error
            return Inertia::render('Dashboard/Customers/Index', [
                'customers' => [],
                'meta' => [
                    'current_page' => 1,
                    'per_page' => 50,
                    'total' => 0
                ],
                'currentPage' => 1,
                'error' => 'Failed to load customers from Qoyod. Please check your API configuration.'
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        }
    }

    public function showCustomer($customerId)
    {
        try {
            \Log::info('Showing customer details', ['customer_id' => $customerId]);

            // Get customer info from Qoyod
            $qoyodService = new \App\Services\QoyodService();
            $customer = $qoyodService->getCustomer($customerId);

            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get basic statistics for this customer
            $artifactsCount = \App\Models\Artifact::where('qoyod_customer_id', $customerId)->count();
            
            // Get recent quotes for this customer from Qoyod
            $quotesResponse = $qoyodService->getQuotes();
            $allQuotes = $quotesResponse['quotes'] ?? [];
            $customerQuotes = array_filter($allQuotes, function($quote) use ($customerId) {
                return isset($quote['contact_id']) && $quote['contact_id'] == $customerId;
            });

            \Log::info('Customer data prepared for display', [
                'customer_id' => $customerId,
                'customer_name' => $customer['name'] ?? $customer['display_name'] ?? 'Unknown',
                'artifacts_count' => $artifactsCount,
                'quotes_count' => count($customerQuotes),
                'customer_data' => $customer,
                'statistics_data' => [
                    'artifacts_count' => $artifactsCount,
                    'quotes_count' => count($customerQuotes),
                    'recent_quotes' => array_slice($customerQuotes, 0, 5)
                ]
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return Inertia::render('Dashboard/Customers/Show', [
                'customer' => $customer,
                'statistics' => [
                    'artifacts_count' => $artifactsCount,
                    'quotes_count' => count($customerQuotes),
                    'recent_quotes' => array_slice($customerQuotes, 0, 5) // Last 5 quotes
                ]
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error showing customer', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => 'An error occurred while loading the customer.']);
        }
    }

    public function storeCustomer(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'organization' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'secondary_email' => 'nullable|email|max:255',
                'phone_number' => 'nullable|string|max:255',
                'secondary_phone_number' => 'nullable|string|max:255',
                'website' => 'nullable|url|max:255',
                'currency' => 'nullable|string|max:10',
                'billing_address' => 'nullable|string|max:500',
                'billing_city' => 'nullable|string|max:255',
                'billing_state' => 'nullable|string|max:255',
                'billing_postal_code' => 'nullable|string|max:20',
                'billing_country' => 'nullable|string|max:255',
                'shipping_address' => 'nullable|string|max:500',
                'shipping_city' => 'nullable|string|max:255',
                'shipping_state' => 'nullable|string|max:255',
                'shipping_postal_code' => 'nullable|string|max:20',
                'shipping_country' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:15|min:15',
                'commercial_registration_number' => 'nullable|string|max:255',
                'tax_subject' => 'nullable|boolean',
                'pos_customer' => 'nullable|boolean',
                'government_entity_customer' => 'nullable|boolean',
                'status' => 'nullable|string|in:Active,Inactive',
                'credit_limit' => 'nullable|numeric|min:0',
                'pos' => 'nullable|boolean',
                'government_entity' => 'nullable|boolean',
                'allow_credit' => 'nullable|boolean',
                'notes' => 'nullable|string|max:1000',
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            // Convert boolean fields
            $validatedData['pos'] = $request->boolean('pos');
            $validatedData['government_entity'] = $request->boolean('government_entity');
            $validatedData['allow_credit'] = $request->boolean('allow_credit');
            $validatedData['tax_subject'] = $request->boolean('tax_subject');
            $validatedData['pos_customer'] = $request->boolean('pos_customer');
            $validatedData['government_entity_customer'] = $request->boolean('government_entity_customer');

            $qoyodService = new \App\Services\QoyodService();
            $result = $qoyodService->createCustomer($validatedData);

            if ($result) {
                return redirect()->route('dashboard.customers')
                    ->with('success', 'تم إنشاء العميل بنجاح في قيود!');
            } else {
                return back()->withErrors(['error' => 'فشل في إنشاء العميل في قيود. يرجى المحاولة مرة أخرى.']);
            }
        } catch (\Exception $e) {
            \Log::error('Error creating customer', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return back()->withErrors(['error' => 'An error occurred while creating the customer. Please try again.']);
        }
    }

    public function updateCustomer(Request $request, $customerId)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'organization' => 'nullable|string|max:255',
                'email' => 'nullable|email|max:255',
                'secondary_email' => 'nullable|email|max:255',
                'phone_number' => 'nullable|string|max:255',
                'secondary_phone_number' => 'nullable|string|max:255',
                'website' => 'nullable|url|max:255',
                'currency' => 'nullable|string|max:10',
                'billing_address' => 'nullable|string|max:500',
                'billing_city' => 'nullable|string|max:255',
                'billing_state' => 'nullable|string|max:255',
                'billing_postal_code' => 'nullable|string|max:20',
                'billing_country' => 'nullable|string|max:255',
                'shipping_address' => 'nullable|string|max:500',
                'shipping_city' => 'nullable|string|max:255',
                'shipping_state' => 'nullable|string|max:255',
                'shipping_postal_code' => 'nullable|string|max:20',
                'shipping_country' => 'nullable|string|max:255',
                'tax_number' => 'nullable|string|max:15|min:15',
                'commercial_registration_number' => 'nullable|string|max:255',
                'tax_subject' => 'nullable|boolean',
                'pos_customer' => 'nullable|boolean',
                'government_entity_customer' => 'nullable|boolean',
                'status' => 'nullable|string|in:Active,Inactive',
                'credit_limit' => 'nullable|numeric|min:0',
                'pos' => 'nullable|boolean',
                'government_entity' => 'nullable|boolean',
                'allow_credit' => 'nullable|boolean',
                'notes' => 'nullable|string|max:1000',
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            // Convert boolean fields
            $validatedData['pos'] = $request->boolean('pos');
            $validatedData['government_entity'] = $request->boolean('government_entity');
            $validatedData['allow_credit'] = $request->boolean('allow_credit');
            $validatedData['tax_subject'] = $request->boolean('tax_subject');
            $validatedData['pos_customer'] = $request->boolean('pos_customer');
            $validatedData['government_entity_customer'] = $request->boolean('government_entity_customer');

            $qoyodService = new \App\Services\QoyodService();
            $result = $qoyodService->updateCustomer($customerId, $validatedData);

            if ($result) {
                return redirect()->route('dashboard.customers')
                    ->with('success', 'Customer updated successfully in Qoyod!');
            } else {
                return back()->withErrors(['error' => 'Failed to update customer in Qoyod. Please try again.']);
            }
        } catch (\Exception $e) {
            \Log::error('Error updating customer', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return back()->withErrors(['error' => 'An error occurred while updating the customer. Please try again.']);
        }
    }

    public function deleteCustomer($customerId)
    {
        try {
            $qoyodService = new \App\Services\QoyodService();
            $result = $qoyodService->deleteCustomer($customerId);

            if ($result) {
                return redirect()->route('dashboard.customers')
                    ->with('success', 'Customer deleted successfully from Qoyod!');
            } else {
                return back()->withErrors(['error' => 'Failed to delete customer from Qoyod. Please try again.']);
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting customer', [
                'customer_id' => $customerId,
                'error' => $e->getMessage()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return back()->withErrors(['error' => 'An error occurred while deleting the customer. Please try again.']);
        }
    }

    public function storeCustomerArtifact(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'client_id' => 'required|integer',
                'test_request_id' => 'nullable|integer|exists:test_requests,id',
                'type' => 'required|string|max:100',
                'subtype' => 'nullable|string|max:100',
                'service' => 'nullable|string|max:100',
                'weight' => 'nullable|numeric',
                'weight_unit' => 'nullable|in:ct,gm',
                'delivery_type' => 'nullable|string|max:100',
                'notes' => 'nullable|string|max:1000',
                'quantity' => 'nullable|integer|min:1|max:100', // Optional quantity for creating multiple artifacts
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            \Log::info('Creating artifact for Qoyod customer', [
                'client_id' => $validatedData['client_id'],
                'test_request_id' => $validatedData['test_request_id'] ?? null,
                'type' => $validatedData['type']
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            // Get quantity, default to 1
            $quantity = intval($validatedData['quantity'] ?? 1);
            
            // Generate base artifact code
            $baseCode = \App\Models\Artifact::generateArtifactCode($validatedData['type']);

            // Calculate price automatically
            $price = null;
            if ($validatedData['type'] && $validatedData['service'] && $validatedData['weight']) {
                $weight = floatval($validatedData['weight']);
                $price = PricingService::calculatePrice(
                    $validatedData['type'],
                    $validatedData['service'],
                    $weight
                );
            }

            // Create multiple artifacts if quantity > 1
            $createdArtifacts = [];
            for ($i = 1; $i <= $quantity; $i++) {
                // Generate sub-code if quantity > 1
                $artifactCode = $quantity > 1
                    ? $baseCode . '-' . $i
                    : $baseCode;
                
                // Create artifact with Qoyod customer ID
                $artifact = \App\Models\Artifact::create([
                'client_id' => null, // Qoyod customers are not in local clients table
                'qoyod_customer_id' => $validatedData['client_id'], // Store Qoyod customer ID
                'test_request_id' => $validatedData['test_request_id'] ?? null, // Link to test request if provided
                'artifact_code' => $artifactCode,
                'type' => $validatedData['type'],
                'subtype' => $validatedData['subtype'] ?? null,
                'service' => $validatedData['service'] ?? null,
                'weight' => $validatedData['weight'] ? (string)$validatedData['weight'] : null,
                'weight_unit' => $validatedData['weight_unit'] ?? null,
                'price' => $price,
                'delivery_type' => $validatedData['delivery_type'] ?? null,
                'notes' => $validatedData['notes'] ?? null,
                'status' => 'pending',
                'title' => ['en' => '', 'ar' => ''],
                'description' => ['en' => '', 'ar' => ''],
                'category_id' => null,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
                
                $createdArtifacts[] = $artifact;
            }

            \Log::info('Artifacts created successfully for Qoyod customer', [
                'created_count' => count($createdArtifacts),
                'quantity' => $quantity,
                'base_code' => $baseCode,
                'artifacts' => collect($createdArtifacts)->map(fn($a) => [
                    'id' => $a->id,
                    'code' => $a->artifact_code
                ])->toArray()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            // Prepare success message
            $message = $quantity > 1 
                ? "Successfully created {$quantity} artifacts with codes from {$baseCode}-1 to {$baseCode}-{$quantity}"
                : 'Artifact added successfully for customer!';

            // Redirect back to test request if it was created from there, otherwise to artifacts page
            if (!empty($validatedData['test_request_id'])) {
                return redirect()->route('dashboard.test-requests.show', ['testRequest' => $validatedData['test_request_id']])
                    ->with('success', $quantity > 1 ? "Added {$quantity} artifacts to test request!" : 'Artifact added successfully to test request!');
            }

            return redirect()->route('dashboard.customers.artifacts.index', ['customer' => $validatedData['client_id']])
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Error creating artifact for Qoyod customer', [
                'error' => $e->getMessage(),
                'data' => $request->all()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return back()->withErrors(['error' => 'An error occurred while adding the artifact. Please try again.']);
        }
    }

    public function showAddArtifact($customerId)
    {
        try {
            \Log::info('Showing add artifact form', ['customer_id' => $customerId]);
            
            // Get customer info from Qoyod
            $qoyodService = new \App\Services\QoyodService();
            $customer = $qoyodService->getCustomer($customerId);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            return Inertia::render('Dashboard/Customers/AddArtifact', [
                'customer' => $customer,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing add artifact form', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => 'An error occurred while loading the form.']);
        }
    }

    public function storeArtifactForCustomer($customerId, Request $request)
    {
        try {
            \Log::info('Storing artifact for customer', ['customer_id' => $customerId]);
            
            $validatedData = $request->validate([
                'type' => 'required|string|max:255',
                'subtype' => 'nullable|string|max:255',
                'service' => 'required|string|max:255',
                'weight' => 'nullable|numeric|min:0',
                'weight_unit' => 'nullable|string|in:ct,g,kg,mg',
                'delivery_type' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:1000',
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            // Generate artifact code
            $artifactCode = 'GR' . str_pad(rand(1, 9999999999), 9, '0', STR_PAD_LEFT);
            
            // Create artifact with Qoyod customer ID
            $artifact = \App\Models\Artifact::create([
                'client_id' => null,
                'qoyod_customer_id' => $customerId,
                'artifact_code' => $artifactCode,
                'type' => $validatedData['type'],
                'subtype' => $validatedData['subtype'] ?? null,
                'service' => $validatedData['service'],
                'weight' => (string)$validatedData['weight'],
                'weight_unit' => $validatedData['weight_unit'],
                'delivery_type' => $validatedData['delivery_type'],
                'notes' => $validatedData['notes'],
                'status' => 'pending',
                'title' => ['en' => '', 'ar' => ''],
                'description' => ['en' => '', 'ar' => ''],
                'category_id' => null,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            \Log::info('Artifact created successfully for customer', [
                'artifact_id' => $artifact->id,
                'artifact_code' => $artifactCode,
                'customer_id' => $customerId
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->route('dashboard.customers.artifacts.index', ['customer' => $customerId])
                ->with('success', 'Artifact added successfully!');

        } catch (\Exception $e) {
            \Log::error('Error creating artifact for customer', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return back()->withErrors(['error' => 'An error occurred while adding the artifact. Please try again.']);
        }
    }

    public function customerArtifacts($customerId)
    {
        try {
            \Log::info('Fetching customer artifacts', ['customer_id' => $customerId]);
            
            // Get customer info from Qoyod
            $qoyodService = new \App\Services\QoyodService();
            $customer = $qoyodService->getCustomer($customerId);
            
            \Log::info('Customer data from Qoyod', ['customer' => $customer]);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get artifacts for this Qoyod customer
            $artifacts = \App\Models\Artifact::where('qoyod_customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->get();

            \Log::info('Artifacts found', ['count' => $artifacts->count(), 'artifacts' => $artifacts->toArray()]);

            return Inertia::render('Dashboard/Customers/Artifacts', [
                'customer' => $customer,
                'artifacts' => $artifacts,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching customer artifacts', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => 'An error occurred while fetching customer artifacts.']);
        }
    }

    /**
     * Show create quote form for customer
     */
    public function showCreateQuote($customerId)
    {
        try {
            \Log::info('Showing create quote form', ['customer_id' => $customerId]);
            
            // Get customer info from Qoyod
            $qoyodService = new \App\Services\QoyodService();
            $customer = $qoyodService->getCustomer($customerId);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get artifacts for this customer
            $artifacts = \App\Models\Artifact::where('qoyod_customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get products from Qoyod
            $productsResponse = $qoyodService->getProducts();
            $products = $productsResponse['products'] ?? [];
            
            // Get locations from Qoyod
            $locations = $qoyodService->getLocations();

            \Log::info('Data prepared for quote form', [
                'customer_id' => $customerId,
                'customer_name' => $customer['name'] ?? $customer['display_name'] ?? 'Unknown',
                'artifacts_count' => $artifacts->count(),
                'products_count' => count($products)
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return Inertia::render('Dashboard/Customers/CreateQuote', [
                'customer' => $customer,
                'artifacts' => $artifacts,
                'products' => $products,
                'locations' => $locations,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing create quote form', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->route('dashboard.customers.artifacts.index', $customerId)
                ->withErrors(['error' => 'An error occurred while loading the quote form.']);
        }
    }

    /**
     * Store/create quote for customer
     */
    public function storeQuote(Request $request, $customerId)
    {
        try {
            $validatedData = $request->validate([
                'quotation_number' => 'required|string|max:100',
                'issue_date' => 'required|date|date_format:Y-m-d',
                'expiry_date' => 'required|date|date_format:Y-m-d|after:issue_date',
                'status' => 'required|string|in:Draft,Approved',
                'terms_conditions' => 'nullable|string|max:1000',
                'notes' => 'nullable|string|max:1000',
                'inventory_id' => 'required|integer',
                'location_id' => 'required|integer',
                'line_items' => 'required|array|min:1',
                'line_items.*.product_id' => 'required|integer',
                'line_items.*.description' => 'nullable|string|max:500',
                'line_items.*.quantity' => 'required|numeric|min:0.01',
                'line_items.*.unit_price' => 'required|numeric|min:0',
                'line_items.*.unit_type' => 'nullable|string|max:50',
                'line_items.*.discount' => 'nullable|numeric|min:0',
                'line_items.*.discount_type' => 'nullable|string|in:percentage,amount',
                'line_items.*.tax_percent' => 'nullable|numeric|min:0|max:100',
                'line_items.*.is_inclusive' => 'nullable|boolean',
                'custom_fields' => 'nullable|array',
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            \Log::info('Creating quote in Qoyod', [
                'customer_id' => $customerId,
                'quotation_number' => $validatedData['quotation_number'],
                'line_items_count' => count($validatedData['line_items'])
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            $qoyodService = new \App\Services\QoyodService();
            
            // Prepare quote data for Qoyod API
            $quoteData = [
                'quote' => [
                    'contact_id' => $customerId,
                    'quotation_number' => $validatedData['quotation_number'],
                    'issue_date' => $validatedData['issue_date'],
                    'expiry_date' => $validatedData['expiry_date'],
                    'status' => $validatedData['status'],
                    'terms_conditions' => $validatedData['terms_conditions'] ?? null,
                    'notes' => $validatedData['notes'] ?? null,
                    'inventory_id' => $validatedData['inventory_id'],
                    'line_items' => $validatedData['line_items'],
                ]
            ];

            // Add custom_fields if provided
            if (isset($validatedData['custom_fields']) && !empty($validatedData['custom_fields'])) {
                $quoteData['quote']['custom_fields'] = $validatedData['custom_fields'];
            }

            // Create quote via Qoyod API
            $response = $qoyodService->createQuote($quoteData);

            if (isset($response['quote']) && isset($response['quote']['id'])) {
                \Log::info('Quote created successfully', [
                    'customer_id' => $customerId,
                    'quote_id' => $response['quote']['id'],
                    'quotation_number' => $validatedData['quotation_number'],
                    'total_amount' => $response['quote']['total_amount'] ?? 'N/A'
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                return redirect()->route('dashboard.customers.quotes', $customerId)
                    ->with('success', 'Quote created successfully! Quote ID: ' . $response['quote']['id']);
            } else {
                \Log::error('Failed to create quote', [
                    'customer_id' => $customerId,
                    'response' => $response
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                return redirect()->back()
                    ->withErrors(['error' => 'Failed to create quote: ' . ($response['message'] ?? 'Unknown error')])
                    ->withInput();
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error when creating quote', [
                'customer_id' => $customerId,
                'errors' => $e->errors()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating quote', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'An error occurred while creating the quote. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Update artifact
     */
    public function updateArtifact(Request $request, Artifact $artifact)
    {
        try {
            $validatedData = $request->validate([
                'type' => 'required|string|max:100',
                'subtype' => 'nullable|string|max:100',
                'service' => 'nullable|string|max:100',
                'weight' => 'nullable|numeric',
                'weight_unit' => 'nullable|in:ct,gm',
                'delivery_type' => 'nullable|string|max:100',
                'price' => 'nullable|numeric',
                'status' => 'required|string|in:pending,under_evaluation,evaluated,certified,rejected',
                'notes' => 'nullable|string|max:1000',
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            \Log::info('Updating artifact', [
                'artifact_id' => $artifact->id,
                'artifact_code' => $artifact->artifact_code,
                'data' => $validatedData
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            // Update artifact
            $artifact->update([
                'type' => $validatedData['type'],
                'subtype' => $validatedData['subtype'] ?? null,
                'service' => $validatedData['service'] ?? null,
                'weight' => $validatedData['weight'] ? (string)$validatedData['weight'] : null,
                'weight_unit' => $validatedData['weight_unit'] ?? null,
                'delivery_type' => $validatedData['delivery_type'] ?? null,
                'price' => $validatedData['price'] ?? null,
                'status' => $validatedData['status'],
                'notes' => $validatedData['notes'] ?? null,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            \Log::info('Artifact updated successfully', [
                'artifact_id' => $artifact->id,
                'artifact_code' => $artifact->artifact_code
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->with('success', 'Artifact updated successfully');

        } catch (\Exception $e) {
            \Log::error('Error updating artifact', [
                'artifact_id' => $artifact->id,
                'error' => $e->getMessage(),
                'data' => $request->all()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'An error occurred while updating the artifact. Please try again.']);
        }
    }

    /**
     * Delete artifact
     */
    public function deleteArtifact(Artifact $artifact)
    {
        try {
            \Log::info('Deleting artifact', [
                'artifact_id' => $artifact->id,
                'artifact_code' => $artifact->artifact_code
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            $artifact->delete();

            \Log::info('Artifact deleted successfully', [
                'artifact_id' => $artifact->id,
                'artifact_code' => $artifact->artifact_code
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->with('success', 'Artifact deleted successfully');

        } catch (\Exception $e) {
            \Log::error('Error deleting artifact', [
                'artifact_id' => $artifact->id,
                'error' => $e->getMessage()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'An error occurred while deleting the artifact. Please try again.']);
        }
    }

    /**
     * Edit artifact evaluation
     */
    public function editEvaluation(Artifact $artifact)
    {
        // تحديد نوع التقييم المناسب بناءً على نوع القطعة
        $evaluation = null;
        $evaluationPage = 'Dashboard/Artifacts/Evaluate';

        if ($artifact->type === 'Colorless Diamonds') {
            $evaluation = $artifact->diamondEvaluations()
                ->latest()
                ->first();
            $evaluationPage = 'Dashboard/Artifacts/EvaluateDiamond';
        } elseif ($artifact->type === 'Jewellery') {
            // للمجوهرات، احمّل من جدول jewellery_evaluations
            $evaluation = $artifact->jewelleryEvaluations()
                ->latest()
                ->first();
            $evaluationPage = 'Dashboard/Artifacts/EvaluateJewellery';
        } else if (in_array($artifact->type, ['Colored Gemstones'])) {
            $evaluation = $artifact->evaluations()
                ->latest()
                ->first();
            $evaluationPage = 'Dashboard/Artifacts/Evaluate';
        } else {
            // للأنواع الأخرى
            $evaluation = $artifact->evaluations()
                ->latest()
                ->first();
        }

        if (!$evaluation) {
            return back()->withErrors(['error' => 'No evaluation found for this artifact to edit.']);
        }

        // Log the evaluation data for debugging
        \Log::info('Edit evaluation data:', [
            'artifact_id' => $artifact->id,
            'artifact_type' => $artifact->type,
            'evaluation_id' => $evaluation->id,
            'test_date' => $evaluation->test_date,
            'test_date_type' => gettype($evaluation->test_date),
            'grader_date' => $evaluation->grader_date,
            'grader_date_type' => gettype($evaluation->grader_date),
        ]);

        return Inertia::render($evaluationPage, [
            'artifact' => $artifact->load('client'),
            'existingEvaluation' => $evaluation,
            'isEditing' => true,
        ]);
    }

    /**
     * Update artifact evaluation
     */
    public function updateEvaluation(Request $request, Artifact $artifact)
    {
        // Find the existing evaluation based on artifact type
        $evaluation = null;
        if ($artifact->type === 'Jewellery') {
            $evaluation = $artifact->jewelleryEvaluations()->latest()->first();
        } else {
            $evaluation = $artifact->evaluations()->latest()->first();
        }
        
        if (!$evaluation) {
            return back()->withErrors(['error' => 'No evaluation found to update.']);
        }

        // Validate the request based on artifact type
        if ($artifact->type === 'Jewellery') {
            $validatedData = $this->validateJewelleryEvaluation($request);
        } else {
            $validatedData = $this->validateGeneralEvaluation($request);
        }

        // Update the evaluation
        $evaluation->update($validatedData);

        // Log the update
        \Log::info('Evaluation updated successfully:', [
            'artifact_code' => $artifact->artifact_code,
            'evaluation_id' => $evaluation->id,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('dashboard.artifacts.evaluation.show', $artifact)
            ->with('success', 'Evaluation updated successfully!');
    }

    /**
     * Edit diamond evaluation
     */
    public function editDiamondEvaluation(DiamondEvaluation $evaluation)
    {
        // Log the evaluation data for debugging
        \Log::info('Edit diamond evaluation data:', [
            'artifact_id' => $evaluation->artifact->id,
            'artifact_type' => $evaluation->artifact->type,
            'evaluation_id' => $evaluation->id,
            'test_date' => $evaluation->test_date,
            'test_date_type' => gettype($evaluation->test_date),
            'grader_date' => $evaluation->grader_date,
            'grader_date_type' => gettype($evaluation->grader_date),
        ]);

        return Inertia::render('Dashboard/Artifacts/EvaluateDiamond', [
            'artifact' => $evaluation->artifact->load('client'),
            'existingEvaluation' => $evaluation,
            'isEditing' => true,
        ]);
    }

    /**
     * Update diamond evaluation
     */
    public function updateDiamondEvaluation(Request $request, DiamondEvaluation $evaluation)
    {
        $validatedData = $this->validateDiamondEvaluation($request);
        
        // Update the evaluation
        $evaluation->update($validatedData);

        // Log the update
        \Log::info('Diamond evaluation updated successfully:', [
            'artifact_code' => $evaluation->artifact->artifact_code,
            'evaluation_id' => $evaluation->id,
            'updated_by' => auth()->id()
        ]);

        return redirect()->route('dashboard.artifacts.evaluation.show', $evaluation->artifact)
            ->with('success', 'Diamond evaluation updated successfully!');
    }

    /**
     * Validate diamond evaluation request
     */
    private function validateDiamondEvaluation(Request $request)
    {
        return $request->validate([
            // Job Information
            'test_date' => 'required|date',
            'test_location' => 'nullable|string|max:255',
            'item_product_id' => 'nullable|string|max:255',
            'receiving_record' => 'nullable|string|max:255',
            'prepared_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            
            // Stone Information
            'weight' => 'nullable|numeric|min:0',
            'shape' => 'nullable|string|max:255',
            'laser_inscription' => 'nullable|string|max:255',
            
            // Lab-Grown Diamond Screen
            'hpht_screen' => 'nullable|string|max:255',
            'cvd_check' => 'nullable|string|max:255',
            
            // Proportion Grade
            'diameter' => 'nullable|numeric|min:0',
            'total_depth' => 'nullable|numeric|min:0',
            'table' => 'nullable|numeric|min:0',
            'star_facet' => 'nullable|numeric|min:0',
            'crown_angle' => 'nullable|numeric|min:0',
            'crown_height' => 'nullable|numeric|min:0',
            'girdle_thickness_min' => 'nullable|numeric|min:0',
            'girdle_thickness_max' => 'nullable|numeric|min:0',
            'pavilion_depth' => 'nullable|numeric|min:0',
            'pavilion_angle' => 'nullable|numeric|min:0',
            'lower_girdle' => 'nullable|numeric|min:0',
            'culet_size' => 'nullable|string|max:255',
            'girdle_condition' => 'nullable|string|max:255',
            'culet_condition' => 'nullable|string|max:255',
            
            // Grade Information
            'polish' => 'nullable|string|max:255',
            'symmetry' => 'nullable|string|max:255',
            'cut' => 'nullable|string|max:255',
            
            // Visual Inspection
            'clarity' => 'nullable|string|max:255',
            'colour' => 'nullable|string|max:255',
            
            // Fluorescence
            'fluorescence_strength' => 'nullable|string|max:255',
            'fluorescence_colour' => 'nullable|string|max:255',
            
            // Result
            'result' => 'nullable|string|max:255',
            'stone_type' => 'nullable|string|max:255',
            
            // Comments
            'comments' => 'nullable|string',
            
            // Grader
            'grader_name' => 'nullable|string|max:255',
            'grader_date' => 'nullable|date',
            'grader_signature' => 'nullable|string|max:255',
            
            // Analytical Equipment
            'analytical_name' => 'nullable|string|max:255',
            'analytical_date' => 'nullable|date',
            'analytical_signature' => 'nullable|string|max:255',
            
            // Retaining Information
            'retaining_place' => 'nullable|string|max:255',
            'retaining_by' => 'nullable|string|max:255',
            'retaining_date' => 'nullable|date',
            'retaining_signature' => 'nullable|string|max:255',
            
            // Reporting Information
            'report_done' => 'nullable|string|max:255',
            'label_done' => 'nullable|string|max:255',
            'report_done_by' => 'nullable|string|max:255',
            'report_date' => 'nullable|date',
            'checked_by' => 'nullable|string|max:255',
            'report_number' => 'nullable|string|max:255',
            
            // Status
            'status' => 'nullable|string|max:255',
        ]);
    }

    /**
     * Validate general evaluation request
     */
    private function validateGeneralEvaluation(Request $request)
    {
        return $request->validate([
            // Job Information
            'test_date' => 'required|date',
            'test_location' => 'nullable|string|max:255',
            'item_id' => 'nullable|string|max:255',
            
            // Stone Information
            'weight' => 'nullable|numeric|min:0',
            'colour' => 'nullable|string|max:255',
            'transparency' => 'nullable|string|max:255',
            'lustre' => 'nullable|string|max:255',
            'tone' => 'nullable|string|max:255',
            'phenomena' => 'nullable|string|max:255',
            'saturation' => 'nullable|string|max:255',
            'measurements' => 'nullable|numeric|min:0',
            'shape_cut' => 'nullable|string|max:255',
            'pleochroism' => 'nullable|string|max:255',
            'optic_character' => 'nullable|string|max:255',
            'refractive_index' => 'nullable|array',
            'ri_result' => 'nullable|string|max:255',
            'inclusion' => 'nullable|string',
            'weight_air' => 'nullable|numeric|min:0',
            'weight_water' => 'nullable|numeric|min:0',
            'sg_result' => 'nullable|string|max:255',
            'fluorescence_long' => 'nullable|string|max:255',
            'fluorescence_short' => 'nullable|string|max:255',
            'result' => 'nullable|string|max:255',
            'variety' => 'nullable|string|max:255',
            'species_group' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
            
            // Grader
            'grader_name' => 'nullable|string|max:255',
            'grader_date' => 'nullable|date',
            'analytical_interpretation' => 'nullable|string',
            'image1' => 'nullable|string',
            'image2' => 'nullable|string',
            
            // Retaining Information
            'retaining_place' => 'nullable|string|max:255',
            'retained_by' => 'nullable|string|max:255',
            'retained_date' => 'nullable|date',
            'report_done' => 'nullable|boolean',
            'label_done' => 'nullable|boolean',
            'checked_by' => 'nullable|string|max:255',
            'checked_date' => 'nullable|date',
        ]);
    }

    /**
     * Validate jewellery evaluation request
     */
    private function validateJewelleryEvaluation(Request $request)
    {
        return $request->validate([
            // Job Information
            'test_date' => 'required|date',
            'test_location' => 'nullable|string|max:255',
            'item_product_id' => 'nullable|string|max:255',
            'receiving_record' => 'nullable|string|max:255',
            'prepared_by' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            
            // Product Information
            'product_number' => 'nullable|string|max:255',
            'product_type' => 'nullable|string|max:255',
            'metal_type' => 'nullable|string|max:255',
            'stamp' => 'nullable|string|max:255',
            'total_weight' => 'nullable|numeric|min:0',
            
            // Diamond/s
            'side_stones_weight_type' => 'nullable|string|max:255',
            'side_stones_weight' => 'nullable|numeric|min:0',
            'side_stones_pieces' => 'nullable|integer|min:0',
            'side_stones_shapes' => 'nullable|array',
            'side_stones_colours' => 'nullable|array',
            'side_stones_clarities' => 'nullable|array',
            'centre_stone_weight' => 'nullable|numeric|min:0',
            'centre_stone_shape' => 'nullable|string|max:255',
            'centre_stone_colour' => 'nullable|string|max:255',
            'centre_stone_clarity' => 'nullable|string|max:255',
            
            // Coloured Gemstones
            'coloured_stones_weight' => 'nullable|numeric|min:0',
            'coloured_stones_shape' => 'nullable|string|max:255',
            'coloured_stones_count' => 'nullable|integer|min:0',
            'coloured_stones_group' => 'nullable|string|max:255',
            'coloured_stones_species' => 'nullable|string|max:255',
            'coloured_stones_conclusion' => 'nullable|string|max:255',
            'coloured_stones_note' => 'nullable|string',
            
            // Result
            'result' => 'nullable|string|max:255',
            'comments' => 'nullable|string',
            
            // Grader
            'grader_name' => 'nullable|string|max:255',
            'grader_date' => 'nullable|date',
            'grader_signature' => 'nullable|string|max:255',
            
            // Analytical Equipment
            'analytical_name' => 'nullable|string|max:255',
            'analytical_date' => 'nullable|date',
            'analytical_signature' => 'nullable|string|max:255',
            
            // Images
            'image1_taken_by' => 'nullable|string|max:255',
            'image1_date' => 'nullable|date',
            'image1_signature' => 'nullable|string|max:255',
            'image2_taken_by' => 'nullable|string|max:255',
            'image2_date' => 'nullable|date',
            'image2_signature' => 'nullable|string|max:255',
            
            // Retaining Information
            'retaining_place' => 'nullable|string|max:255',
            'retaining_by' => 'nullable|string|max:255',
            'retaining_date' => 'nullable|date',
            'retaining_signature' => 'nullable|string|max:255',
            
            // Reporting Information
            'report_done' => 'nullable|string|max:255',
            'label_done' => 'nullable|string|max:255',
            'report_done_by' => 'nullable|string|max:255',
            'checked_by' => 'nullable|string|max:255',
            'report_number' => 'nullable|string|max:255',
            
            // Metal Analysis
            'metal_analysis' => 'nullable|array',
        ]);
    }

    /**
     * Test endpoint for debugging evaluation issues on server
     */
    public function testEvaluation(Request $request)
    {
        \Log::info('testEvaluation endpoint called');
        
        try {
            // Test 1: Check if we can create a basic evaluation
            $testData = [
                'artifact_id' => 1,
                'evaluator_id' => 1,
                'test_date' => '2025-08-06',
                'test_location' => 'test',
                'weight' => 12,
                'colour' => 'black',
                'transparency' => 'Translucent',
                'lustre' => 'Resinous',
                'tone' => 'Medium',
                'phenomena' => '21',
                'saturation' => 'Weak',
                'is_final' => true,
                'evaluation_date' => now(),
                'detailed_notes' => ['en' => '', 'ar' => ''],
                'supporting_documents' => []
            ];

            \Log::info('Test data:', $testData);

            $evaluation = ArtifactEvaluation::create($testData);
            \Log::info('Test evaluation created successfully:', $evaluation->toArray());

            // Test 2: Check database connection and table structure
            $columns = \DB::select('DESCRIBE artifact_evaluations');
            $columnNames = array_column($columns, 'Field');
            
            \Log::info('Database columns:', $columnNames);

            // Test 3: Check if artifact exists
            $artifact = Artifact::find(1);
            if ($artifact) {
                \Log::info('Artifact found:', $artifact->toArray());
            } else {
                \Log::warning('Artifact with ID 1 not found');
            }

            return response()->json([
                'success' => true,
                'test_evaluation_id' => $evaluation->id,
                'columns' => $columnNames,
                'artifact_exists' => $artifact ? true : false,
                'message' => 'All tests passed'
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

        } catch (\Exception $e) {
            \Log::error('Test evaluation failed:', [
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => $e->getCode(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Show a specific quote
     */
    public function showQuote($quoteId)
    {
        try {
            \Log::info('Showing quote', ['quote_id' => $quoteId]);

            // Get quote from Qoyod
            $qoyodService = new \App\Services\QoyodService();
            
            // Try to get quote by local ID first, then by qoyod_id if needed
            $quote = $qoyodService->getQuote($quoteId);
            
            // If not found by local ID, try to find it in all quotes
            if (!$quote) {
                \Log::info('Quote not found by local ID, searching in all quotes', ['quote_id' => $quoteId]);
                $allQuotes = $qoyodService->getQuotes();
                
                if (isset($allQuotes['quotes'])) {
                    // Look for quote with matching reference or ID
                    foreach ($allQuotes['quotes'] as $qoyodQuote) {
                        if ($qoyodQuote['id'] == $quoteId || 
                            (isset($qoyodQuote['reference']) && $qoyodQuote['reference'] == $quoteId)) {
                            $quote = $qoyodQuote;
                            break;
                        }
                    }
                }
            }

            if (!$quote) {
                \Log::warning('Quote not found in Qoyod', ['quote_id' => $quoteId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Quote not found in Qoyod.']);
            }

            // Get customer info if available
            $customer = null;
            if (isset($quote['contact_id'])) {
                $customer = $qoyodService->getCustomer($quote['contact_id']);
            }

            // Get products info for quote line items
            $products = [];
            if (isset($quote['line_items']) && is_array($quote['line_items'])) {
                // Get all unique product IDs from line items
                $productIds = array_unique(array_column($quote['line_items'], 'product_id'));
                
                // Fetch products from Qoyod
                $productsResponse = $qoyodService->getProducts();
                $allProducts = $productsResponse['products'] ?? [];
                
                // Filter to only products used in this quote
                $products = array_filter($allProducts, function($product) use ($productIds) {
                    return in_array($product['id'], $productIds);
                });
            }

            \Log::info('Quote data prepared', [
                'quote_id' => $quoteId,
                'customer_name' => $customer['name'] ?? $customer['display_name'] ?? 'Unknown',
                'total_amount' => $quote['total_amount'] ?? 'N/A',
                'status' => $quote['status'] ?? 'Unknown',
                'line_items_count' => count($quote['line_items'] ?? []),
                'products_count' => count($products)
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return Inertia::render('Dashboard/Quotes/ShowQuote', [
                'quote' => $quote,
                'customer' => $customer,
                'products' => array_values($products), // Re-index array
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing quote', [
                'quote_id' => $quoteId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->route('dashboard.customers')
                ->withErrors(['error' => 'An error occurred while loading the quote.']);
        }
    }

    /**
     * List customer quotes
     */
    public function listCustomerQuotes($customerId)
    {
        try {
            \Log::info('Showing customer quotes', ['customer_id' => $customerId]);

            // Get customer info from Qoyod
            $qoyodService = new \App\Services\QoyodService();
            $customer = $qoyodService->getCustomer($customerId);

            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get all quotes from Qoyod (we'll filter by customer_id in the frontend)
            $quotesResponse = $qoyodService->getQuotes();
            $allQuotes = $quotesResponse['quotes'] ?? [];
            
            // Filter quotes for this customer
            $customerQuotes = array_filter($allQuotes, function($quote) use ($customerId) {
                return isset($quote['contact_id']) && $quote['contact_id'] == $customerId;
            });

            \Log::info('Customer quotes data prepared', [
                'customer_id' => $customerId,
                'customer_name' => $customer['name'] ?? $customer['display_name'] ?? 'Unknown',
                'quotes_count' => count($customerQuotes)
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return Inertia::render('Dashboard/Customers/CustomerQuotes', [
                'customer' => $customer,
                'quotes' => array_values($customerQuotes), // Re-index array
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing customer quotes', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->route('dashboard.customers.artifacts.index', $customerId)
                ->withErrors(['error' => 'An error occurred while loading quotes.']);
        }
    }


    /**
     * Generate local PDF for quote (fallback method)
     */
    private function generateLocalQuotePDF($quote)
    {
        try {
            // Get customer info
            $qoyodService = new \App\Services\QoyodService();
            $customer = null;
            if (isset($quote['contact_id'])) {
                $customer = $qoyodService->getCustomer($quote['contact_id']);
            }

            // Generate base64 encoded logo for PDF
            $logoPath = public_path('images/idg_logo.jpg');
            $logoDataUri = '';
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $imageInfo = getimagesize($logoPath);
                $mimeType = $imageInfo['mime'];
                $logoDataUri = "data:$mimeType;base64,$logoData";
            }

            // Generate PDF using professional template
            try {
                // Get products info for PDF generation
                $products = [];
                if (isset($quote['line_items']) && is_array($quote['line_items'])) {
                    $productIds = array_unique(array_column($quote['line_items'], 'product_id'));
                    $productsResponse = $qoyodService->getProducts();
                    $allProducts = $productsResponse['products'] ?? [];
                    
                    $productsFiltered = array_filter($allProducts, function($product) use ($productIds) {
                        return in_array($product['id'], $productIds);
                    });
                    
                    $productsLookup = [];
                    foreach ($productsFiltered as $product) {
                        $productsLookup[$product['id']] = $product;
                    }
                    $products = $productsLookup;
                }

                $html = view('quotes.professional-pdf', [
                    'quote' => $quote,
                    'customer' => $customer,
                    'products' => $products,
                    'logoDataUri' => $logoDataUri,
                ])->render();
            } catch (\Exception $viewError) {
                \Log::warning('Fallback to simplified PDF template', [
                    'error' => $viewError->getMessage()
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
                // Use simplified template as fallback
                $html = view('quotes.pdf-simple', [
                    'quote' => $quote,
                    'customer' => $customer,
                    'logoDataUri' => $logoDataUri,
                ])->render();
            }

            // Use dompdf to generate PDF
            $dompdf = new \Dompdf\Dompdf([
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
                'defaultMediaType' => 'screen',
                'defaultPaperSize' => 'a4',
                'defaultPaperOrientation' => 'portrait',
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            
            \Log::info('Dompdf configuration', [
                'html_length' => strlen($html),
                'quote_id' => $quote['id'] ?? 'unknown'
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            
            $dompdf->loadHtml($html, 'UTF-8');
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $filename = 'quote-' . ($quote['reference'] ?? $quote['id'] ?? 'unknown') . '.pdf';

            $output = $dompdf->output();
            
            \Log::info('Local PDF generation completed', [
                'quote_id' => $quote['id'] ?? 'unknown',
                'filename' => $filename,
                'output_size' => strlen($output)
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
            
            if (empty($output)) {
                \Log::error('Generated PDF output is empty', [
                    'quote_id' => $quote['id'] ?? 'unknown',
                    'html_length' => strlen($html)
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
                
                // Log the HTML content for debugging
                \Log::debug('HTML content for debugging', ['html' => $html]);
                throw new \Exception('Generated PDF is empty - check HTML content');
            }

            return response($output, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
                ->header('Content-Length', strlen($output));

        } catch (\Exception $e) {
            \Log::error('Error generating local PDF', [
                'quote_id' => $quote['id'] ?? 'unknown',
                'error' => $e->getMessage()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'Failed to generate PDF. Please try again later.']);
        }
    }

    /**
     * Generate basic PDF HTML as fallback
     */
    private function generateBasicPDFHtml($quote, $customer)
    {
        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Quote ' . ($quote['id'] ?? 'N/A') . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #007bff; }
        .title { font-size: 24px; color: #007bff; font-weight: bold; }
        .section { margin-bottom: 20px; }
        .section-title { font-size: 18px; color: #007bff; font-weight: bold; margin-bottom: 10px; border-bottom: 1px solid #ddd; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        th { background-color: #007bff; color: white; font-weight: bold; }
        .total { background-color: #f8f9fa; padding: 15px; margin-top: 20px; border-radius: 5px; }
        .total-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .grand-total { background-color: #007bff; color: white; padding: 10px; border-radius: 5px; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">QUOTE</div>
        <div>' . ($quote['reference'] ?? '#' . ($quote['id'] ?? '')) . '</div>
    </div>
    
    <div class="section">
        <div class="section-title">Quote Details</div>
        <p><strong>Quote Number:</strong> ' . ($quote['reference'] ?? '#') . '</p>
        <p><strong>Status:</strong> ' . ($quote['status'] ?? 'Draft') . '</p>
        <p><strong>Issue Date:</strong> ' . ($quote['issue_date'] ?? 'N/A') . '</p>
        <p><strong>Expiry Date:</strong> ' . ($quote['expiry_date'] ?? 'N/A') . '</p>
    </div>';
        
        if ($customer) {
            $html .= '
    <div class="section">
        <div class="section-title">Customer Information</div>
        <p><strong>Customer Name:</strong> ' . ($customer['display_name'] ?? $customer['name'] ?? 'N/A') . '</p>';
            if (isset($customer['email'])) {
                $html .= '<p><strong>Email:</strong> ' . $customer['email'] . '</p>';
            }
            if (isset($customer['phone'])) {
                $html .= '<p><strong>Phone:</strong> ' . $customer['phone'] . '</p>';
            }
            $html .= '</div>';
        }
        
        if (isset($quote['line_items']) && count($quote['line_items']) > 0) {
            $html .= '
    <div class="section">
        <div class="section-title">Quote Items</div>
        <table>
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Description</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>';
            
            $subtotal = 0;
            foreach ($quote['line_items'] as $index => $item) {
                $itemTotal = ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0);
                $subtotal += $itemTotal;
                
                $html .= '
                <tr>
                    <td>' . ($index + 1) . '</td>
                    <td>' . ($item['product_name'] ?? 'Product #' . ($item['product_id'] ?? '')) . '</td>
                    <td>' . ($item['quantity'] ?? 0) . '</td>
                    <td>SAR ' . number_format($item['unit_price'] ?? 0, 2) . '</td>
                    <td>SAR ' . number_format($itemTotal, 2) . '</td>
                </tr>';
            }
            
            $html .= '
            </tbody>
        </table>
    </div>';
            
            $tax = $subtotal * 0.15;
            $total = $quote['total_amount'] ?? ($subtotal + $tax);
            
            $html .= '
    <div class="total">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>SAR ' . number_format($subtotal, 2) . '</span>
        </div>
        <div class="total-row">
            <span>Tax (15%):</span>
            <span>SAR ' . number_format($tax, 2) . '</span>
        </div>
        <div class="total-row grand-total">
            <span><strong>Total Amount:</strong></span>
            <span><strong>SAR ' . number_format($total, 2) . '</strong></span>
        </div>
    </div>';
        }
        
        if (isset($quote['notes']) && !empty($quote['notes'])) {
            $html .= '
    <div class="section">
        <div class="section-title">Notes</div>
        <p>' . $quote['notes'] . '</p>
    </div>';
        }
        
        $html .= '
    <div style="margin-top: 40px; text-align: center; font-size: 12px; color: #666;">
        <p>This quote is valid until ' . ($quote['expiry_date'] ?? 'N/A') . '</p>
        <p>Generated on ' . date('Y/m/d H:i') . '</p>
    </div>
</body>
</html>';
        
        return $html;
}
    
    /**
     * Print quote (view only)
     */
    public function printQuote($quoteId)
    {
        try {
            \Log::info('Printing quote', ['quote_id' => $quoteId]);

            // Get quote from Qoyod first to check if it exists
            $qoyodService = new \App\Services\QoyodService();
            
            // Try to get quote by local ID first, then by qoyod_id if needed
            $quote = $qoyodService->getQuote($quoteId);
            
            // If not found by local ID, try to find it in all quotes
            if (!$quote) {
                \Log::info('Quote not found by local ID, searching in all quotes', ['quote_id' => $quoteId]);
                $allQuotes = $qoyodService->getQuotes();
                
                if (isset($allQuotes['quotes'])) {
                    // Look for quote with matching reference or ID
                    foreach ($allQuotes['quotes'] as $qoyodQuote) {
                        if ($qoyodQuote['id'] == $quoteId || 
                            (isset($qoyodQuote['reference']) && $qoyodQuote['reference'] == $quoteId)) {
                            $quote = $qoyodQuote;
                            break;
                        }
                    }
                }
            }

            if (!$quote) {
                \Log::warning('Quote not found for printing', ['quote_id' => $quoteId]);
                return redirect()->route('dashboard.customers')->withErrors(['error' => 'Quote not found.']);
            }

            // Get customer info if available
            $customer = null;
            if (isset($quote['contact_id'])) {
                $customer = $qoyodService->getCustomer($quote['contact_id']);
            }

            // Get products info for quote line items
            $products = [];
            if (isset($quote['line_items']) && is_array($quote['line_items'])) {
                // Get all unique product IDs from line items
                $productIds = array_unique(array_column($quote['line_items'], 'product_id'));
                
                // Fetch products from Qoyod
                $productsResponse = $qoyodService->getProducts();
                $allProducts = $productsResponse['products'] ?? [];
                
                // Filter to only products used in this quote
                $products = array_filter($allProducts, function($product) use ($productIds) {
                    return in_array($product['id'], $productIds);
                });
                
                // Create a keyed array for easy lookup
                $productsLookup = [];
                foreach ($products as $product) {
                    $productsLookup[$product['id']] = $product;
                }
                $products = $productsLookup;
            }

            \Log::info('Quote data prepared for printing', [
                'quote_id' => $quoteId,
                'customer_name' => $customer['name'] ?? $customer['display_name'] ?? 'Unknown',
                'total_amount' => $quote['total_amount'] ?? 'N/A',
                'line_items_count' => count($quote['line_items']),
                'products_count' => count($products)
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            // Generate base64 encoded logo for PDF
            $logoPath = public_path('images/idg_logo.jpg');
            $logoDataUri = '';
            if (file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                $imageInfo = getimagesize($logoPath);
                $mimeType = $imageInfo['mime'];
                $logoDataUri = "data:$mimeType;base64,$logoData";
            }

            return view('quotes.print-view', [
                'quote' => $quote,
                'customer' => $customer,
                'products' => $products,
                'logoDataUri' => $logoDataUri,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error preparing quote for printing', [
                'quote_id' => $quoteId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'An error occurred while preparing the quote for printing.']);
        }
    }

    /**
     * Get Qoyod quote PDF
     */
    public function getQoyodQuotePdf($quoteId)
    {
        try {
            \Log::info('Getting Qoyod quote PDF', ['quote_id' => $quoteId]);

            // Get quote from Qoyod first to check if it exists and get qoyod_id
            $qoyodService = new \App\Services\QoyodService();
            
            // Try to get quote by local ID first, then by qoyod_id if needed
            $quote = $qoyodService->getQuote($quoteId);
            \Log::info('First attempt to get quote for PDF', ['quote_id' => $quoteId, 'found' => $quote ? 'yes' : 'no']);
            
            // If not found by local ID, try to find it in all quotes
            if (!$quote) {
                \Log::info('Quote not found by local ID, searching in all quotes', ['quote_id' => $quoteId]);
                $allQuotes = $qoyodService->getQuotes();
                
                if (isset($allQuotes['quotes'])) {
                    \Log::info('Searching through quotes for PDF', ['total_quotes' => count($allQuotes['quotes'])]);
                    // Look for quote with matching reference or ID
                    foreach ($allQuotes['quotes'] as $qoyodQuote) {
                        \Log::info('Checking quote for PDF', [
                            'qoyod_id' => $qoyodQuote['id'] ?? 'N/A',
                            'reference' => $qoyodQuote['reference'] ?? 'N/A',
                            'searching_for' => $quoteId
                        ]);
                        if ($qoyodQuote['id'] == $quoteId || 
                            (isset($qoyodQuote['reference']) && $qoyodQuote['reference'] == $quoteId)) {
                            $quote = $qoyodQuote;
                            \Log::info('Found matching quote for PDF', ['quote' => $quote]);
                            break;
                        }
                    }
                }
            }

            if (!$quote) {
                \Log::warning('Quote not found for PDF download', ['quote_id' => $quoteId]);
                return response()->json(['error' => 'Quote not found.'], 404);
            }

            // Check if quote has qoyod_id
            if (!isset($quote['id'])) {
                \Log::warning('Quote does not have Qoyod ID', ['quote_id' => $quoteId]);
                return response()->json(['error' => 'Quote not found in Qoyod.'], 404);
            }

            $qoyodQuoteId = $quote['id'];
            \Log::info('Using Qoyod ID for PDF', ['qoyod_quote_id' => $qoyodQuoteId]);

            // Get PDF from Qoyod
            $pdfData = $qoyodService->getQuotePdf($qoyodQuoteId);

            if (!$pdfData) {
                \Log::error('Failed to get PDF from Qoyod', ['qoyod_quote_id' => $qoyodQuoteId]);
                return response()->json(['error' => 'Failed to get PDF from Qoyod.'], 500);
            }

            \Log::info('PDF fetched successfully from Qoyod', [
                'qoyod_quote_id' => $qoyodQuoteId,
                'pdf_size' => strlen($pdfData)
            ]);

            // Return PDF response
            return response($pdfData)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="quote-' . $qoyodQuoteId . '.pdf"')
                ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');

        } catch (\Exception $e) {
            \Log::error('Error getting Qoyod quote PDF', [
                'quote_id' => $quoteId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json(['error' => 'An error occurred while getting the PDF from Qoyod.'], 500);
        }
    }

    /**
     * Show create invoice form for customer
     */
    public function showCreateInvoice($customerId)
    {
        try {
            \Log::info('Showing create invoice form', ['customer_id' => $customerId]);
            
            // Get customer info from Qoyod
            $qoyodService = new \App\Services\QoyodService();
            $customer = $qoyodService->getCustomer($customerId);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get artifacts for this customer
            $artifacts = \App\Models\Artifact::where('qoyod_customer_id', $customerId)
                ->orderBy('created_at', 'desc')
                ->get();

            // Get products from Qoyod
            $productsResponse = $qoyodService->getProducts();
            
              // Get locations from Qoyod for default selection
            $locations = $qoyodService->getLocations();

            \Log::info('Data prepared for invoice form', [
                'customer_id' => $customerId,
                'customer_name' => $customer['name'] || $customer['display_name'] || 'Unknown',
                'artifacts_count' => $artifacts->count(),
                'products_count' => count($productsResponse['products'] ?? [])
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return Inertia::render('Dashboard/Customers/CreateInvoice', [
                'customer' => $customer,
                'artifacts' => $artifacts,
                'products' => $productsResponse['products'] ?? [],
                'locations' => $locations,
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);
        } catch (\Exception $e) {
            \Log::error('Error showing create invoice form', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->route('dashboard.customers.artifacts.index', $customerId)
                ->withErrors(['error' => 'An error occurred while loading the invoice form.']);
        }
    }

    /**
     * Store/create invoice for customer
     */
    public function storeInvoice(Request $request, $customerId)
    {
        try {
            $validatedData = $request->validate([
                'reference' => 'nullable|string|max:255',
                'description' => 'nullable|string|max:500',
                'notes' => 'nullable|string|max:1000',
                'issue_date' => 'required|date',
                'due_date' => 'required|date|after:issue_date',
                'delivery_date' => 'required|date',
                'status' => 'required|in:Draft,Approved',
                'contact_id' => 'required|integer',
                'inventory_id' => 'required|integer',
                'location_id' => 'required|integer',
                'payment_method' => 'nullable|string|max:100',
                'draft_if_out_of_stock' => 'boolean',
                'line_items' => 'required|array|min:1',
                'line_items.*.product_id' => 'required|integer',
                'line_items.*.description' => 'nullable|string|max:500',
                'line_items.*.quantity' => 'required|numeric|min:0.01',
                'line_items.*.unit_price' => 'required|numeric|min:0',
                'line_items.*.unit_type' => 'nullable|string|max:100',
                'line_items.*.discount' => 'nullable|numeric|min:0',
                'line_items.*.discount_type' => 'nullable|in:percentage,amount',
                'line_items.*.tax_percent' => 'nullable|integer|min:0|max:100',
                'line_items.*.is_inclusive' => 'boolean',
                'custom_fields' => 'nullable|array',
                'custom_fields.*.name' => 'required_with:custom_fields|string|max:255',
                'custom_fields.*.value' => 'required_with:custom_fields|string|max:500',
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            \Log::info('Creating invoice in Qoyod', [
                'customer_id' => $customerId,
                'invoice_data' => $validatedData
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            $qoyodService = new \App\Services\QoyodService();
            
            // Remove reference from validated data - let Qoyod service generate it
            unset($validatedData['reference']);
            
            // Handle payment_method - convert Arabic text to numeric values for Qoyod
            $paymentMethod = $validatedData['payment_method'] ?? null;
            if ($paymentMethod) {
                \Log::info('Payment method provided', ['payment_method' => $paymentMethod]);
                
                // Map Arabic payment methods to numeric values - ALL CONFIRMED WITH MANUAL TESTING
                $paymentMethodMap = [
                    'نقدي' => 10,  // ✅ CONFIRMED - Invoice 60
                    'بطاقة بنك' => 48,  // ✅ CONFIRMED - Manual testing Invoice 80
                    'بالآجل' => 30,  // ✅ CONFIRMED - Manual testing Invoice 77
                    'دفعة لحساب البنك' => 42,  // ✅ CONFIRMED - Manual testing Invoice 79
                    'غير محدد' => 1  // ✅ CONFIRMED - Manual testing Invoice 81
                ];
                
                // Convert Arabic text to numeric value
                $numericPaymentMethod = $paymentMethodMap[$paymentMethod] ?? 0; // Default to 0
                
                // Update the payment_method with numeric value
                $validatedData['payment_method'] = $numericPaymentMethod;
                
                \Log::info('Payment method converted to numeric value', [
                    'original' => $paymentMethod,
                    'numeric' => $numericPaymentMethod
                ]);
            }
            
            $invoiceData = [
                'invoice' => $validatedData
            ];

            $result = $qoyodService->createInvoice($invoiceData);

            if (isset($result['invoice']) && isset($result['invoice']['id'])) {
                \Log::info('Invoice created successfully', [
                    'customer_id' => $customerId,
                    'invoice_id' => $result['invoice']['id'],
                    'reference' => $result['invoice']['reference'] ?? null
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                return redirect()->route('dashboard.customers.invoices', $customerId)
                    ->with('success', 'Invoice created successfully!');
            } else {
                \Log::error('Failed to create invoice', [
                    'customer_id' => $customerId,
                    'response' => $result
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                $errorMessage = $result['message'] ?? 'Failed to create invoice';
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['error' => $errorMessage]);
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error while creating invoice', [
                'customer_id' => $customerId,
                'errors' => $e->errors()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors($e->errors());

        } catch (\Exception $e) {
            \Log::error('Exception while creating invoice', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'An error occurred while creating the invoice. Please try again.']);
        }
    }

    /**
     * List customer invoices
     */
    public function listCustomerInvoices($customerId)
    {
        try {
            \Log::info('Listing customer invoices', ['customer_id' => $customerId]);

            $qoyodService = new \App\Services\QoyodService();
            
            // Get customer info
            $customer = $qoyodService->getCustomer($customerId);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Clear invoice cache first
            Cache::forget("qoyod_invoices_page_1_per_50");
            Cache::forget("qoyod_invoices_page_1_per_1000");
            
            // Get all invoices from Qoyod (exactly same logic as quotes)
            $invoicesResponse = $qoyodService->getInvoices(1, 1000); // Get more invoices
            $allInvoices = $invoicesResponse['invoices'] ?? $invoicesResponse['data'] ?? [];
            
            // Filter invoices for this customer and add customer info
            $customerInvoices = array_filter($allInvoices, function($invoice) use ($customerId) {
                return isset($invoice['contact_id']) && $invoice['contact_id'] == $customerId;
            });
            
            // Add customer name to each invoice
            $customerInvoices = array_map(function($invoice) use ($customer) {
                $invoice['customer_name'] = $customer['name'] ?? $customer['display_name'] ?? $customer['title'] ?? 'Unknown Customer';
                $invoice['contact_name'] = $invoice['customer_name']; // Ensure contact_name exists
                return $invoice;
            }, $customerInvoices);

            \Log::info('Customer invoices data prepared', [
                'customer_id' => $customerId,
                'customer_data' => $customer,
                'customer_name' => $customer['name'] ?? $customer['display_name'] ?? 'Unknown',
                'total_invoices' => count($allInvoices),
                'customer_invoices_count' => count($customerInvoices),
                'response_structure' => array_keys($invoicesResponse),
                'sample_invoice' => isset($customerInvoices[0]) ? $customerInvoices[0] : null
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return Inertia::render('Dashboard/Customers/ShowCustomerInvoices', [
                'customer' => $customer,
                'invoices' => $customerInvoices,
                'meta' => $invoicesResponse['meta'] ?? []
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error listing customer invoices', [
                'customer_id' => $customerId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'An error occurred while fetching invoices.']);
        }
    }

    /**
     * Show specific invoice details
     */
    public function showInvoice($customerId, $invoiceId)
    {
        try {
            \Log::info('Showing invoice details', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            $qoyodService = new \App\Services\QoyodService();
            
            // Get customer info
            $customer = $qoyodService->getCustomer($customerId);
            
            if (!$customer) {
                \Log::warning('Customer not found in Qoyod', ['customer_id' => $customerId]);
                return redirect()->route('dashboard.customers')
                    ->withErrors(['error' => 'Customer not found in Qoyod.']);
            }

            // Get invoice details
            $invoiceResponse = $qoyodService->getInvoice($invoiceId);

            if (!isset($invoiceResponse['invoice'])) {
                \Log::error('Invoice not found in Qoyod', [
                    'customer_id' => $customerId,
                    'invoice_id' => $invoiceId,
                    'response' => $invoiceResponse
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                return redirect()->route('dashboard.customers.invoices', $customerId)
                    ->withErrors(['error' => $invoiceResponse['message'] ?? 'Invoice not found.']);
            }

            \Log::info('Invoice details retrieved successfully', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId,
                'reference' => $invoiceResponse['invoice']['reference'] ?? null
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return Inertia::render('Dashboard/Customers/ShowInvoice', [
                'customer' => $customer,
                'invoice' => $invoiceResponse['invoice'],
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error showing invoice details', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'An error occurred while fetching invoice details.']);
        }
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoicePdf($customerId, $invoiceId)
    {
        try {
            \Log::info('Downloading invoice PDF', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            $qoyodService = new \App\Services\QoyodService();
            $pdfResponse = $qoyodService->getInvoicePdf($invoiceId);

            if (isset($pdfResponse['pdf_file'])) {
                \Log::info('Invoice PDF link retrieved successfully', [
                    'customer_id' => $customerId,
                    'invoice_id' => $invoiceId,
                    'pdf_url' => $pdfResponse['pdf_file']
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                // Redirect to PDF URL
                return redirect($pdfResponse['pdf_file']);
            } else {
                \Log::error('Failed to get invoice PDF', [
                    'customer_id' => $customerId,
                    'invoice_id' => $invoiceId,
                    'response' => $pdfResponse
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                return redirect()->back()->withErrors(['error' => 'Failed to generate PDF. Please try again.']);
            }

        } catch (\Exception $e) {
            \Log::error('Error downloading invoice PDF', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'An error occurred while generating PDF.']);
        }
    }

    /**
     * Delete invoice
     */
    public function deleteInvoice($customerId, $invoiceId)
    {
        try {
            \Log::info('Deleting invoice', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            $qoyodService = new \App\Services\QoyodService();
            $result = $qoyodService->deleteInvoice($invoiceId);

            if ($result['success']) {
                \Log::info('Invoice deleted successfully', [
                    'customer_id' => $customerId,
                    'invoice_id' => $invoiceId
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                return redirect()->route('dashboard.customers.invoices', $customerId)
                    ->with('success', 'Invoice deleted successfully!');
            } else {
                \Log::error('Failed to delete invoice', [
                    'customer_id' => $customerId,
                    'invoice_id' => $invoiceId,
                    'response' => $result
                ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

                return redirect()->back()->withErrors(['error' => $result['message'] ?? 'Failed to delete invoice.']);
            }

        } catch (\Exception $e) {
            \Log::error('Error deleting invoice', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'An error occurred while deleting the invoice.']);
        }
    }

    /**
     * Edit invoice
     */
    public function editInvoice($customerId, $invoiceId)
    {
        try {
            \Log::info('Editing invoice', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            $qoyodService = new \App\Services\QoyodService();
            
            // Get invoice details
            $invoice = $qoyodService->getInvoice($invoiceId);
            
            if (!$invoice || !isset($invoice['invoice'])) {
                return redirect()->back()->withErrors(['error' => 'Invoice not found.']);
            }

            // Get customer details
            $customer = $qoyodService->getCustomer($customerId);
            
            if (!$customer) {
                return redirect()->back()->withErrors(['error' => 'Customer not found.']);
            }

            // Get products for line items
            $products = $qoyodService->getProducts();
            
            // Get locations/warehouses
            $locations = $qoyodService->getLocations();

            return Inertia::render('Dashboard/Customers/EditInvoice', [
                'customer' => $customer,
                'invoice' => $invoice['invoice'],
                'products' => $products['products'] ?? [],
                'locations' => $locations,
                'meta' => [
                    'customer_id' => $customerId,
                    'invoice_id' => $invoiceId
                ]
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

        } catch (\Exception $e) {
            \Log::error('Error editing invoice', [
                'customer_id' => $customerId,
                'invoice_id' => $invoiceId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], [
                'tax_number.min' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
                'tax_number.max' => 'الرقم الضريبي يجب أن يكون 15 رقم بالضبط',
            ]);

            return redirect()->back()->withErrors(['error' => 'An error occurred while loading the invoice for editing.']);
        }
    }
}
