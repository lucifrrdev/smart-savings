<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\SimpleXlsxExporter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Membangun query transaksi sesuai peran user dan filter pencarian.
     */
    private function getTransactionsQuery(Request $request)
    {
        $user = auth()->user();
        $isSiswa = $user && $user->role === 'siswa';
        $isAdmin = $user && $user->role === 'admin';
        $managedGrade = $isAdmin ? $user->getAssignedClassGrade() : null;

        $query = Transaction::with('user')
            ->where('type', 'deposit');

        if ($isSiswa) {
            $query->where('user_id', $user->id);
        } elseif ($isAdmin) {
            // Batasi data transaksi hanya untuk siswa dari kelas yang diampu
            $query->whereHas('user', function ($q) use ($managedGrade, $user) {
                if ($managedGrade) {
                    $q->where(function ($sub) use ($managedGrade, $user) {
                        $sub->where('class', 'like', "%{$managedGrade}%")
                            ->orWhere('class', 'like', "%{$user->class}%");
                    });
                } else {
                    $q->where('class', 'like', "%{$user->class}%");
                }
            });
        } elseif ($user && $user->role === 'superadmin') {
            // Khusus Superadmin: bisa memilih kelas tertentu yang ingin ditampilkan / diexport
            if ($request->filled('class_filter') && $request->input('class_filter') !== 'all') {
                $classFilter = $request->input('class_filter');
                $query->whereHas('user', function ($q) use ($classFilter) {
                    $q->where('class', 'like', "%{$classFilter}%");
                });
            }
        }

        // Filter pencarian nama/kelas
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($q) use ($search) {
                $q->where(function ($innerQuery) use ($search) {
                    $innerQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('class', 'like', "%{$search}%");
                });
            });
        }

        // Filter tanggal / rentang tanggal
        $exportMode = $request->input('export_mode');

        if ($exportMode === 'today' || $request->input('date_type') === 'today') {
            $query->whereDate('transaction_date', Carbon::today());
        } elseif ($request->filled('start_date') && $request->filled('end_date') && $exportMode !== 'all') {
            $query->whereBetween('transaction_date', [$request->input('start_date'), $request->input('end_date')]);
        } elseif ($request->filled('start_date') && $exportMode !== 'all') {
            $query->whereDate('transaction_date', '>=', $request->input('start_date'));
        } elseif ($request->filled('end_date') && $exportMode !== 'all') {
            $query->whereDate('transaction_date', '<=', $request->input('end_date'));
        } elseif ($request->filled('date') && $exportMode !== 'all') {
            $query->whereDate('transaction_date', $request->input('date'));
        }

        return $query;
    }

    public function index(Request $request)
    {
        $transactions = $this->getTransactionsQuery($request)
            ->latest('transaction_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('transactions.index', compact('transactions'));
    }

    public function export(Request $request)
    {
        $user = auth()->user();
        $transactions = $this->getTransactionsQuery($request)
            ->latest('transaction_date')
            ->latest('id')
            ->get();

        $headers = [
            'No',
            'Tanggal Masuk',
            'NISN',
            'Nama Siswa',
            'Kelas',
            'Kategori',
            'Deskripsi',
            'Jumlah (Rp)',
        ];

        $rows = [];
        $totalAmount = 0;

        foreach ($transactions as $index => $transaction) {
            $amount = (float) $transaction->amount;
            $totalAmount += $amount;

            $rows[] = [
                $index + 1,
                Carbon::parse($transaction->transaction_date)->format('d/m/Y'),
                $transaction->user->nisn ?? '-',
                $transaction->user->name ?? 'IoT Guest',
                $transaction->user->class ?? '-',
                $transaction->category ?? 'Tabungan',
                $transaction->description ?? '-',
                $amount,
            ];
        }

        // Tentukan label tanggal untuk nama file dan judul sheet
        $dateLabel = 'semua-tanggal';
        $sheetDateInfo = '';

        $exportMode = $request->input('export_mode');
        if ($exportMode === 'today' || $request->input('date_type') === 'today') {
            $today = Carbon::today();
            $dateLabel = 'hari-ini-' . $today->format('Y-m-d');
            $sheetDateInfo = ' (' . $today->format('d M Y') . ')';
        } elseif ($request->filled('start_date') && $request->filled('end_date') && $exportMode !== 'all') {
            $start = Carbon::parse($request->input('start_date'));
            $end = Carbon::parse($request->input('end_date'));
            $dateLabel = $start->format('Y-m-d') . '_sd_' . $end->format('Y-m-d');
            $sheetDateInfo = ' (' . $start->format('d/m') . '-' . $end->format('d/m/Y') . ')';
        } elseif ($request->filled('date') && $exportMode !== 'all') {
            $d = Carbon::parse($request->input('date'));
            $dateLabel = $d->format('Y-m-d');
            $sheetDateInfo = ' (' . $d->format('d M Y') . ')';
        }

        if ($user && $user->role === 'admin') {
            $classLabel = Str::slug($user->assigned_class_label ?: 'admin');
            $filename = "riwayat-transaksi-{$classLabel}-{$dateLabel}.xlsx";
            $sheetTitle = "Transaksi " . ($user->assigned_class_label ?: 'Kelas') . $sheetDateInfo;
        } elseif ($user && $user->role === 'siswa') {
            $userName = Str::slug($user->name);
            $filename = "riwayat-transaksi-{$userName}-{$dateLabel}.xlsx";
            $sheetTitle = "Tabungan " . mb_substr($user->name, 0, 10) . $sheetDateInfo;
        } else {
            // Khusus Superadmin: sesuaikan nama file dan judul jika memfilter kelas tertentu
            if ($request->filled('class_filter') && $request->input('class_filter') !== 'all') {
                $classVal = $request->input('class_filter');
                $classSlug = is_numeric($classVal) ? "kelas-{$classVal}" : Str::slug($classVal);
                $className = is_numeric($classVal) ? "Kelas {$classVal}" : $classVal;
                $filename = "riwayat-transaksi-{$classSlug}-{$dateLabel}.xlsx";
                $sheetTitle = "Transaksi {$className}" . $sheetDateInfo;
            } else {
                $filename = "riwayat-transaksi-semua-kelas-{$dateLabel}.xlsx";
                $sheetTitle = "Semua Transaksi" . $sheetDateInfo;
            }
        }

        $xlsxContent = SimpleXlsxExporter::create($sheetTitle, $headers, $rows, $totalAmount);

        return response($xlsxContent, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => strlen($xlsxContent),
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
