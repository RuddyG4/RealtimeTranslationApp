<?php

namespace App\Http\Controllers\Auth;

use App\Enums\UserState;
use App\Events\UserStateChanged;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => bcrypt($request->input('password')),
            'language_id' => $request->input('language_id'),
        ]);
        Auth::login($user);

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('web')->plainTextToken,
        ]);
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        if (!Auth::attempt($credentials)) {
            abort(401, 'Invalid credentials');
        }
        $request->session()->regenerate();

        $user = User::with('language')->find(auth()->user()->id);
        $user->updateUserState(UserState::ONLINE);
        return response()->json([
            'user' => $user,
        ]);
    }

    public function mobileLogin(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8',
            'device_name' => 'required',
        ]);

        $user = User::where('email', $request->email)
            ->with('language')
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'The provided credentials are incorrect.',
            ], 401);
        }
        $user->updateUserState(UserState::ONLINE);

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => $user,
        ]);
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): \Illuminate\Http\RedirectResponse
    {
        $user = User::find(auth()->user()->id);
        $user->updateUserState(UserState::OFFLINE);

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Log the user out of the mobile application.
     */
    public function mobileLogout(Request $request): JsonResponse
    {
        $user = User::find(auth()->user()->id);
        // $user->currentAccessToken()->delete();
        $user->tokens()->delete();
        $user->updateUserState(UserState::OFFLINE);

        return response()->json([
            'token' => $user->createToken($request->device_name)->plainTextToken,
            'user' => $user,
        ]);
    }
}
