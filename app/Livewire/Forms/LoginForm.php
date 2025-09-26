<?php

namespace App\Livewire\Forms;

use Livewire\Form;
use App\Models\User;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class LoginForm extends Form
{
    #[Validate('required|string')]
    public string $username = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {

        $this->ensureIsNotRateLimited();

        $response = Http::withHeaders([
            'secret-key' => env('SSO_API_KEY'),
            'Accept' => 'application/json',
        ])->post(env('SSO_API_URL'), [
            'username' => $this->username,
            'password' => $this->password,
        ]);

        if ($response->successful() && $response->json()['status'] == true) {
            $ssoUserData = $response->json()['results']['data'];

            $photoUrl = $ssoUserData['photo'] ?? null;
            $localPhotoPath = null;

            // Periksa apakah URL foto valid
            if (filter_var($photoUrl, FILTER_VALIDATE_URL)) {
                try {
                    // Ambil konten gambar dari URL
                    $imageContents = file_get_contents($photoUrl);

                    if ($imageContents !== false) {
                        // Buat nama file unik berdasarkan username (nrk) dan ekstensi asli
                        $filename = $ssoUserData['nrk'] . '.' . pathinfo(parse_url($photoUrl, PHP_URL_PATH), PATHINFO_EXTENSION);
                        $path = 'avatars/' . $filename;

                        // Simpan gambar ke disk 'public' (storage/app/public/avatars)
                        Storage::disk('public')->put($path, $imageContents);

                        // Path ini yang akan disimpan ke database
                        $localPhotoPath = $path;
                    }
                } catch (\Exception $e) {
                    // Jika download gagal, catat error tapi jangan hentikan proses login
                    Log::warning('Gagal mengunduh foto pengguna dari SSO.', [
                        'username' => $ssoUserData['nrk'],
                        'photo_url' => $photoUrl,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            /*
            $localUser = User::updateOrCreate(
                ['username' => $ssoUserData['nrk']],
                [
                    'name' => $ssoUserData['nama'],
                    'email' => $ssoUserData['email'],
                    'photo' => $localPhotoPath,
                ]
            );
            */

            $userData = [
                'name' => $ssoUserData['nama'],
                'email' => $ssoUserData['email'],
            ];

            if ($localPhotoPath) {
                $userData['photo'] = $localPhotoPath;
            }

            $localUser = User::updateOrCreate(
                ['username' => $ssoUserData['nrk']],
                $userData
            );

            Auth::login($localUser, $this->remember);

            session([
                'sso_user_info' => [
                    'jabatan' => $ssoUserData['jabatan'] ?? 'Tidak ada',
                    'penugasan' => $ssoUserData['penugasan'] ?? 'Tidak ada',
                    'penempatan' => $ssoUserData['penempatan'] ?? [],
                ]
            ]);

            Log::info('SSO User Login Successful: Data saved to log.', [
                'username' => $localUser->username,
                'sso_data' => $ssoUserData
            ]);

            RateLimiter::clear($this->throttleKey());
            Session::regenerate();
            return;
        }

        $field = filter_var($this->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials[$field] = $this->username;
        $credentials['password'] = $this->password;


        if (Auth::attempt($credentials, $this->remember)) {
            Session::regenerate();
            RateLimiter::clear($this->throttleKey());
            return;
        }

        RateLimiter::hit($this->throttleKey());

        throw ValidationException::withMessages([
            'form.username' => __('auth.failed'),
        ]);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->username) . '|' . request()->ip());
    }
}
