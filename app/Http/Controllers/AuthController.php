<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;

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
}
