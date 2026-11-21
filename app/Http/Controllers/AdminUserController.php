<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminUserController extends Controller
{
    public function index()
    {
        // Tampilkan semua user kecuali user superadmin yang sedang login
        $users = User::where('id', '!=', Auth::id())
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(15);

        return view('users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $role = $request->input('role', 'siswa');

        // Normalisasi NISN / NIP jika terisi silang
        if ($role !== 'siswa' && empty($request->nip) && !empty($request->nisn)) {
            $request->merge(['nip' => $request->nisn, 'nisn' => null]);
        } elseif ($role === 'siswa' && empty($request->nisn) && !empty($request->nip)) {
            $request->merge(['nisn' => $request->nip, 'nip' => null]);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'class' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'role' => ['required', 'string', 'in:superadmin,admin,siswa'],
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'
            ],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'class.required' => $role === 'admin' ? 'Wali kelas berapa wajib dipilih.' : ($role === 'siswa' ? 'Kelas wajib dipilih.' : 'Penugasan/Kelas wajib diisi.'),
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
            'password.regex' => 'Password harus minimal 8 karakter dan mengandung setidaknya 1 huruf besar, 1 huruf kecil, 1 angka, dan 1 karakter khusus (@$!%*?&#).',
        ];

        if ($role === 'siswa') {
            $rules['nisn'] = ['required', 'string', 'max:20', 'unique:users,nisn'];
            $messages['nisn.required'] = 'NISN wajib diisi untuk siswa.';
            $messages['nisn.unique'] = 'NISN ini sudah terdaftar.';
        } elseif ($role === 'admin') {
            $rules['nip'] = ['required', 'string', 'max:30', 'unique:users,nip'];
            $messages['nip.required'] = 'NIP wajib diisi untuk Wali Kelas.';
            $messages['nip.unique'] = 'NIP ini sudah terdaftar.';
        } else {
            $rules['nip'] = ['nullable', 'string', 'max:30', 'unique:users,nip'];
            $messages['nip.unique'] = 'NIP ini sudah terdaftar.';
        }

        $validated = $request->validate($rules, $messages);
        $validated['password'] = Hash::make($validated['password']);

        if ($role === 'siswa') {
            $validated['nip'] = null;
        } else {
            $validated['nisn'] = null;
        }

        User::create($validated);

        return redirect()->back()->with('success', "User {$request->name} berhasil ditambahkan secara manual.");
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah data Anda sendiri.');
        }

        $role = $request->input('role', $user->role);

        if ($role !== 'siswa' && empty($request->nip) && !empty($request->nisn)) {
            $request->merge(['nip' => $request->nisn, 'nisn' => null]);
        } elseif ($role === 'siswa' && empty($request->nisn) && !empty($request->nip)) {
            $request->merge(['nisn' => $request->nip, 'nip' => null]);
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'class' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'role' => ['required', 'string', 'in:superadmin,admin,siswa'],
        ];

        $messages = [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username ini sudah digunakan.',
            'class.required' => 'Kelas / Penugasan wajib diisi.',
            'birth_date.required' => 'Tanggal lahir wajib diisi.',
        ];

        if ($role === 'siswa') {
            $rules['nisn'] = ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)];
            $messages['nisn.required'] = 'NISN wajib diisi untuk siswa.';
            $messages['nisn.unique'] = 'NISN ini sudah terdaftar.';
        } elseif ($role === 'admin') {
            $rules['nip'] = ['required', 'string', 'max:30', Rule::unique('users')->ignore($user->id)];
            $messages['nip.required'] = 'NIP wajib diisi untuk Wali Kelas.';
            $messages['nip.unique'] = 'NIP ini sudah terdaftar.';
        } else {
            $rules['nip'] = ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)];
            $messages['nip.unique'] = 'NIP ini sudah terdaftar.';
        }

        if ($request->filled('password')) {
            $rules['password'] = [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$/'
            ];
            $messages['password.min'] = 'Password minimal harus 8 karakter.';
            $messages['password.regex'] = 'Password harus minimal 8 karakter dan mengandung setidaknya 1 huruf besar, 1 huruf kecil, 1 angka, dan 1 karakter khusus (@$!%*?&#).';
        }

        $validated = $request->validate($rules, $messages);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        if ($role === 'siswa') {
            $validated['nip'] = null;
        } else {
            $validated['nisn'] = null;
        }

        $user->update($validated);

        return redirect()->back()->with('success', "Data user {$user->name} berhasil diperbarui.");
    }

    public function destroy(User $user)
    {
        // Cegah menghapus diri sendiri
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('success', "Akun {$user->name} berhasil dihapus.");
    }
}
