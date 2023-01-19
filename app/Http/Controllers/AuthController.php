<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->all(['email','password']);

        $token = auth('api')->attempt($credentials);

        if(!$token)
        {
            return response()->json(['erro'=>'Usuário ou senha inválidos'], 403);
        }

        return response()->json(['token'=>$token], 200);
    }

    public function logout()
    {
        return 'logout';
    }

    public function refresh()
    {
        return 'refresh';
    }

    public function me()
    {
        return 'me';
    }
}
