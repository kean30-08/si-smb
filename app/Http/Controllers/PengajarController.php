<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PengajarController extends Controller
{
        public function index()
    {
        return view('pengajar.index');
    }
}
