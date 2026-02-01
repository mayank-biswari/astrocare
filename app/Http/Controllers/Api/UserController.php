<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function search(Request $request)
    {
        $term = $request->get('q', '');
        
        $users = User::where('name', 'like', "%{$term}%")
            ->orWhere('email', 'like', "%{$term}%")
            ->orderBy('name')
            ->limit(20)
            ->get(['id', 'name', 'email']);
        
        return response()->json([
            'results' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'text' => "{$user->name} ({$user->email})"
                ];
            })
        ]);
    }
}
