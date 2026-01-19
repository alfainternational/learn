<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $transactions = $request->user()->transactions()
            ->latest()
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => $transactions
        ]);
    }
}
