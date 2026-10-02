<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;

class LegalController extends Controller
{
    public function privacy()
    {
        return view('shop.legal.privacy', [
            'lastUpdated' => '2 October 2026',
        ]);
    }

    public function terms()
    {
        return view('shop.legal.terms', [
            'lastUpdated' => '2 October 2026',
        ]);
    }
}
