<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class LegalController extends Controller
{
    /**
     * Display the privacy policy page.
     */
    public function privacy(): View
    {
        return view("legal.privacy");
    }

    /**
     * Display the terms of service page.
     */
    public function terms(): View
    {
        return view("legal.terms");
    }
}
