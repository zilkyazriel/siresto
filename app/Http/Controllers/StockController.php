<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $stocks = Stock::query()
            ->when($q !== '', fn ($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->get();

        // Ringkasan dihitung dari seluruh data agar akurat
        $all = Stock::all();
        $total = $all->count();
        $low = $all->filter(fn ($s) => $s->status === 'menipis')->count();
        $out = $all->filter(fn ($s) => $s->status === 'habis')->count();

        return view('stocks.index', compact('stocks', 'total', 'low', 'out', 'q'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('stocks', 'public');
        }

        Stock::create($data);

        return redirect()->route('stocks.index')->with('success', 'Bahan baku berhasil ditambahkan.');
    }

    public function update(Request $request, Stock $stock)
    {
        $data = $this->validateData($request);

        if ($request->hasFile('image')) {
            if ($stock->image_path) {
                Storage::disk('public')->delete($stock->image_path);
            }
            $data['image_path'] = $request->file('image')->store('stocks', 'public');
        }

        $stock->update($data);

        return redirect()->route('stocks.index')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    public function destroy(Stock $stock)
    {
        if ($stock->image_path) {
            Storage::disk('public')->delete($stock->image_path);
        }

        $stock->delete();

        return redirect()->route('stocks.index')->with('success', 'Bahan baku berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'quantity' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'min_quantity' => 'required|numeric|min:0',
            'image' => 'nullable|image|max:2048',
        ]);

        unset($validated['image']);

        return $validated;
    }
}
