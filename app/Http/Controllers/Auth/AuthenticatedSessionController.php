<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * @param Request $request  The incoming HTTP request containing form input and uploaded files.
     * @return RedirectResponse
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        // Step 1: User ko authenticate karein (Email/Password check)
        $request->authenticate();

        // Step 2: Authenticated user ko fetch karein
        $user = Auth::user();

        // Step 3: Check karein ki user ke paas koi role hai ya nahi
        if ($user->roles->isEmpty()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Your account is not authorized to access this panel.',
            ]);
        }

        // ---- YEH HAI NAYA DEBUG LOG ----
        // Step 4: User ke roles aur permissions ko log karein
        Log::info('User Authentication Successful:', [
            'user_id' => $user->id,
            'email' => $user->email,
            'roles' => $user->getRoleNames(), // User ke saare roles dikhayega
            'permissions' => $user->getPermissionNames() // Roles se milne waali saari permissions
        ]);
        // ---- DEBUG LOG END ----
        // dd($user->getRoleNames(), $user->getAllPermissions());

        // Step 5: Session regenerate karein
        $request->session()->regenerate();
        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Handle an incoming authentication request.
     * @param Request $request  The incoming HTTP request containing form input and uploaded files.
     * @return RedirectResponse
     */
    public function store_old(LoginRequest $request): RedirectResponse
    {

        $request->authenticate();

        $request->session()->regenerate();
        // Admin redirect
        if (Auth::user()->hasRole('admin')) {
            return redirect()->route('admin.dashboard'); // Admin dashboard route
        }


        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
