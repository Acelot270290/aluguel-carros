<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class AuthActions
{
    public function register(RegisterRequest $request)
    {
        try {
            $imgUrl = null;
            if ($request->hasFile('img')) {

                $file = $request->file('img');
                $filename = date('YmdHis') . '_' . time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
                $destinationPath = public_path('usuarios');

                // Verifica se o diretório 'public/usuarios' existe, se não, cria
                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }

                try {
                    // Move o arquivo para a pasta 'public/usuarios'
                    $file->move($destinationPath, $filename);
                    $imgUrl = url('usuarios/' . $filename);
                } catch (\Exception $e) {
                    Log::error('File storage error', ['message' => $e->getMessage()]);
                    return response()->json(['error' => 'File storage error: ' . $e->getMessage()], 500);
                }
            }

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'email_verified_at' => now(),
                'password' => Hash::make($request->password),
                'remember_token' => Str::random(10),
                'zip_code' => $request->zip_code,
                'street' => $request->street,
                'number' => $request->number,
                'city' => $request->city,
                'neighborhood' => $request->neighborhood,
                'state' => $request->state,
                'img' => $imgUrl,
            ]);

            $token = JWTAuth::fromUser($user);

            return response()->json(compact('user', 'token'), 201);
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                return response()->json(['error' => 'Email já cadastrado'], 409);
            }
            return response()->json(['error' => 'Erro ao registrar usuário'], 500);
        }
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return ['error' => 'invalid_credentials', 'status' => 400];
            }
        } catch (JWTException $e) {
            return ['error' => 'could_not_create_token', 'status' => 500];
        }

        return compact('token');
    }

    public function logout($token)
    {
        try {
            JWTAuth::invalidate($token);
            return ['success' => 'User logged out successfully'];
        } catch (JWTException $exception) {
            return ['error' => 'Sorry, the user cannot be logged out', 'status' => 500];
        }
    }

    public function getAuthenticatedUser()
    {
        try {
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                return ['error' => 'user_not_found', 'status' => 404];
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return ['error' => 'token_expired', 'status' => $e->getStatusCode()];
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return ['error' => 'token_invalid', 'status' => $e->getStatusCode()];
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return ['error' => 'token_absent', 'status' => $e->getStatusCode()];
        }

        return compact('user');
    }

    public function listUsers()
    {
        try {
            $users = User::all();
            return $users;
        } catch (UserNotDefinedException $e) {
            return ['error' => 'User not authenticated', 'status' => 401];
        } catch (JWTException $e) {
            return ['error' => 'Token error: ' . $e->getMessage(), 'status' => 401];
        }
    }

    public function updateUser(UpdateUserRequest $request, User $user)
    {
        try {
            $user->name = $request->name;
            $user->email = $request->email;
            $user->zip_code = $request->zip_code;
            $user->street = $request->street;
            $user->number = $request->number;
            $user->city = $request->city;
            $user->neighborhood = $request->neighborhood;
            $user->state = $request->state;
            $user->img = $request->img;

            if ($request->password) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return $user;
        } catch (UserNotDefinedException $e) {
            return ['error' => 'User not authenticated', 'status' => 401];
        } catch (JWTException $e) {
            return ['error' => 'Token error: ' . $e->getMessage(), 'status' => 401];
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);

            $user->delete();
            return response()->json(['success' => 'User deleted successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'User not found'], 404);
        } catch (UserNotDefinedException $e) {
            return response()->json(['error' => 'User not authenticated'], 401);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token error: ' . $e->getMessage()], 401);
        }
    }
}
