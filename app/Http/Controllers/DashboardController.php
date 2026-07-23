<?php

namespace App\Http\Controllers;

use App\Models\DiningTable;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Stock;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today();

        // KPI 1 — Pendapatan hari ini (pakai paid_at, fallback created_at)
        $revenueToday = (float) Payment::whereRaw(
            'DATE(COALESCE(payments.paid_at, payments.created_at)) = ?',
            [$today->toDateString()]
        )->sum('amount');

        // KPI 2 — Pesanan aktif (hari ini, bukan batal, belum lunas)
        $activeOrders = Order::whereDate('created_at', $today)
            ->where('status', '!=', 'batal')
            ->whereDoesntHave('payment')
            ->count();

        // KPI 3 — Stok menipis / habis
        $lowStockCount = Stock::whereColumn('quantity', '<=', 'min_quantity')->count();

        // KPI 4 — Meja terisi
        $occupiedTables = DiningTable::where('status', 'terisi')->count();
        $totalTables = DiningTable::count();

        // Pesanan terbaru hari ini
        $recentOrders = Order::with(['diningTable', 'payment'])
            ->whereDate('created_at', $today)
            ->latest()
            ->take(8)
            ->get();

        // Daftar stok yang perlu perhatian
        $lowStockItems = Stock::whereColumn('quantity', '<=', 'min_quantity')
            ->orderBy('quantity')
            ->take(6)
            ->get();

        return view('dashboard', compact(
            'revenueToday',
            'activeOrders',
            'lowStockCount',
            'occupiedTables',
            'totalTables',
            'recentOrders',
            'lowStockItems',
        ));
    }
}