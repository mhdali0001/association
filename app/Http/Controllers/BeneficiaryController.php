<?php

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BeneficiaryController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'allocated_amount' => 'required|numeric|min:0',
            'notes'            => 'nullable|string|max:1000',
        ]);

        Beneficiary::create(array_merge($data, ['user_id' => Auth::id()]));

        return back()->with('success', 'تم إضافة المستفيد بنجاح.');
    }

    public function update(Request $request, Beneficiary $beneficiary)
    {
        abort_if($beneficiary->user_id !== Auth::id(), 403);

        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'allocated_amount' => 'required|numeric|min:0',
            'notes'            => 'nullable|string|max:1000',
        ]);

        $beneficiary->update($data);

        return back()->with('success', 'تم تحديث بيانات المستفيد.');
    }

    public function destroy(Beneficiary $beneficiary)
    {
        abort_if($beneficiary->user_id !== Auth::id(), 403);

        $beneficiary->delete();

        return back()->with('success', 'تم حذف المستفيد.');
    }
}
