<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        if (auth()->user()->role == 'super_admin') {
            return view('super_admin.reports.index');
        }
        return view('admin.reports.index');
    }
}
