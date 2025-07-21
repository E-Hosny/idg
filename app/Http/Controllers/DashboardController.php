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
        $query = Artifact::with(['category', 'assignedTo', 'client']);
        
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
        }

        return Inertia::render($evaluationPage, [
            'artifact' => $artifact,
            'existingEvaluation' => $existingEvaluation,
        ]);
    }

    public function storeEvaluation(Request $request, Artifact $artifact)
    {
        // التحقق من أن المستخدم مسجل دخول
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        try {
            // التحقق من نوع القطعة وحفظ التقييم المناسب
            if ($artifact->type === 'Colorless Diamonds') {
                $this->storeDiamondEvaluation($request, $artifact);
                $message = 'Diamond evaluation saved successfully!';
            } else {
                // يمكن إضافة تقييمات أخرى هنا لاحقاً
                $this->storeGeneralEvaluation($request, $artifact);
                $message = 'Evaluation saved successfully!';
            }

            // تحديث حالة القطعة حسب نوع التقييم
            if ($request->status === 'completed') {
                $artifact->update([
                    'status' => 'evaluated',
                    'assigned_to' => auth()->id(),
                ]);
            } else {
                $artifact->update([
                    'status' => 'under_evaluation',
                    'assigned_to' => auth()->id(),
                ]);
            }

            return redirect()
                ->route('dashboard.artifacts')
                ->with('success', $message);

        } catch (\Exception $e) {
            \Log::error('Evaluation save error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'An error occurred while saving the evaluation. Please try again.']);
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
        // هذه الطريقة يمكن تطويرها لاحقاً للتقييمات العامة
        // حالياً سنحفظ في ArtifactEvaluation القديم
        
        $validatedData = $request->validate([
            'comments' => 'nullable|string',
            'result' => 'nullable|string',
        ]);

        ArtifactEvaluation::create([
            'artifact_id' => $artifact->id,
            'evaluator_id' => auth()->id(),
            'evaluation_date' => now(),
            'comments' => $validatedData['comments'] ?? '',
            'result' => $validatedData['result'] ?? '',
            'is_final' => true,
        ]);
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
        } else {
            // للأنواع الأخرى (يمكن إضافة المزيد لاحقاً)
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
        $artifacts = Artifact::with(['client', 'category'])
            ->whereIn('status', ['evaluated', 'certified'])
            ->orderBy('updated_at', 'desc')
            ->paginate(15);

        return Inertia::render('Dashboard/Artifacts/EvaluatedIndex', [
            'artifacts' => $artifacts,
        ]);
    }
}
