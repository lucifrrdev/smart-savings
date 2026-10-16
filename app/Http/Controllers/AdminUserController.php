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
        $input = $request->all();
        if ($request->role !== 'siswa' && empty($input['nip']) && !empty($input['nisn'])) {
            $input['nip'] = $input['nisn'];
            $input['nisn'] = null;
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'class' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'role' => ['required', 'string', 'in:superadmin,admin,siswa'],
            'password' => ['required', 'string', 'min:6'],
        ];

        if ($request->role === 'siswa') {
            $rules['nisn'] = ['required', 'string', 'max:20', 'unique:users,nisn'];
        } else {
            $rules['nip'] = ['nullable', 'string', 'max:30', 'unique:users,nip'];
        }

        $validator = \Illuminate\Support\Facades\Validator::make($input, $rules);
        $validated = $validator->validate();
        $validated['password'] = Hash::make($request->password);

        User::create($validated);

        return redirect()->back()->with('success', "User {$request->name} berhasil ditambahkan secara manual.");
    }

    public function update(Request $request, User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah data Anda sendiri.');
        }

        $input = $request->all();
        if ($request->role !== 'siswa' && empty($input['nip']) && !empty($input['nisn'])) {
            $input['nip'] = $input['nisn'];
            $input['nisn'] = null;
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'class' => ['required', 'string', 'max:255'],
            'birth_date' => ['required', 'date'],
            'role' => ['required', 'string', 'in:superadmin,admin,siswa'],
        ];

        if ($request->role === 'siswa') {
            $rules['nisn'] = ['required', 'string', 'max:20', Rule::unique('users')->ignore($user->id)];
        } else {
            $rules['nip'] = ['nullable', 'string', 'max:30', Rule::unique('users')->ignore($user->id)];
        }

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:6'];
        }

        $validator = \Illuminate\Support\Facades\Validator::make($input, $rules);
        $validated = $validator->validate();

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
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
