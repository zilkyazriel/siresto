<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    /**
     * Tanggal transaksi: pakai paid_at, fallback ke created_at bila kosong.
     */
    private const DATE_EXPR = 'COALESCE(payments.paid_at, payments.created_at)';

    public function index(Request $request)
    {
        $period = $this->period($request);
        [$start, $end] = $this->range($period);
        [$prevStart, $prevEnd] = $this->previousRange($period, $start);

        $current = $this->metrics($start, $end);
        $previous = $this->metrics($prevStart, $prevEnd);

        $revenue = $current['revenue'];
        $count = $current['count'];
        $avg = $count > 0 ? $revenue / $count : 0;
        $prevAvg = $previous['count'] > 0 ? $previous['revenue'] / $previous['count'] : 0;

        $revenueGrowth = $this->growth($revenue, $previous['revenue']);
        $countGrowth = $this->growth($count, $previous['count']);
        $avgGrowth = $this->growth($avg, $prevAvg);

        [$trendLabels, $trendValues] = $this->trend($period, $start, $end);

        $transactions = Payment::with('order.diningTable')
            ->whereRaw(self::DATE_EXPR . ' >= ?', [$start])
            ->whereRaw(self::DATE_EXPR . ' <= ?', [$end])
            ->orderByRaw(self::DATE_EXPR . ' DESC')
            ->paginate(10)
            ->withQueryString();

        return view('reports.index', compact(
            'period',
            'revenue',
            'count',
            'avg',
            'revenueGrowth',
            'countGrowth',
            'avgGrowth',
            'trendLabels',
            'trendValues',
            'transactions',
        ));
    }

    public function export(Request $request)
    {
        $period = $this->period($request);
        [$start, $end] = $this->range($period);

        $payments = Payment::with('order.diningTable')
            ->whereRaw(self::DATE_EXPR . ' >= ?', [$start])
            ->whereRaw(self::DATE_EXPR . ' <= ?', [$end])
            ->orderByRaw(self::DATE_EXPR . ' DESC')
            ->get();

        $filename = 'laporan-pendapatan-' . $period . '-' . now()->format('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($payments) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Tanggal', 'Kode Pesanan', 'Meja', 'Metode Bayar', 'Total']);

            foreach ($payments as $p) {
                $date = $p->paid_at ?? $p->created_at;
                $tableNo = $p->order?->diningTable?->number;
                fputcsv($out, [
                    optional($date)->format('Y-m-d H:i'),
                    $p->order?->code ?? '-',
                    $tableNo ? 'Meja ' . $tableNo : 'Takeaway',
                    $this->methodLabel($p->method),
                    $p->amount,
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function period(Request $request): string
    {
        $period = $request->query('period');

        return in_array($period, ['today', '7d', '30d'], true) ? $period : 'today';
    }

    private function range(string $period): array
    {
        $end = Carbon::now();
        $start = match ($period) {
            '7d' => Carbon::now()->subDays(6)->startOfDay(),
            '30d' => Carbon::now()->subDays(29)->startOfDay(),
            default => Carbon::now()->startOfDay(),
        };

        return [$start, $end];
    }

    private function previousRange(string $period, Carbon $start): array
    {
        $days = match ($period) {
            '7d' => 7,
            '30d' => 30,
            default => 1,
        };

        return [
            (clone $start)->subDays($days),
            (clone $start)->subSecond(),
        ];
    }

    private function metrics(Carbon $start, Carbon $end): array
    {
        $row = Payment::whereRaw(self::DATE_EXPR . ' >= ?', [$start])
            ->whereRaw(self::DATE_EXPR . ' <= ?', [$end])
            ->selectRaw('COALESCE(SUM(amount), 0) as revenue, COUNT(*) as cnt')
            ->first();

        return [
            'revenue' => (float) ($row->revenue ?? 0),
            'count' => (int) ($row->cnt ?? 0),
        ];
    }

    private function growth(float $current, float $previous): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return (($current - $previous) / $previous) * 100;
    }

    private function trend(string $period, Carbon $start, Carbon $end): array
    {
        if ($period === 'today') {
            $totals = Payment::whereRaw(self::DATE_EXPR . ' >= ?', [$start])
                ->whereRaw(self::DATE_EXPR . ' <= ?', [$end])
                ->selectRaw('HOUR(' . self::DATE_EXPR . ') as bucket, SUM(amount) as total')
                ->groupBy('bucket')
                ->pluck('total', 'bucket');

            $labels = [];
            $values = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $values[] = round((float) ($totals[$h] ?? 0), 2);
            }

            return [$labels, $values];
        }

        $days = $period === '30d' ? 30 : 7;

        $totals = Payment::whereRaw(self::DATE_EXPR . ' >= ?', [$start])
            ->whereRaw(self::DATE_EXPR . ' <= ?', [$end])
            ->selectRaw('DATE(' . self::DATE_EXPR . ') as bucket, SUM(amount) as total')
            ->groupBy('bucket')
            ->pluck('total', 'bucket');

        $labels = [];
        $values = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $day = Carbon::now()->subDays($i)->startOfDay();
            $labels[] = $day->format('d M');
            $values[] = round((float) ($totals[$day->format('Y-m-d')] ?? 0), 2);
        }

        return [$labels, $values];
    }

    private function methodLabel(?string $method): string
    {
        return match ($method) {
            'cash', 'tunai' => 'Tunai',
            'qris' => 'QRIS',
            'debit' => 'Debit',
            'card', 'kartu' => 'Kartu',
            null, '' => '-',
            default => ucfirst($method),
        };
    }
}
