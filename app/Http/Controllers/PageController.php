<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        // Try to find page by slug
        $page = Page::where('slug', $slug)->first();

        if (!$page) {
            abort(404, 'Page not found');
        }

        return view('frontend.pages.show', compact('page'));
    }
}
