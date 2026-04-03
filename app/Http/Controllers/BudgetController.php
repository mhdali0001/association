<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $budget     = Budget::firstOrCreate(['user_id' => $userId], ['total_amount' => 0]);
        $totalSpent = (float) Expense::where('user_id', $userId)->sum('amount');
        $remaining  = (float) $budget->total_amount - $totalSpent;
        $percent    = $budget->total_amount > 0
                        ? min(100, round($totalSpent / $budget->total_amount * 100, 1))
                        : 0;

        // All expenses for this user
        $expenses = Expense::where('user_id', $userId)
                           ->with('beneficiary')
                           ->latest('date')->latest('id')
                           ->get();

        // Beneficiaries with their paid amounts
        $beneficiaries = Beneficiary::where('user_id', $userId)
                                    ->withCount('expenses')
                                    ->with('expenses')
                                    ->orderBy('name')
                                    ->get();

        // Unlinked expenses (no beneficiary)
        $unlinkedExpenses = Expense::where('user_id', $userId)
                                   ->whereNull('beneficiary_id')
                                   ->sum('amount');

        return view('budget.index', compact(
            'budget', 'totalSpent', 'remaining', 'percent',
            'expenses', 'beneficiaries', 'unlinkedExpenses'
        ));
    }

    public function setTotal(Request $request)
    {
        $data = $request->validate([
            'total_amount' => 'required|numeric|min:0',
        ]);

        Budget::updateOrCreate(
            ['user_id' => Auth::id()],
            ['total_amount' => $data['total_amount']]
        );

        return back()->with('success', 'تم تحديث إجمالي الميزانية بنجاح.');
    }
}
