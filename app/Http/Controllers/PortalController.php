<?php

namespace App\Http\Controllers;

class PortalController extends Controller
{
    public function index()
    {
        return view('portal.index');
    }
}