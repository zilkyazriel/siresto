<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TableController extends Controller
{
    public function index()
    {
        $tables = DiningTable::orderBy('number')->get();

        return view('tables.index', compact('tables'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'number'   => ['required', 'string', 'max:255', Rule::unique('dining_tables', 'number')],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'status'   => ['required', Rule::in(['tersedia', 'terisi', 'kotor', 'reserved'])],
        ], $this->messages());

        DiningTable::create($data);

        return redirect()->route('tables.index')
            ->with('success', 'Meja "' . $data['number'] . '" berhasil ditambahkan.');
    }

    public function update(Request $request, DiningTable $table)
    {
        $data = $request->validate([
            'number'   => ['required', 'string', 'max:255', Rule::unique('dining_tables', 'number')->ignore($table->id)],
            'capacity' => ['required', 'integer', 'min:1', 'max:100'],
            'status'   => ['required', Rule::in(['tersedia', 'terisi', 'kotor', 'reserved'])],
        ], $this->messages());

        $table->update($data);

        return redirect()->route('tables.index')
            ->with('success', 'Meja "' . $data['number'] . '" berhasil diperbarui.');
    }

    public function destroy(DiningTable $table)
    {
        if ($table->status !== 'tersedia') {
            return redirect()->route('tables.index')
                ->with('error', 'Meja "' . $table->number . '" tidak bisa dihapus karena berstatus ' . $table->status . '.');
        }

        $number = $table->number;
        $table->delete();

        return redirect()->route('tables.index')
            ->with('success', 'Meja "' . $number . '" berhasil dihapus.');
    }

    /**
     * Pro-13: tandai meja sudah dibersihkan -> kembali tersedia.
     */
    public function markClean(DiningTable $table)
    {
        $table->markAvailable();

        return redirect()->route('tables.denah')
            ->with('success', 'Meja "' . $table->number . '" sudah dibersihkan & siap dipakai.');
    }

    private function messages(): array
    {
        return [
            'number.required'   => 'Nomor meja wajib diisi.',
            'number.unique'     => 'Nomor meja ini sudah digunakan.',
            'capacity.required' => 'Kapasitas wajib diisi.',
            'capacity.integer'  => 'Kapasitas harus berupa angka.',
            'capacity.min'      => 'Kapasitas minimal 1 orang.',
            'status.required'   => 'Status wajib dipilih.',
            'status.in'         => 'Status yang dipilih tidak valid.',
        ];
    }
    public function denah()
    {
    $tables = \App\Models\DiningTable::orderBy('number')->get();

    // Pesanan hari ini yang masih relevan (bukan batal) - untuk info tampilan kartu.
    $orders = \App\Models\Order::with(['items', 'payment'])
        ->whereDate('created_at', today())
        ->whereNotNull('dining_table_id')
        ->where('status', '!=', 'batal')
        ->latest()
        ->get();

    $activeByTable = [];
    foreach ($orders as $o) {
        if (! isset($activeByTable[$o->dining_table_id])) {
            $activeByTable[$o->dining_table_id] = $o;
        }
    }

    $cards = $tables->map(function ($t) use ($activeByTable) {
        $o = $activeByTable[$t->id] ?? null;

        return [
            'id'            => $t->id,
            'number'        => $t->number,
            'capacity'      => (int) $t->capacity,
            'status'        => $t->status ?? 'tersedia',
            'status_label'  => $t->status_label,
            'order_id'      => $o->id ?? null,
            'order_code'    => $o->code ?? null,
            'total_display' => $o ? 'Rp ' . number_format((float) $o->total, 0, ',', '.') : null,
            'item_count'    => $o ? (int) $o->items->sum('quantity') : 0,
        ];
    });

    $counts = [
        'tersedia' => $cards->where('status', 'tersedia')->count(),
        'terisi'   => $cards->where('status', 'terisi')->count(),
        'kotor'    => $cards->where('status', 'kotor')->count(),
        'reserved' => $cards->where('status', 'reserved')->count(),
    ];

    return view('tables.denah', compact('cards', 'counts'));
    }
}
