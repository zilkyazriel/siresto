<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MenuController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::orderBy('name')->get();

        $menus = Menu::with('category')
            ->when($request->filled('category'), function ($query) use ($request) {
                $query->whereHas('category', fn ($c) => $c->where('name', $request->category));
            })
            ->when($request->filled('q'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->q . '%');
            })
            ->orderBy('name')
            ->get();

        return view('menus.index', compact('menus', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $stocks = Stock::orderBy('name')->get(['id', 'name', 'unit']);

        return view('menus.create', compact('categories', 'stocks'));
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $ingredients = $this->extractIngredients($data);
        unset($data['image'], $data['ingredients']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('menus', 'public');
        }

        $data['is_available'] = $request->boolean('is_available');

        $menu = Menu::create($data);
        $this->syncIngredients($menu, $ingredients);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil ditambahkan.');
    }

    public function edit(Menu $menu)
    {
        $categories = Category::orderBy('name')->get();
        $stocks = Stock::orderBy('name')->get(['id', 'name', 'unit']);
        $menu->load('ingredients');

        return view('menus.edit', compact('menu', 'categories', 'stocks'));
    }

    public function update(Request $request, Menu $menu)
    {
        $data = $this->validateData($request);
        $ingredients = $this->extractIngredients($data);
        unset($data['image'], $data['ingredients']);

        if ($request->hasFile('image')) {
            if ($menu->image_path) {
                Storage::disk('public')->delete($menu->image_path);
            }
            $data['image_path'] = $request->file('image')->store('menus', 'public');
        }

        $data['is_available'] = $request->boolean('is_available');

        $menu->update($data);
        $this->syncIngredients($menu, $ingredients);

        return redirect()->route('menus.index')->with('success', 'Menu berhasil diperbarui.');
    }

    public function destroy(Menu $menu)
    {
        if ($menu->image_path) {
            Storage::disk('public')->delete($menu->image_path);
        }

        $menu->delete(); // resep ikut terhapus otomatis (cascade)

        return redirect()->route('menus.index')->with('success', 'Menu berhasil dihapus.');
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'max:5120'],
            'ingredients' => ['nullable', 'array'],
            'ingredients.*.stock_id' => ['nullable', 'exists:stocks,id'],
            'ingredients.*.quantity' => ['nullable', 'numeric', 'min:0'],
        ]);
    }

    /**
     * Ambil baris resep yang valid (punya bahan & jumlah > 0),
     * buang baris kosong, dan hilangkan bahan duplikat (ambil yang terakhir).
     */
    private function extractIngredients(array $data): array
    {
        return collect($data['ingredients'] ?? [])
            ->filter(fn ($row) => ! empty($row['stock_id']) && (float) ($row['quantity'] ?? 0) > 0)
            ->keyBy('stock_id')
            ->map(fn ($row, $stockId) => [
                'stock_id' => (int) $stockId,
                'quantity' => (float) $row['quantity'],
            ])
            ->values()
            ->all();
    }

    private function syncIngredients(Menu $menu, array $ingredients): void
    {
        $menu->ingredients()->delete();

        foreach ($ingredients as $row) {
            $menu->ingredients()->create($row);
        }
    }
}