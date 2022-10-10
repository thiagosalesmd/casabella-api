<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'password' => 'required|min:6',
            'email' => 'required|unique:users'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(), 
                'message' => 'Desculpe, não foi possível cadastrar o usuário.'
            ], 400);
        }

        $data = $request->all();
        $data['password'] = bcrypt($data['password']);
        try {
            $user = User::create($data);
            $token = $user->createToken('madeFy')->accessToken;
            return response([ 'user' => $user, 'token' => $token]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Desculpe, houve um erro ao registar usuário.', 'error' => $e->getMessage()], 400);
        }
        
        
    }

    public function login(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(), 
                'message' => 'Desculpe, não foi possível cadastrar o usuário.'
            ], 400);
        }
        
        if (Auth::attempt($request->all())) {
            $user = Auth::user();
            return response(['token' => $user->createToken('madeFy')->accessToken, 'user' => $user], 401);
        }

        return response(['message' => 'usuaário e/ou senha inválidos'], 401);
    }

    public function logout (Request $request) {
        //$token = $request->user()->token();
        $token = auth()->user()->accessToken;
        $token->revoke();
        return response()->json(['message' => 'Usuário deslogado']);
    }
}

