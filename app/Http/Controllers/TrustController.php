<?php

namespace App\Http\Controllers;

use App\Models\TrustSection;
use Illuminate\Http\Request;

class TrustController extends Controller
{
    public function index($slug = null)
    {
        $sections = TrustSection::with('images')
            ->where('status', 1)
            ->orderBy('id')
            ->get();

        if ($sections->isEmpty()) {
            abort(404, 'No Trust sections found.');
        }

        $activeSection = $slug
            ? $sections->firstWhere('slug', $slug) ?? abort(404)
            : $sections->first();

        return view('frontend.trust.index', compact('sections', 'activeSection'));
    }
}
