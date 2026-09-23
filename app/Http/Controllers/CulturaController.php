<?php

namespace App\Http\Controllers;

use App\Models\Cultura;
use Illuminate\View\View;

class CulturaController extends Controller
{
    public function index(): View
    {
        return view('culturas.index', [
            'culturas' => Cultura::orderBy('nome')->get(),
        ]);
    }
}
