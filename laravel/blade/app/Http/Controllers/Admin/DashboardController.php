<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;

class DashboardController extends AdminController implements HasMiddleware
{
    public static function middleware(): array
    {
        return static::permissionsFor('dashboard');
    }

    public function index()
    {
        return view('pages.admin.dashboard.index', [
            'title' => 'Dashboard',
        ]);
    }
}
