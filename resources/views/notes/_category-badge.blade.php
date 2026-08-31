{{-- Expects: $note --}}
@php
    // Literal class strings kept here so Tailwind's scanner always sees them.
    $__badge = [
        'blue'    => 'bg-blue-50 text-blue-700 border-blue-200',
        'indigo'  => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'emerald' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'red'     => 'bg-red-50 text-red-700 border-red-200',
        'violet'  => 'bg-violet-50 text-violet-700 border-violet-200',
        'amber'   => 'bg-amber-50 text-amber-700 border-amber-200',
        'gray'    => 'bg-gray-100 text-gray-600 border-gray-200',
    ];
    $__dot = [
        'blue'    => 'bg-blue-400',
        'indigo'  => 'bg-indigo-400',
        'emerald' => 'bg-emerald-400',
        'red'     => 'bg-red-400',
        'violet'  => 'bg-violet-400',
        'amber'   => 'bg-amber-400',
        'gray'    => 'bg-gray-400',
    ];
    $__c = $note->categoryColor();
@endphp
@if($note->category)
<span class="inline-flex items-center gap-1 text-[11px] font-bold rounded-full px-2 py-0.5 border {{ $__badge[$__c] ?? $__badge['gray'] }}">
    <span class="w-1.5 h-1.5 rounded-full {{ $__dot[$__c] ?? $__dot['gray'] }}"></span>
    {{ $note->categoryLabel() }}
</span>
@endif
