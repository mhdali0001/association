<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeController extends Controller
{
    private function adminOnly(): void
    {
        abort_if(Auth::user()?->role !== 'admin', 403);
    }

    public function index()
    {
        $this->adminOnly();

        $employees = Employee::withCount('transactions')
            ->with('transactions:employee_id,type,amount,currency')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();

        $totalPaidSYP     = EmployeeTransaction::whereIn('type', ['salary', 'addition', 'bonus'])->where('currency', 'SYP')->sum('amount');
        $totalPaidUSD     = EmployeeTransaction::whereIn('type', ['salary', 'addition', 'bonus'])->where('currency', 'USD')->sum('amount');
        $totalDeductedSYP = EmployeeTransaction::where('type', 'deduction')->where('currency', 'SYP')->sum('amount');
        $totalDeductedUSD = EmployeeTransaction::where('type', 'deduction')->where('currency', 'USD')->sum('amount');

        return view('employees.index', compact(
            'employees',
            'totalPaidSYP', 'totalPaidUSD',
            'totalDeductedSYP', 'totalDeductedUSD'
        ));
    }

    public function store(Request $request)
    {
        $this->adminOnly();

        $data = $request->validate([
            'name'                 => 'required|string|max:255',
            'job_title'            => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:50',
            'base_salary'          => 'nullable|numeric|min:0',
            'base_salary_currency' => 'nullable|in:SYP,USD',
            'notes'                => 'nullable|string|max:1000',
        ]);

        $data['base_salary']          = $data['base_salary'] ?? 0;
        $data['base_salary_currency'] = $data['base_salary_currency'] ?? 'SYP';

        Employee::create($data);

        return redirect()->route('employees.index')->with('success', 'تم إضافة الموظف بنجاح.');
    }

    public function show(Employee $employee)
    {
        $this->adminOnly();

        $typeFilter = request('type', 'all');
        $search     = trim(request('search', ''));

        $txQuery = $employee->transactions()->with('creator');

        if ($typeFilter !== 'all') {
            $txQuery->where('type', $typeFilter);
        }
        if ($search !== '') {
            $txQuery->where('reason', 'like', "%{$search}%");
        }

        $transactions = $txQuery->get();

        $all = $employee->transactions()->get();

        $totals = [];
        foreach (['SYP', 'USD'] as $cur) {
            $byCur = $all->where('currency', $cur);
            $totals[$cur] = [
                'salary'    => (float) $byCur->where('type', 'salary')->sum('amount'),
                'additions' => (float) $byCur->where('type', 'addition')->sum('amount'),
                'deductions'=> (float) $byCur->where('type', 'deduction')->sum('amount'),
                'advances'  => (float) $byCur->where('type', 'advance')->sum('amount'),
                'bonuses'   => (float) $byCur->where('type', 'bonus')->sum('amount'),
            ];
            $totals[$cur]['net'] = $totals[$cur]['salary'] + $totals[$cur]['additions']
                + $totals[$cur]['bonuses'] - $totals[$cur]['deductions'] - $totals[$cur]['advances'];
        }

        return view('employees.show', compact(
            'employee', 'transactions', 'typeFilter', 'search', 'totals'
        ));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->adminOnly();

        $data = $request->validate([
            'name'                 => 'required|string|max:255',
            'job_title'            => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:50',
            'base_salary'          => 'nullable|numeric|min:0',
            'base_salary_currency' => 'nullable|in:SYP,USD',
            'notes'                => 'nullable|string|max:1000',
            'is_active'            => 'nullable|boolean',
        ]);

        $data['base_salary']          = $data['base_salary'] ?? 0;
        $data['base_salary_currency'] = $data['base_salary_currency'] ?? 'SYP';
        $data['is_active']            = $request->boolean('is_active');

        $employee->update($data);

        return redirect()->route('employees.show', $employee)->with('success', 'تم تحديث بيانات الموظف.');
    }

    public function destroy(Employee $employee)
    {
        $this->adminOnly();
        $employee->delete();
        return redirect()->route('employees.index')->with('success', 'تم حذف الموظف.');
    }

    public function storeTransaction(Request $request, Employee $employee)
    {
        $this->adminOnly();

        $data = $request->validate([
            'type'             => 'required|in:salary,addition,deduction,advance,bonus',
            'amount'           => 'required|numeric|min:0.01',
            'currency'         => 'required|in:SYP,USD',
            'reason'           => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        $data['employee_id'] = $employee->id;
        $data['created_by']  = Auth::id();

        EmployeeTransaction::create($data);

        return back()->with('success', 'تمت إضافة العملية بنجاح.');
    }

    public function updateTransaction(Request $request, Employee $employee, EmployeeTransaction $transaction)
    {
        $this->adminOnly();
        abort_if($transaction->employee_id !== $employee->id, 403);

        $data = $request->validate([
            'type'             => 'required|in:salary,addition,deduction,advance,bonus',
            'amount'           => 'required|numeric|min:0.01',
            'currency'         => 'required|in:SYP,USD',
            'reason'           => 'nullable|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        $transaction->update($data);

        return back()->with('success', 'تم تعديل العملية بنجاح.');
    }

    public function destroyTransaction(Employee $employee, EmployeeTransaction $transaction)
    {
        $this->adminOnly();
        abort_if($transaction->employee_id !== $employee->id, 403);
        $transaction->delete();
        return back()->with('success', 'تم حذف العملية.');
    }
}
