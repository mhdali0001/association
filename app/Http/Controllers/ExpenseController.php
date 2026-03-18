<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    private function categories(): array
    {
        return ['رواتب', 'إيجار', 'مستلزمات مكتبية', 'مواصلات', 'اتصالات', 'صيانة', 'تبرعات صادرة', 'أخرى'];
    }

    public function index(Request $request)
    {
        $search   = $request->get('search');
        $category = $request->get('category');
        $from     = $request->get('from');
        $to       = $request->get('to');

        $query = Expense::with('user')->latest('date')->latest('id');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title',     'like', "%{$search}%")
                  ->orWhere('recipient', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($category) {
            $query->where('category', $category);
        }
        if ($from) {
            $query->whereDate('date', '>=', $from);
        }
        if ($to) {
            $query->whereDate('date', '<=', $to);
        }

        $expenses   = $query->paginate(20)->withQueryString();
        $total      = (clone $query->getQuery())->sum('amount');
        $categories = $this->categories();

        return view('expenses.index', compact('expenses', 'total', 'categories', 'search', 'category', 'from', 'to'));
    }

    public function create()
    {
        $categories = $this->categories();
        return view('expenses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:100',
            'date'        => 'required|date',
            'description' => 'nullable|string',
            'recipient'   => 'nullable|string|max:255',
        ]);

        $expense = Expense::create(array_merge($data, ['user_id' => Auth::id()]));
        ActivityLogger::log('created', "إضافة مصروف: {$expense->title} — {$expense->amount} ل.س", $expense);

        return redirect()->route('expenses.index')->with('success', 'تم إضافة المصروف بنجاح.');
    }

    public function show(Expense $expense)
    {
        return view('expenses.show', compact('expense'));
    }

    public function edit(Expense $expense)
    {
        $categories = $this->categories();
        return view('expenses.edit', compact('expense', 'categories'));
    }

    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'amount'      => 'required|numeric|min:0',
            'category'    => 'nullable|string|max:100',
            'date'        => 'required|date',
            'description' => 'nullable|string',
            'recipient'   => 'nullable|string|max:255',
        ]);

        $expense->update($data);
        ActivityLogger::log('updated', "تعديل مصروف: {$expense->title} — {$expense->amount} ل.س", $expense);

        return redirect()->route('expenses.index')->with('success', 'تم تحديث المصروف بنجاح.');
    }

    public function destroy(Expense $expense)
    {
        $title = $expense->title;
        $expense->delete();
        ActivityLogger::log('deleted', "حذف مصروف: {$title}");

        return redirect()->route('expenses.index')->with('success', 'تم حذف المصروف بنجاح.');
    }
}
