<?php

namespace App\Http\Controllers;

use App\Models\ServiceCategory;
use App\Models\ServiceRequest;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'categories' => ServiceCategory::where('is_active', true)->orderBy('name')->get(),
            'stats' => [
                'total'      => ServiceRequest::count(),
                'resolved'   => ServiceRequest::where('status', 'resolved')->count(),
                'categories' => ServiceCategory::where('is_active', true)->count(),
            ],
        ]);
    }
}
