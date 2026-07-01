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
            'status'   => ['required', Rule::in(['tersedia', 'terisi', 'reserved'])],
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
            'status'   => ['required', Rule::in(['tersedia', 'terisi', 'reserved'])],
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
}
