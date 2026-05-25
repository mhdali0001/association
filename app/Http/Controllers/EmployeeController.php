<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeTransaction;
use App\Services\ActivityLogger;
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
        $totalAdvancesSYP = EmployeeTransaction::where('type', 'advance')->where('currency', 'SYP')->sum('amount');
        $totalAdvancesUSD = EmployeeTransaction::where('type', 'advance')->where('currency', 'USD')->sum('amount');

        return view('employees.index', compact(
            'employees',
            'totalPaidSYP', 'totalPaidUSD',
            'totalDeductedSYP', 'totalDeductedUSD',
            'totalAdvancesSYP', 'totalAdvancesUSD'
        ));
    }

    public function store(Request $request)
    {
        $this->adminOnly();

        $data = $request->validate([
            'name'                 => 'required|string|max:255',
            'job_title'            => 'nullable|string|max:255',
            'department'           => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:50',
            'base_salary'          => 'nullable|numeric|min:0',
            'base_salary_currency' => 'nullable|in:SYP,USD',
            'notes'                => 'nullable|string|max:1000',
            'hire_date'            => 'nullable|date',
            'access_pin'           => 'nullable|string|max:20|unique:employees,access_pin',
        ]);

        $data['base_salary']          = $data['base_salary'] ?? 0;
        $data['base_salary_currency'] = $data['base_salary_currency'] ?? 'SYP';
        if (empty($data['access_pin'])) unset($data['access_pin']);

        $employee = Employee::create($data);
        ActivityLogger::log('created', "إضافة موظف: {$employee->name}", $employee);

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
            'department'           => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:50',
            'base_salary'          => 'nullable|numeric|min:0',
            'base_salary_currency' => 'nullable|in:SYP,USD',
            'notes'                => 'nullable|string|max:1000',
            'is_active'            => 'nullable|boolean',
            'hire_date'            => 'nullable|date',
            'access_pin'           => 'nullable|string|max:20|unique:employees,access_pin,' . $employee->id,
        ]);
        if (empty($data['access_pin'])) unset($data['access_pin']);

        $data['base_salary']          = $data['base_salary'] ?? 0;
        $data['base_salary_currency'] = $data['base_salary_currency'] ?? 'SYP';
        $data['is_active']            = $request->boolean('is_active');

        $employee->update($data);
        ActivityLogger::log('updated', "تعديل بيانات الموظف: {$employee->name}", $employee);

        return redirect()->route('employees.show', $employee)->with('success', 'تم تحديث بيانات الموظف.');
    }

    public function destroy(Employee $employee)
    {
        $this->adminOnly();
        $name = $employee->name;
        $employee->delete();
        ActivityLogger::log('deleted', "حذف الموظف: {$name}");
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

        $typeLabels = ['salary' => 'راتب', 'addition' => 'إضافة', 'deduction' => 'خصم', 'advance' => 'سلفة', 'bonus' => 'مكافأة'];
        $typeLabel  = $typeLabels[$data['type']] ?? $data['type'];

        $tx = EmployeeTransaction::create($data);
        ActivityLogger::log('created', "إضافة معاملة مالية ({$typeLabel}) للموظف: {$employee->name} — {$data['amount']} {$data['currency']}", $employee);

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

        $typeLabels = ['salary' => 'راتب', 'addition' => 'إضافة', 'deduction' => 'خصم', 'advance' => 'سلفة', 'bonus' => 'مكافأة'];
        $typeLabel  = $typeLabels[$data['type']] ?? $data['type'];

        $transaction->update($data);
        ActivityLogger::log('updated', "تعديل معاملة مالية ({$typeLabel}) للموظف: {$employee->name} — {$data['amount']} {$data['currency']}", $employee);

        return back()->with('success', 'تم تعديل العملية بنجاح.');
    }

    public function destroyTransaction(Employee $employee, EmployeeTransaction $transaction)
    {
        $this->adminOnly();
        abort_if($transaction->employee_id !== $employee->id, 403);
        $typeLabels = ['salary' => 'راتب', 'addition' => 'إضافة', 'deduction' => 'خصم', 'advance' => 'سلفة', 'bonus' => 'مكافأة'];
        $typeLabel  = $typeLabels[$transaction->type] ?? $transaction->type;
        $transaction->delete();
        ActivityLogger::log('deleted', "حذف معاملة مالية ({$typeLabel}) للموظف: {$employee->name}", $employee);
        return back()->with('success', 'تم حذف العملية.');
    }
}
