<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $complaints = Complaint::with('user')
            ->when(in_array($status, ['baru', 'diproses', 'selesai'], true), fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $counts = [
            'baru' => Complaint::where('status', 'baru')->count(),
            'diproses' => Complaint::where('status', 'diproses')->count(),
            'selesai' => Complaint::where('status', 'selesai')->count(),
        ];

        return view('complaints.index', [
            'complaints' => $complaints,
            'counts' => $counts,
            'activeStatus' => $status,
        ]);
    }

    public function create()
    {
        return view('complaints.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'max:2000'],
        ], [
            'content.required' => 'Isi keluhan wajib diisi.',
        ]);

        $complaint = Complaint::create([
            'code' => $this->generateCode(),
            'customer_name' => $validated['customer_name'] ?? null,
            'content' => $validated['content'],
            'status' => 'baru',
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('complaints.show', $complaint)
            ->with('success', "Keluhan {$complaint->code} berhasil dicatat.");
    }

    public function show(Complaint $complaint)
    {
        $complaint->load('user');

        return view('complaints.show', ['complaint' => $complaint]);
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:baru,diproses,selesai'],
            'resolution' => ['nullable', 'string', 'max:2000'],
        ]);

        $complaint->status = $validated['status'];
        $complaint->resolution = $validated['resolution'] ?? $complaint->resolution;
        $complaint->resolved_at = $validated['status'] === 'selesai' ? now() : null;
        $complaint->save();

        return redirect()->route('complaints.show', $complaint)
            ->with('success', 'Status keluhan diperbarui.');
    }

    private function generateCode(): string
    {
        $prefix = 'KL-' . now()->format('ymd') . '-';
        $count = Complaint::whereDate('created_at', now()->toDateString())->count() + 1;

        return $prefix . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}