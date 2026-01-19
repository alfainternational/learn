<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function health()
    {
        return response()->json([
            'status' => 'ok',
            'app' => config('app.name'),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
