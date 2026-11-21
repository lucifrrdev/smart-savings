<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        // Jika password di database belum di-hash (misal plain text dari phpMyAdmin atau seeder lama)
        if ($user && !Hash::isHashed($user->password)) {
            if ($user->password === $credentials['password']) {
                $user->password = Hash::make($credentials['password']);
                $user->save();

                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        }

        try {
            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('/dashboard');
            }
        } catch (\RuntimeException $e) {
            // Tangani jika hash di database tidak valid/rusak tapi plain text cocok
            if ($user && $user->password === $credentials['password']) {
                $user->password = Hash::make($credentials['password']);
                $user->save();

                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended('/dashboard');
            }

            throw ValidationException::withMessages([
                'username' => __('auth.failed'),
            ]);
        }

        throw ValidationException::withMessages([
            'username' => __('auth.failed'),
        ]);
    }

    public function showRegister(Request $request)
    {
        $role = $request->query('role', 'siswa');
        if (!in_array($role, ['siswa', 'wali_kelas'])) {
            $role = 'siswa';
        }
        return view('auth.register', compact('role'));
    }

    public function register(Request $request)
    {
        $roleType = $request->input('role_type', 'siswa');

        if ($roleType === 'wali_kelas') {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'class' => ['required', 'string', 'max:50'],
                'nip' => ['required', 'string', 'max:30', 'unique:users,nip'],
                'birth_date' => ['required', 'date'],
                'username' => ['required', 'string', 'max:255', 'unique:users,username'],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'
                ],
            ], [
                'class.required' => 'Wali kelas berapa wajib dipilih/diisi.',
                'nip.required' => 'NIP wajib diisi.',
                'nip.unique' => 'NIP ini sudah terdaftar.',
                'password.regex' => 'Password harus minimal 8 karakter dan mengandung setidaknya 1 huruf besar, 1 huruf kecil, 1 angka, dan 1 karakter khusus (@$!%*?&#).',
            ]);

            $user = User::create([
                'name' => $request->name,
                'class' => $request->class,
                'nip' => $request->nip,
                'nisn' => null,
                'birth_date' => $request->birth_date,
                'username' => $request->username,
                'role' => 'admin', // Otomatis menjadi admin
                'password' => Hash::make($request->password),
            ]);
        } else {
            $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'class' => ['required', 'string', 'max:50'],
                'nisn' => ['required', 'string', 'max:20', 'unique:users,nisn'],
                'birth_date' => ['required', 'date'],
                'username' => ['required', 'string', 'max:255', 'unique:users,username'],
                'password' => [
                    'required',
                    'string',
                    'min:8',
                    'confirmed',
                    'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'
                ],
            ], [
                'class.required' => 'Kelas wajib dipilih.',
                'nisn.required' => 'NISN wajib diisi.',
                'nisn.unique' => 'NISN ini sudah terdaftar.',
                'password.regex' => 'Password harus minimal 8 karakter dan mengandung setidaknya 1 huruf besar, 1 huruf kecil, 1 angka, dan 1 karakter khusus (@$!%*?&#).',
            ]);

            $user = User::create([
                'name' => $request->name,
                'class' => $request->class,
                'nisn' => $request->nisn,
                'nip' => null,
                'birth_date' => $request->birth_date,
                'username' => $request->username,
                'role' => 'siswa', // Otomatis menjadi role siswa
                'password' => Hash::make($request->password),
            ]);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
