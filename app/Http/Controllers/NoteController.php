<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Note;
use App\Models\NoteAttachment;
use App\Models\PendingChange;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Exports\NotesExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;

class NoteController extends Controller
{
    /** أنواع الملفات المسموحة وحجمها الأقصى (20 ميغابايت). */
    private const ATTACHMENT_MIMES = 'pdf,doc,docx,xls,xlsx,ppt,pptx,png,jpg,jpeg,webp,gif,txt,csv,zip';
    private const ATTACHMENT_MAX_KB = 20480;

    /** أقصى عدد مستفيدين يُعرض في قائمة الاختيار السريع (البحث يغطي الباقي). */
    private const BROWSE_LIMIT = 200;

    private function isAdmin(): bool
    {
        return Auth::user()?->role === 'admin';
    }

    private function authorizeManage(Note $note): void
    {
        abort_unless($this->isAdmin() || $note->created_by === Auth::id(), 403);
    }

    /** Shared filtered query for the list view and the Excel export. */
    private function filteredQuery(Request $request): \Illuminate\Database\Eloquent\Builder
    {
        $query = Note::with(['creator', 'members:id,full_name,dossier_number'])
            ->withCount('attachments');

        if (($search = trim($request->get('search', ''))) !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%")
                  ->orWhereHas('members', fn ($m) => $m->where('full_name', 'like', "%{$search}%"));
            });
        }
        if ($personId = $request->get('person')) {
            $query->whereHas('members', fn ($m) => $m->whereKey($personId));
        }
        if ($authorId = $request->get('author')) {
            $query->where('created_by', $authorId);
        }
        if (($category = $request->get('category')) && isset(Note::CATEGORIES[$category])) {
            $query->where('category', $category);
        }
        if ($from = $request->get('from')) {
            $query->whereDate('note_date', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('note_date', '<=', $to);
        }

        return $query;
    }

    public function index(Request $request)
    {
        $notes = $this->filteredQuery($request)
            ->orderByDesc('pinned')
            ->orderByDesc('note_date')
            ->orderByDesc('id')
            ->paginate(12)
            ->withQueryString();

        $authors  = User::orderBy('name')->get(['id', 'name']);
        $personId = $request->get('person');
        $person   = $personId ? Member::find($personId) : null;

        $stats = [
            'total'      => Note::count(),
            'with_files' => Note::has('attachments')->count(),
            'this_month' => Note::whereBetween('note_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])->count(),
            'mine'       => Note::where('created_by', Auth::id())->count(),
        ];

        return view('notes.index', [
            'notes'      => $notes,
            'authors'    => $authors,
            'person'     => $person,
            'stats'      => $stats,
            'categories' => Note::CATEGORIES,
            'search'     => trim($request->get('search', '')),
            'authorId'   => $request->get('author'),
            'category'   => $request->get('category'),
            'from'       => $request->get('from'),
            'to'         => $request->get('to'),
        ]);
    }

    public function export(Request $request)
    {
        $notes = $this->filteredQuery($request)
            ->with('members:id,full_name')
            ->orderByDesc('note_date')
            ->orderByDesc('id')
            ->get();

        ActivityLogger::log('exported', 'تصدير الملاحظات إلى Excel (' . $notes->count() . ' ملاحظة)');

        return Excel::download(new NotesExport($notes), 'ملاحظات-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function create(Request $request)
    {
        $prefillMemberIds = collect(explode(',', (string) $request->get('member')))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        return view('notes.create', [
            'browseMembers'    => $this->browseMembers(),
            'prefillMemberIds' => $prefillMemberIds,
        ]);
    }

    public function store(Request $request)
    {
        $data      = $this->validated($request, withAttachments: true);
        $memberIds = $this->cleanMemberIds($request);
        $uploads   = $this->storeUploads($request);

        if (! $this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'note',
                'action'       => 'create',
                'payload'      => array_merge($data, [
                    'category_label' => Note::categoryLabelFor($data['category']),
                    'member_ids'    => $memberIds,
                    'members_label' => $this->membersLabel($memberIds),
                    'member_name'   => $this->membersLabel($memberIds),
                    'attachments'   => $uploads,
                    'created_by'    => Auth::id(),
                ]),
                'original'     => [],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);

            return redirect()->route('notes.index')
                ->with('pending', 'تم إرسال طلب إضافة الملاحظة — بانتظار موافقة المسؤول.');
        }

        $note = Note::create(array_merge($data, ['created_by' => Auth::id()]));
        $note->members()->sync($memberIds);
        foreach ($uploads as $row) {
            $note->attachments()->create($row);
        }
        ActivityLogger::log('created', 'إضافة ملاحظة: ' . $this->noteLabel($note->load('members')), $note);

        return redirect()->route('notes.index')->with('success', 'تمت إضافة الملاحظة بنجاح.');
    }

    public function show(Note $note)
    {
        $note->load(['creator', 'members:id,full_name,dossier_number', 'attachments.uploader']);

        return view('notes.show', compact('note'));
    }

    public function edit(Note $note)
    {
        $this->authorizeManage($note);
        $note->load('members:id,full_name');

        return view('notes.edit', ['note' => $note, 'browseMembers' => $this->browseMembers()]);
    }

    /** A capped, alphabetical list of members for the "browse & pick" panel. */
    private function browseMembers(): \Illuminate\Support\Collection
    {
        return Member::orderBy('full_name')
            ->limit(self::BROWSE_LIMIT)
            ->get(['id', 'full_name', 'dossier_number'])
            ->map(fn ($m) => [
                'id'              => $m->id,
                'full_name'       => $m->full_name,
                'dossier_number'  => $m->dossier_number,
            ]);
    }

    public function update(Request $request, Note $note)
    {
        $this->authorizeManage($note);

        $data      = $this->validated($request, withAttachments: false, existing: $note);
        $memberIds = $this->cleanMemberIds($request);

        if (! $this->isAdmin()) {
            $currentIds = $note->members()->pluck('members.id')->all();

            PendingChange::create([
                'model_type'   => 'note',
                'model_id'     => $note->id,
                'action'       => 'update',
                'payload'      => array_merge($data, [
                    'category_label' => Note::categoryLabelFor($data['category']),
                    'member_ids'    => $memberIds,
                    'members_label' => $this->membersLabel($memberIds),
                    'member_name'   => $this->membersLabel($memberIds),
                ]),
                'original'     => [
                    'title'          => $note->title,
                    'category'       => $note->category,
                    'category_label' => $note->categoryLabel(),
                    'body'           => $note->body,
                    'note_date'      => $note->note_date?->toDateString(),
                    'pinned'         => $note->pinned,
                    'member_ids'     => $currentIds,
                    'members_label'  => $this->membersLabel($currentIds),
                    'member_name'    => $this->membersLabel($currentIds),
                ],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);

            return redirect()->route('notes.index')
                ->with('pending', 'تم إرسال طلب تعديل الملاحظة — بانتظار موافقة المسؤول.');
        }

        $note->update($data);
        $note->members()->sync($memberIds);
        ActivityLogger::log('updated', 'تعديل ملاحظة: ' . $this->noteLabel($note->load('members')), $note);

        return redirect()->route('notes.index')->with('success', 'تم تحديث الملاحظة بنجاح.');
    }

    public function destroy(Note $note)
    {
        $this->authorizeManage($note);
        $currentIds = $note->members()->pluck('members.id')->all();

        if (! $this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'note',
                'model_id'     => $note->id,
                'action'       => 'delete',
                'payload'      => [],
                'original'     => [
                    'title'          => $note->title,
                    'category_label' => $note->categoryLabel(),
                    'body'           => $note->body,
                    'note_date'      => $note->note_date?->toDateString(),
                    'members_label'  => $this->membersLabel($currentIds),
                    'member_name'    => $this->membersLabel($currentIds),
                ],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);

            return redirect()->route('notes.index')
                ->with('pending', 'تم إرسال طلب حذف الملاحظة — بانتظار موافقة المسؤول.');
        }

        $label = $this->noteLabel($note->load('members'));
        $note->delete();
        ActivityLogger::log('deleted', 'حذف ملاحظة: ' . $label);

        return redirect()->route('notes.index')->with('success', 'تم حذف الملاحظة.');
    }

    public function storeAttachment(Request $request, Note $note)
    {
        $this->authorizeManage($note);

        $request->validate([
            'attachments'   => 'required|array|max:20',
            'attachments.*' => 'file|max:' . self::ATTACHMENT_MAX_KB . '|mimes:' . self::ATTACHMENT_MIMES,
        ], [
            'attachments.required' => 'يرجى اختيار ملف واحد على الأقل.',
            'attachments.*.mimes'  => 'نوع ملف غير مدعوم.',
            'attachments.*.max'    => 'الحجم الأقصى لكل ملف هو 20 ميغابايت.',
        ]);

        $uploads = $this->storeUploads($request);

        if (! $this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'note_attachment',
                'action'       => 'create',
                'payload'      => [
                    'note_id'     => $note->id,
                    'note_label'  => $this->noteLabel($note->loadMissing('members')),
                    'attachments' => $uploads,
                ],
                'original'     => [],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);

            return back()->with('pending', 'تم إرسال طلب رفع الملفات — بانتظار موافقة المسؤول.');
        }

        foreach ($uploads as $row) {
            $note->attachments()->create($row);
        }
        ActivityLogger::log('created', 'رفع ' . count($uploads) . ' ملف للملاحظة: ' . $this->noteLabel($note->loadMissing('members')), $note);

        return back()->with('success', 'تم رفع الملفات بنجاح.');
    }

    public function destroyAttachment(NoteAttachment $attachment)
    {
        $note = $attachment->note;
        abort_if($note === null, 404);
        $this->authorizeManage($note);

        if (! $this->isAdmin()) {
            PendingChange::create([
                'model_type'   => 'note_attachment',
                'model_id'     => $attachment->id,
                'action'       => 'delete',
                'payload'      => [
                    'note_id'    => $note->id,
                    'note_label' => $this->noteLabel($note->loadMissing('members')),
                    'file_name'  => $attachment->file_name,
                    'file_path'  => $attachment->file_path,
                ],
                'original'     => ['file_name' => $attachment->file_name],
                'requested_by' => Auth::id(),
                'status'       => 'pending',
            ]);

            return back()->with('pending', 'تم إرسال طلب حذف الملف — بانتظار موافقة المسؤول.');
        }

        $name = $attachment->file_name;
        $attachment->delete();
        ActivityLogger::log('deleted', "حذف ملف من ملاحظة: {$name}");

        return back()->with('success', 'تم حذف الملف.');
    }

    public function downloadAttachment(NoteAttachment $attachment)
    {
        abort_unless(Storage::disk('public')->exists($attachment->file_path), 404);

        return Storage::disk('public')->download($attachment->file_path, $attachment->file_name);
    }

    /* ---------------------------------------------------------------------- */

    /**
     * @return array{title: ?string, category: ?string, body: string, note_date: ?string, pinned: bool}
     */
    private function validated(Request $request, bool $withAttachments, ?Note $existing = null): array
    {
        $rules = [
            'title'     => 'nullable|string|max:255',
            'category'  => ['nullable', Rule::in(array_keys(Note::CATEGORIES))],
            'body'      => 'required|string',
            'note_date' => 'nullable|date',
            'pinned'    => 'nullable|boolean',
            'people'    => 'nullable|array',
            'people.*'  => 'integer|exists:members,id',
        ];

        if ($withAttachments) {
            $rules['attachments']   = 'nullable|array|max:20';
            $rules['attachments.*'] = 'file|max:' . self::ATTACHMENT_MAX_KB . '|mimes:' . self::ATTACHMENT_MIMES;
        }

        $v = $request->validate($rules, [
            'body.required'       => 'يرجى كتابة نص الملاحظة.',
            'category.in'         => 'تصنيف غير معروف.',
            'attachments.max'     => 'الحد الأقصى 20 ملفاً في المرة الواحدة.',
            'attachments.*.mimes' => 'نوع ملف غير مدعوم.',
            'attachments.*.max'   => 'الحجم الأقصى لكل ملف هو 20 ميغابايت.',
        ]);

        return [
            'title'     => $v['title'] ?? null,
            'category'  => $v['category'] ?? null,
            'body'      => $v['body'],
            'note_date' => $v['note_date'] ?? $existing?->note_date?->toDateString() ?? now()->toDateString(),
            'pinned'    => $request->boolean('pinned'),
        ];
    }

    /** @return list<int> */
    private function cleanMemberIds(Request $request): array
    {
        return collect($request->input('people', []))
            ->filter(fn ($v) => is_numeric($v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();
    }

    private function membersLabel(array $memberIds): string
    {
        if (empty($memberIds)) {
            return 'بدون أشخاص';
        }

        return Member::whereIn('id', $memberIds)->orderBy('full_name')->pluck('full_name')->implode('، ');
    }

    private function noteLabel(Note $note): string
    {
        $members = $note->relationLoaded('members') ? $note->members : $note->members()->get();
        $who     = $members->isNotEmpty() ? $members->pluck('full_name')->implode('، ') : 'بدون أشخاص';

        return $note->title ? "{$note->title} — {$who}" : $who;
    }

    /**
     * Store uploaded files on the public disk and return metadata rows.
     *
     * @return array<int, array{file_path: string, file_name: string, file_size: int, mime_type: ?string, uploaded_by: ?int}>
     */
    private function storeUploads(Request $request): array
    {
        if (! $request->hasFile('attachments')) {
            return [];
        }

        $rows = [];
        foreach ($request->file('attachments') as $file) {
            if (! $file->isValid()) {
                continue;
            }

            $rows[] = [
                'file_path'   => $file->storeAs(
                    'notes/' . now()->format('Y-m'),
                    Str::uuid() . '.' . $file->getClientOriginalExtension(),
                    'public'
                ),
                'file_name'   => $file->getClientOriginalName(),
                'file_size'   => $file->getSize(),
                'mime_type'   => $file->getMimeType(),
                'uploaded_by' => Auth::id(),
            ];
        }

        return $rows;
    }
}
