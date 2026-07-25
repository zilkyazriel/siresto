<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\Reservation;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $reservations = Reservation::with(['user', 'diningTable'])
            ->when(in_array($status, ['menunggu', 'hadir', 'selesai', 'batal'], true), fn ($q) => $q->where('status', $status))
            // Yang masih "menunggu" tampil dulu, lalu urut waktu reservasi terdekat.
            ->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->orderBy('reserved_at')
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'menunggu' => Reservation::where('status', 'menunggu')->count(),
            'hadir'    => Reservation::where('status', 'hadir')->count(),
            'selesai'  => Reservation::where('status', 'selesai')->count(),
            'batal'    => Reservation::where('status', 'batal')->count(),
        ];

        return view('reservations.index', [
            'reservations' => $reservations,
            'counts'       => $counts,
            'activeStatus' => $status,
        ]);
    }

    public function create()
    {
        $tables = DiningTable::orderBy('number')->get();

        return view('reservations.create', ['tables' => $tables]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name'   => ['required', 'string', 'max:255'],
            'customer_phone'  => ['nullable', 'string', 'max:30'],
            'dining_table_id' => ['nullable', 'exists:dining_tables,id'],
            'reserved_at'     => ['required', 'date'],
            'party_size'      => ['required', 'integer', 'min:1', 'max:100'],
            'note'            => ['nullable', 'string', 'max:500'],
        ], [
            'customer_name.required' => 'Nama pelanggan wajib diisi.',
            'reserved_at.required'   => 'Waktu reservasi wajib diisi.',
            'party_size.required'    => 'Jumlah orang wajib diisi.',
        ]);

        $reservation = Reservation::create([
            'code'            => $this->generateCode(),
            'customer_name'   => $validated['customer_name'],
            'customer_phone'  => $validated['customer_phone'] ?? null,
            'dining_table_id' => $validated['dining_table_id'] ?? null,
            'reserved_at'     => $validated['reserved_at'],
            'party_size'      => $validated['party_size'],
            'status'          => 'menunggu',
            'note'            => $validated['note'] ?? null,
            'user_id'         => auth()->id(),
        ]);

        return redirect()->route('reservations.show', $reservation)
            ->with('success', "Reservasi {$reservation->code} berhasil dicatat.");
    }

    public function show(Reservation $reservation)
    {
        $reservation->load(['user', 'diningTable']);

        return view('reservations.show', ['reservation' => $reservation]);
    }

    public function updateStatus(Request $request, Reservation $reservation)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:menunggu,hadir,selesai,batal'],
        ]);

        $reservation->status = $validated['status'];
        $reservation->save();

        return redirect()->route('reservations.show', $reservation)
            ->with('success', 'Status reservasi diperbarui.');
    }

    private function generateCode(): string
    {
        $prefix = 'RSV-' . now()->format('ymd') . '-';
        $count = Reservation::whereDate('created_at', now()->toDateString())->count() + 1;

        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}