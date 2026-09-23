<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Máximo de tentativas de login por e-mail/IP antes do bloqueio temporário.
     */
    private const MAX_TENTATIVAS = 5;

    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credenciais = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $this->garantirQueNaoEstaBloqueado($request);

        if (! Auth::attempt($credenciais, $request->boolean('remember'))) {
            RateLimiter::hit($this->chaveDeBloqueio($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->chaveDeBloqueio($request));

        $request->session()->regenerate();

        return redirect()->intended(route('painel'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function garantirQueNaoEstaBloqueado(Request $request): void
    {
        $chave = $this->chaveDeBloqueio($request);

        if (! RateLimiter::tooManyAttempts($chave, self::MAX_TENTATIVAS)) {
            return;
        }

        event(new Lockout($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => RateLimiter::availableIn($chave),
            ]),
        ]);
    }

    private function chaveDeBloqueio(Request $request): string
    {
        return Str::transliterate(
            Str::lower((string) $request->input('email')).'|'.$request->ip()
        );
    }
}
