<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeePortalController extends Controller
{
    private const SESSION_KEY = 'emp_portal_id';

    public function login()
    {
        if (session(self::SESSION_KEY)) {
            return redirect()->route('employee-portal.dashboard');
        }
        return view('employees.portal.login');
    }

    public function authenticate(Request $request)
    {
        $request->validate(['pin' => 'required|string']);

        $employee = Employee::whereNotNull('access_pin')
            ->where('access_pin', $request->pin)
            ->where('is_active', true)
            ->first();

        if (! $employee) {
            return back()->withErrors(['pin' => 'كلمة السر غير صحيحة أو الحساب غير نشط.'])->withInput();
        }

        session([self::SESSION_KEY => $employee->id]);

        return redirect()->route('employee-portal.dashboard');
    }

    public function dashboard()
    {
        $id = session(self::SESSION_KEY);
        if (! $id) {
            return redirect()->route('employee-portal.login');
        }

        $employee = Employee::with(['transactions' => fn($q) => $q->orderByDesc('transaction_date')->orderByDesc('id')])
            ->find($id);

        if (! $employee) {
            session()->forget(self::SESSION_KEY);
            return redirect()->route('employee-portal.login');
        }

        $totals = [];
        foreach (['SYP', 'USD'] as $cur) {
            $byCur = $employee->transactions->where('currency', $cur);
            $totals[$cur] = [
                'salary'     => (float) $byCur->where('type', 'salary')->sum('amount'),
                'additions'  => (float) $byCur->where('type', 'addition')->sum('amount'),
                'bonuses'    => (float) $byCur->where('type', 'bonus')->sum('amount'),
                'deductions' => (float) $byCur->where('type', 'deduction')->sum('amount'),
                'advances'   => (float) $byCur->where('type', 'advance')->sum('amount'),
            ];
            $totals[$cur]['net'] = $totals[$cur]['salary'] + $totals[$cur]['additions']
                + $totals[$cur]['bonuses'] - $totals[$cur]['deductions'] - $totals[$cur]['advances'];
        }

        return view('employees.portal.dashboard', compact('employee', 'totals'));
    }

    public function logout()
    {
        session()->forget(self::SESSION_KEY);
        return redirect()->route('employee-portal.login');
    }
}
