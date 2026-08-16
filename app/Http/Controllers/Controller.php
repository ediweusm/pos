<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

abstract class Controller
{
    protected function requirePermission(Request $request, string $permission): void
    {
        $user = $request->user();

        abort_unless(
            $user && ($user->hasRole('super_admin') || $user->can($permission)),
            403
        );
    }
}
