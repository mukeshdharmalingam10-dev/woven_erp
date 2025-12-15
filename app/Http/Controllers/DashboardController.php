<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the dashboard
     */
    public function index()
    {
        $user = auth()->user()->load(['role', 'entity']);
        
        return view('dashboard.index', compact('user'));
    }
}
