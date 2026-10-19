<?php

namespace App\Http\Controllers;

use App\Models\ServiceRequest;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    /** Public tracking page: shows status + public timeline only (no personal data). */
    public function index(Request $request)
    {
        $found = null;

        if ($request->filled('ref')) {
            $request->validate(['ref' => ['string', 'max:30']]);

            $found = ServiceRequest::with([
                'category',
                'updates' => fn ($q) => $q->where('is_internal', false)->oldest(),
            ])->where('reference_no', strtoupper(trim($request->ref)))->first();
        }

        return view('track', ['found' => $found, 'ref' => $request->ref]);
    }
}
