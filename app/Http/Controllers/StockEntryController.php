<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\StockEntry;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockEntryController extends Controller
{
    public function index()
    {
        $entries = StockEntry::with(['supplier', 'user'])
            ->withCount('items')
            ->latest()
            ->paginate(15);

        return view('stock-entries.index', compact('entries'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get(['id', 'name']);
        $stocks = Stock::orderBy('name')->get(['id', 'name', 'unit']);

        return view('stock-entries.create', compact('suppliers', 'stocks'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'note' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.stock_id' => ['nullable', 'exists:stocks,id'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0'],
            'items.*.price' => ['nullable', 'numeric', 'min:0'],
        ], [
            'items.required' => 'Tambahkan minimal satu bahan yang masuk.',
            'items.min' => 'Tambahkan minimal satu bahan yang masuk.',
        ]);

        // Ambil baris valid (punya bahan & qty > 0), gabungkan bahan yang sama.
        $rows = collect($validated['items'])
            ->filter(fn ($r) => ! empty($r['stock_id']) && (float) ($r['quantity'] ?? 0) > 0)
            ->groupBy('stock_id')
            ->map(fn ($group, $stockId) => [
                'stock_id' => (int) $stockId,
                'quantity' => (float) $group->sum(fn ($r) => (float) $r['quantity']),
                'price' => (float) ($group->last()['price'] ?? 0),
            ])
            ->values();

        if ($rows->isEmpty()) {
            return back()->withInput()->with('error', 'Tidak ada bahan yang valid untuk dicatat.');
        }

        $entry = DB::transaction(function () use ($validated, $rows) {
            $total = 0;

            $entry = StockEntry::create([
                'code' => $this->generateCode(),
                'supplier_id' => $validated['supplier_id'] ?? null,
                'user_id' => auth()->id(),
                'note' => $validated['note'] ?? null,
                'total' => 0,
            ]);

            foreach ($rows as $row) {
                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;

                $entry->items()->create([
                    'stock_id' => $row['stock_id'],
                    'quantity' => $row['quantity'],
                    'price' => $row['price'],
                    'subtotal' => $subtotal,
                ]);

                // Barang masuk → stok bertambah (increment atomik, aman).
                Stock::whereKey($row['stock_id'])->increment('quantity', $row['quantity']);
            }

            $entry->update(['total' => $total]);

            return $entry;
        });

        return redirect()
            ->route('stock-entries.show', $entry)
            ->with('success', "Barang masuk {$entry->code} berhasil dicatat & stok diperbarui.");
    }

    public function show(StockEntry $stockEntry)
    {
        $stockEntry->load(['supplier', 'user', 'items.stock']);

        return view('stock-entries.show', compact('stockEntry'));
    }

    private function generateCode(): string
    {
        $prefix = 'BM-' . now()->format('ymd') . '-';
        $count = StockEntry::where('code', 'like', $prefix . '%')->count() + 1;

        return $prefix . str_pad((string) $count, 4, '0', STR_PAD_LEFT);
    }
}