<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PainelController extends Controller
{
    public function __invoke(): View
    {
        return view('painel');
    }
}
