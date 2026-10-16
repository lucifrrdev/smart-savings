<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isSiswa = $user && $user->role === 'siswa';

        $query = Transaction::with('user')
            ->where('type', 'deposit');

        if ($isSiswa) {
            $query->where('user_id', $user->id);
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

        // Filter pencarian tanggal
        if ($request->filled('date')) {
            $query->whereDate('transaction_date', $request->input('date'));
        }

        $transactions = $query->latest('transaction_date')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('transactions.index', compact('transactions'));
    }
}
