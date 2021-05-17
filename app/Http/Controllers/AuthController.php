<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Register new user
     * endpoint: /api/auth/register
     */
    public function register(Request $request) {
        
        $email = $request->email;
        $password = $request->password;

        if (empty($email) or empty($password)) {
            return response()->json($data = ['message' => 'Please fill all required fields.', 'action' => false], $status = 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json($data = ['message' => 'Please input a valid email address.', 'action' => false], $status = 400);
        }

        if (User::where('email', $email)->exists()) {
            $message = sprintf('User with email "%s" already exists.', $email);
            return response()->json($data = ['message' => $message, 'action' => false], $status = 400);
        }

        try {
            $new_user = new User();
            $new_user->email = $email;
            $new_user->password = app('hash')->make($password);
            $new_user->is_verified = true; // FIXME : set to true after email verification
            $new_user->save();
            
        } catch (\Exception $err) {
            return response()->json($data = ['message' => $err->getMessage(), 'action' => false], $status = 500);
        };
        
        return response()->json($data = ['message' => 'Successfully register new user.', 'action' => true], $status = 201);
    }

    public function login(Request $request) {

        $email = $request->email;
        $password = $request->password;

        if (empty($email) or empty($password)) {
            return response()->json($data = ['message' => 'Please fill all required fields.', 'action' => false], $status = 400);
        }

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json($data = ['message' => 'Successfully logged out.', 'action' => true], $status = 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
