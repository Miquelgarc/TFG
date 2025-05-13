<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\User;
use App\Models\Role;

class AfiliatController extends Controller
{
    // InfoAfiliatController.php
    public function index()
    {
        $user = auth()->user();
        return Inertia::render('InfoAfiliat', [
            'auth' => [
                'user' => $user,
            ],
        ]);
    }

}
