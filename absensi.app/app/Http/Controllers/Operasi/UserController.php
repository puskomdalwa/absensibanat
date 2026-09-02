<?php

namespace App\Http\Controllers\Operasi;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function autocomplete($query)
    {
        return User::where('name', 'LIKE', "%$query%")
            ->select('id', 'name')
            ->limit(100)
            ->get()
            ->map(function ($user) {
                return [
                    'label' => $user->name,
                    'value' => $user->id
                ];
            });
    }
}
