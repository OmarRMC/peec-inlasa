<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private $user;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->user = User::where('username', $this->username)->first();
        if ($this->user) {
            $this->ensureIsNotRateLimited();
            if ($this->attemptLoginMd5()) {
                RateLimiter::clear($this->throttleKey());
                return;
            }
            if ($this->isBcryptOrArgon($this->user->password) && Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
                RateLimiter::clear($this->throttleKey());
                return;
            }
        }
        RateLimiter::hit($this->throttleKey());
        throw ValidationException::withMessages([
            'username' => trans('auth.failed'),
        ]);
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }

    protected function attemptLoginMd5()
    {
        $username = (string) $this->string('username');
        $plain    = (string) $this->string('password');
        $user = $this->user;
        $stored = (string) $user->password;
        $looksLikeMd5 = strlen($stored) === 32 && ctype_xdigit($stored);

        if ($looksLikeMd5 && hash_equals(md5($plain), $stored)) {
            $user->forceFill([
                'password' => $this->password,
            ])->save();
            Auth::login($user, $this->boolean('remember'));
            return true;
        }

        return false;
    }

    /**
     * Devuelve true si el hash tiene pinta de bcrypt/argon2
     */
    private function isBcryptOrArgon(string $hash): bool
    {
        return str_starts_with($hash, '$2y$')
            || str_starts_with($hash, '$2b$')
            || str_starts_with($hash, '$argon2id$')
            || str_starts_with($hash, '$argon2i$');
    }
}
