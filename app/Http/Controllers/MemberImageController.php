<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\MemberImage;
use App\Models\PendingChange;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MemberImageController extends Controller
{
    public function index(Request $request)
    {
        $search     = $request->get('search');
        $memberName = $request->get('member_name');

        $query = MemberImage::with('member', 'uploader')->latest();

        if ($memberName) {
            $query->whereHas('member', fn ($m) => $m->where('full_name', 'like', "%{$memberName}%"));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('file_name', 'like', "%{$search}%");
            });
        }

        $images  = $query->paginate(24)->withQueryString();
        $members = Member::orderBy('full_name')->get(['id', 'full_name']);

        return view('member-images.index', compact('images', 'members', 'search', 'memberName'));
    }

    /** رفع من صفحة العضو مباشرة */
    public function store(Request $request, Member $member)
    {
        $this->upload($request, $member);
        $msg = $this->isAdmin() ? 'تم رفع الملف بنجاح.' : 'تم إرسال طلب رفع الملف للمراجعة.';
        return back()->with('success', $msg);
    }

    /** رفع من صفحة الأرشيف العامة */
    public function storeGlobal(Request $request)
    {
        $request->validate(['member_id' => 'required|exists:members,id']);
        $member = Member::findOrFail($request->input('member_id'));
        $this->upload($request, $member);
        $msg = $this->isAdmin() ? 'تم رفع الملف بنجاح.' : 'تم إرسال طلب رفع الملف للمراجعة.';
        return back()->with('success', $msg);
    }

    public function edit(MemberImage $memberImage)
    {
        return view('member-images.edit', compact('memberImage'));
    }

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    public function update(Request $request, MemberImage $memberImage)
    {
        $request->validate(['title' => 'nullable|string|max:255']);

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'member_image',
                'model_id'     => $memberImage->id,
                'action'       => 'update',
                'payload'      => ['title' => $request->input('title') ?: null, 'member_name' => $memberImage->member?->full_name, 'file_name' => $memberImage->file_name],
                'original'     => ['title' => $memberImage->title,              'member_name' => $memberImage->member?->full_name, 'file_name' => $memberImage->file_name],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return redirect()->route('member-images.index')
                             ->with('success', 'تم إرسال طلب تعديل وصف الملف للمراجعة.');
        }

        $memberImage->update(['title' => $request->input('title') ?: null]);
        ActivityLogger::log('updated', "تعديل وصف ملف: {$memberImage->file_name}", $memberImage);
        return redirect()->route('member-images.index')->with('success', 'تم تحديث وصف الملف.');
    }

    public function destroy(MemberImage $memberImage)
    {
        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'member_image',
                'model_id'     => $memberImage->id,
                'action'       => 'delete',
                'original'     => ['member_name' => $memberImage->member?->full_name, 'file_name' => $memberImage->file_name, 'title' => $memberImage->title, 'mime_type' => $memberImage->mime_type],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return back()->with('success', 'تم إرسال طلب حذف الملف للمراجعة.');
        }

        Storage::disk('public')->delete($memberImage->file_path);
        ActivityLogger::log('deleted', "حذف ملف: {$memberImage->file_name}");
        $memberImage->delete();
        return back()->with('success', 'تم حذف الملف.');
    }

    private function upload(Request $request, Member $member): void
    {
        $request->validate([
            'image' => 'required|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
            'title' => 'nullable|string|max:255',
        ], [
            'image.required' => 'يرجى اختيار ملف.',
            'image.mimes'    => 'الملفات المدعومة: JPG, PNG, GIF, WEBP, PDF.',
            'image.max'      => 'الحجم الأقصى للملف هو 10 ميغابايت.',
        ]);

        $file = $request->file('image');
        $path = $file->storeAs(
            "member-images/{$member->id}",
            Str::uuid() . '.' . $file->getClientOriginalExtension(),
            'public'
        );

        if (!$this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'member_image',
                'action'       => 'create',
                'payload'      => [
                    'member_id'   => $member->id,
                    'member_name' => $member->full_name,
                    'title'       => $request->input('title') ?: null,
                    'file_path'   => $path,
                    'file_name'   => $file->getClientOriginalName(),
                    'file_size'   => $file->getSize(),
                    'mime_type'   => $file->getMimeType(),
                    'uploaded_by' => Auth::id(),
                ],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);
            return;
        }

        $img = MemberImage::create([
            'member_id'   => $member->id,
            'title'       => $request->input('title') ?: null,
            'file_path'   => $path,
            'file_name'   => $file->getClientOriginalName(),
            'file_size'   => $file->getSize(),
            'mime_type'   => $file->getMimeType(),
            'uploaded_by' => Auth::id(),
        ]);

        ActivityLogger::log('created', "رفع ملف: {$img->file_name} للعضو: {$member->full_name}", $img);
    }
}
