<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    /**
     * Display the homepage.
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        // Render the homepage view
        return view('frontend.home.index');
    }
}
