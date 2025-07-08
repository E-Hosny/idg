<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Artifact;
use App\Models\Category;
use App\Models\ArtifactEvaluation;
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
            'pending_evaluations' => Artifact::where('status', 'pending')->count(),
            'under_evaluation' => Artifact::where('status', 'under_evaluation')->count(),
            'completed_evaluations' => Artifact::where('status', 'evaluated')->count(),
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
        $query = Artifact::with(['category', 'assignedTo']);
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $artifacts = $query->orderBy('created_at', 'desc')->paginate(15);
        return Inertia::render('Dashboard/Artifacts/Index', [
            'artifacts' => $artifacts,
        ]);
    }

    public function evaluations()
    {
        $evaluations = ArtifactEvaluation::with(['artifact', 'evaluator'])
            ->orderBy('evaluation_date', 'desc')
            ->paginate(15);

        return Inertia::render('Dashboard/Evaluations/Index', [
            'evaluations' => $evaluations,
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
}
