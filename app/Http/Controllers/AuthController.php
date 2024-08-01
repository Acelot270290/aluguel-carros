<?php
namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Actions\AuthActions;
use App\Services\S3StorageService;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authActions;
    protected $s3StorageService;

    public function __construct(AuthActions $authActions, S3StorageService $s3StorageService)
    {
        $this->authActions = $authActions;
        $this->s3StorageService = $s3StorageService;
    }

    public function register(RegisterRequest $request)
    {
        $result = $this->authActions->register($request, $this->s3StorageService);
    
        if ($result instanceof \Illuminate\Http\JsonResponse) {
            return $result;
        }
    
        return response()->json($result, 201);
    }
    

    public function login(LoginRequest $request)
    {
        $result = $this->authActions->login($request);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function logout(Request $request)
    {
        $this->validate($request, ['token' => 'required']);

        $result = $this->authActions->logout($request->token);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }

    public function getAuthenticatedUser()
    {
        $result = $this->authActions->getAuthenticatedUser();
        return $result; 
    }
    public function listUsers()
    {
        $users = $this->authActions->listUsers();
        return response()->json($users);
    }

    public function updateUser(UpdateUserRequest $request, User $user)
    {
        $updatedUser = $this->authActions->updateUser($request, $user);
        return response()->json($updatedUser);
    }

    public function deleteUser($id)
    {
        $result = $this->authActions->deleteUser($id);

        if (isset($result['error'])) {
            return response()->json(['error' => $result['error']], $result['status']);
        }

        return response()->json($result);
    }
}
