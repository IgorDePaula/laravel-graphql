<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $data = $this->validate($request, [
            'email' => ['email', 'unique:users'],
            'password' => ['min:6']
        ]);

        $user = \App\User::create($data);

        return response()->json(['user' => $user]);
    }
}
