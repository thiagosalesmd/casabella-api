<?php

namespace App\Http\Controllers;

use App\Models\Adresses;
use App\Models\Person;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        //$this->middleware('auth', ['except' => ['login', 'register']]);
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
            return response([
                'token' => $user->createToken('madeFy')->accessToken, 
                'user' => $user->person
            ], 401);
        }

        return response(['message' => 'usuaário e/ou senha inválidos'], 401);
    }

    public function register(Request $request)
    {
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'password' => 'required|min:6',
            'cpf' => 'required|numeric|min:11',
            'email' => 'required|unique:users'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(), 
                'message' => 'Desculpe, não foi possível cadastrar o usuário.'
            ], 400);
        }
/*
        if (!$this->validateCPF($request->cpf)) {
            return response()->json(['message' => 'Este CPF não é válido'], 400);
        }
*/
        $data = $request->all();

        $dataAddress = isset($data['address']) ? $data['address'] : [];
        $dataUser = array(
            'password' => bcrypt($data['password']),
            'name' => $data['name'],
            'email' => $data['email']
        );
        $dataPerson = array(
            'name' => $data['name'],
            'cpf' => $data['cpf'],
            'rg' => isset($data['rg']) ? $data['rg']: '',
            'phone' => isset($data['phone']) ? $data['phone'] : '',
            'is_whatsapp' => isset($data['is_whatsapp']) ? $data['is_whatsapp'] : false
        );
        
        try {
            if (sizeof($dataAddress) > 0) {
                if ( !isset($dataAddress['street']) ) {
                    return response()->json(['message' => 'Desculpe, ao informar o endereço é necessário que informe o nome da rua.'], 401);
                }
                $address = Adresses::create($dataAddress);
                $dataPerson['addres_id'] = $address->id;
            }

            $user = User::create($dataUser);
            $dataPerson['user_id'] = $user->id;
            
            $person = Person::create($dataPerson);
            
            $token = $user->createToken('madeFy')->accessToken;
            
            return response([ 'user' => $person, 'token' => $token]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Desculpe, houve um erro ao registar usuário.',
                'error' => $e->getMessage()
            ], 401);
        }
        
        
    }

    public function logout (Request $request) {
        //$token = $request->user()->token();
        $token = auth()->user()->accessToken;
        $token->revoke();
        return response()->json(['message' => 'Usuário deslogado']);
    }

    public function changePassword (Request $request)
    {
        if (Auth::guest()) {
            return response()->json(['message' => 'Usuário não autenticado'], 400);
        }
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()->all(), 
                'message' => 'Desculpe, não foi possível atualizar sua senha.'
            ], 400);
        }

        //if (Auth::attempt(['email' => $user->email, 'password' => $request->current_password]) ){
            
            $userUpdated = User::findOrFail($user->id);
            $userUpdated->password = bcrypt($request->new_password);
            try {
                $userUpdated->save();
                return response()->json(['message' => 'Senha Alterada com sucesso']);
            } catch (Exception $e) {
                return response()->json(['message' => $e->getMessage(), 400]);
            }
     //   }

        //return response()->json(['message' => 'Senha inválida'], 401);
        
    }

    public function me ()
    {
        $user = auth()->user();
        $person = $user->person;
        $person->address;
        $person->user = $user;

        return response()->json($person);
    }
}
