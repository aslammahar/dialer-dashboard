<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        // 🔒 SECURITY: Check IP-based rate limiting first (prevents brute force from single IP)
        $this->ensureIpNotRateLimited();
        
        // Check email+IP based rate limiting
        $this->ensureIsNotRateLimited();

        // Session-only login: no "remember me" so user is logged out when browser/tab closes (SESSION_EXPIRE_ON_CLOSE)
        if (! Auth::attempt($this->only('email', 'password'), false)) {
            // Increment both rate limiters on failed login with proper decay times
            RateLimiter::hit($this->throttleKey(), 900); // 15 minutes (900 seconds)
            RateLimiter::hit($this->ipThrottleKey(), 1800); // 30 minutes (1800 seconds)
            
            // 🔒 SECURITY: Log failed login attempt
            Log::warning('Failed login attempt', [
                'email' => $this->input('email'),
                'ip' => $this->ip(),
                'user_agent' => $this->userAgent(),
                'attempts_email' => RateLimiter::attempts($this->throttleKey()),
                'attempts_ip' => RateLimiter::attempts($this->ipThrottleKey())
            ]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // Clear rate limiters on successful login
        RateLimiter::clear($this->throttleKey());
        RateLimiter::clear($this->ipThrottleKey());
        
        // 🔒 SECURITY: Log successful login
        Log::info('Successful login', [
            'email' => $this->input('email'),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent()
        ]);
    }

    /**
     * Ensure the login request is not rate limited (email+IP based).
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited()
    {
        // 🔒 SECURITY: Rate limiting - 10 attempts per email+IP, 15 minute lockout
        // Increased from 5 to 10 to reduce false positives for legitimate users
        $maxAttempts = 10;
        $decayMinutes = 15;
        
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $maxAttempts)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());
        
        // 🔒 SECURITY: Log lockout event
        Log::warning('Login account locked due to too many attempts', [
            'email' => $this->input('email'),
            'ip' => $this->ip(),
            'user_agent' => $this->userAgent(),
            'lockout_seconds' => $seconds,
            'throttle_key' => $this->throttleKey()
        ]);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }
    
    /**
     * Ensure the IP is not rate limited (IP-based brute force protection).
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIpNotRateLimited()
    {
        // 🔒 SECURITY: IP-based rate limiting - 50 attempts per IP, 30 minute lockout
        // This prevents brute force attacks from a single IP trying multiple accounts
        // Increased from 20 to 50 to reduce false positives in office/shared IP environments
        $maxAttempts = 50;
        $decayMinutes = 30;
        
        if (! RateLimiter::tooManyAttempts($this->ipThrottleKey(), $maxAttempts)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->ipThrottleKey());
        
        // 🔒 SECURITY: Log IP lockout event (potential brute force attack)
        Log::critical('IP locked due to too many login attempts (potential brute force attack)', [
            'ip' => $this->ip(),
            'email' => $this->input('email'),
            'user_agent' => $this->userAgent(),
            'lockout_seconds' => $seconds,
            'attempts' => RateLimiter::attempts($this->ipThrottleKey())
        ]);

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request (email+IP based).
     *
     * @return string
     */
    public function throttleKey()
    {
        return Str::lower($this->input('email')).'|'.$this->ip();
    }
    
    /**
     * Get the IP-based throttle key for brute force protection.
     *
     * @return string
     */
    public function ipThrottleKey()
    {
        return 'login|'.$this->ip();
    }
}
