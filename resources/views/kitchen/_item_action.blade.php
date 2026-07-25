@php $st = $it['status'] ?? 'antri'; @endphp
@if ($st === 'antri')
    <form method="POST" action="{{ route('kitchen.itemStatus', $it['id']) }}" class="shrink-0">
        @csrf
        <input type="hidden" name="status" value="dimasak">
        <button type="submit" class="flex items-center gap-1 rounded-lg bg-amber-600 px-3 py-1 text-xs font-semibold text-white transition-colors hover:bg-amber-500 active:scale-95">
            <span class="material-symbols-outlined text-[16px]">skillet</span> Masak
        </button>
    </form>
@elseif ($st === 'dimasak')
    <form method="POST" action="{{ route('kitchen.itemStatus', $it['id']) }}" class="shrink-0">
        @csrf
        <input type="hidden" name="status" value="siap">
        <button type="submit" class="flex items-center gap-1 rounded-lg bg-blue-600 px-3 py-1 text-xs font-semibold text-white transition-colors hover:bg-blue-500 active:scale-95">
            <span class="material-symbols-outlined text-[16px]">check</span> Siap
        </button>
    </form>
@else
    <span class="flex shrink-0 items-center gap-1 rounded-lg bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-400">
        <span class="material-symbols-outlined text-[16px]">check_circle</span> Siap
    </span>
@endif