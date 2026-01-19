<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'login' => ['required', 'string'],
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
        $this->ensureIsNotRateLimited();

        $loginField = $this->input('login');
        $password = $this->input('password');
        $remember = $this->boolean('remember');

        // Determine the field type and find the user
        $user = null;
        $field = 'email';

        if (filter_var($loginField, FILTER_VALIDATE_EMAIL)) {
            // It's an email
            $field = 'email';
            $user = \App\Models\User::where('email', $loginField)->first();
        } elseif (preg_match('/^[0-9]{10,15}$/', preg_replace('/[^0-9]/', '', $loginField))) {
            // It's a phone number (remove non-numeric characters first)
            $field = 'phone';
            $cleanPhone = preg_replace('/[^0-9]/', '', $loginField);
            $user = \App\Models\User::where('phone', 'like', '%' . $cleanPhone . '%')->first();
        } else {
            // It's a username
            $field = 'username';
            $user = \App\Models\User::where('username', $loginField)->first();
        }

        if (! $user || ! Auth::attempt([$field => $user->$field, 'password' => $password], $remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'login' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
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
        return Str::transliterate(Str::lower($this->input('login')).'|'.$this->ip());
    }
}
