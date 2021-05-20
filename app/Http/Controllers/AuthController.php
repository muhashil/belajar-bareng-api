<?php

namespace App\Http\Controllers;

use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPUnit\Framework\isEmpty;
use App\Models\User;
use App\Models\UserVerification;
use App\Utils\Helpers;
use Carbon\Carbon;
use Mailgun\Mailgun;

class AuthController extends Controller
{
    /**
     * Register new user
     * endpoint: /api/auth/register
     * 
     * @return \Illuminate\Http\JsonResponse
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
        
        // create new user
        try {
            $new_user = new User();
            $new_user->email = $email;
            $new_user->password = app('hash')->make($password);
            // $new_user->is_verified = true; // FIXME : set to true after email verification
            $new_user->save();
            
        } catch (\Exception $err) {
            return response()->json($data = ['message' => $err->getMessage(), 'action' => false], $status = 500);
        };

        // send email confirmation
        try {
            $code = Helpers::createRandomCode(50, ['Upper_Case', 'Lower_Case', 'Number']);
            
            $userVerification = new UserVerification();
            $userVerification->user_id = $new_user->id;
            $userVerification->expired_at = Carbon::tomorrow('Asia/Jakarta');
            $userVerification->code = $code;
            $userVerification->save();
            
            $baseUrl = "http://localhost";
            $confirmationLink = "{$baseUrl}/api/auth/verification/{$code}";
            $domain = config("service.mailgun.domain");
            $message = "Buka link ini untuk melakukan verifikasi email, {$confirmationLink}";
            
            $mgClient = Mailgun::create(config("service.mailgun.api_key"), config("service.mailgun.base_url"));
            $params = [
                "from"    => "Belajar Bareng <mailgun@{$domain}>",
                "to"      => $email,
                "subject" => "Verifikasi Email Belajar Bareng",
                "text"    => $message,
            ];

            $mgClient->messages()->send($domain, $params);

        } catch (\Exception $err) {
            return response()->json($data = ['message' => $err->getMessage(), 'action' => 'false'], $status = 500);
        }
        
        return response()->json($data = ['message' => 'Successfully register new user. Please check your email.', 'action' => true], $status = 201);
    }

    /**
     * Login user
     * endpoint: /api/auth/login
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {

        $email = $request->email;
        $password = $request->password;

        if (empty($email) or empty($password)) {
            return response()->json($data = ['message' => 'Please fill all required fields.', 'action' => false], $status = 400);
        }

        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid email address or password.', 'action' => false], 401);
        }

        $user = User::where('email', $email)->first();
        if (!$user->is_verified) {
            return response()->json($data = ['message' => 'User is not verified. Please check your email.', 'action' => false], $status = 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json($data = ['message' => 'Successfully logged out.', 'action' => true], $status = 200);
    }

    /**
     * Verify user email
     * Endpoint: /api/auth/verification
     */
    public function verification($code) {
        try {
            $userVerification = UserVerification::where('code', $code)->firstOrFail();
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $err) {
            return response()->json($data = ['message' => 'Verification code not found.', 'action' => false], $status = 400);
        }

        if ($userVerification->isExpired()) {
            return response()->json($data = ['message' => 'Verification code already expired.', 'action' => false], $status = 400);
        }

        $user = User::find($userVerification->user_id);
        $user->is_verified = true;
        $user->save();

        $userVerification->forceDelete();

        return response()->json($data = ['message' => 'User successfully verified.', 'action' => true], $status = 200);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token) {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
