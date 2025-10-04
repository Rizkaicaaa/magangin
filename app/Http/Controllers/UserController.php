<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'desc')->get();
        return view('user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => ['required', Rule::in(['superadmin', 'admin', 'mahasiswa'])],
            'nim' => 'nullable|string|max:15|unique:users',
            'no_telp' => 'nullable|string|max:15',
            'status' => ['required', Rule::in(['aktif', 'non_aktif'])],
            'dinas_id' => 'nullable|integer',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);
        $data['tanggal_daftar'] = now()->toDateString();
        
        User::create($data);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request, $id)
    
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8',
            'role' => ['required', Rule::in(['superadmin', 'admin', 'mahasiswa'])],
            'nim' => ['nullable', 'string', 'max:15', Rule::unique('users')->ignore($user->id)],
            'no_telp' => 'nullable|string|max:15',
            'status' => ['required', Rule::in(['aktif', 'non_aktif'])],
            'dinas_id' => 'nullable|integer',
        ]);
        
        $data = $request->except('password');

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui!');
    }
    
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'User berhasil dihapus!');
    }
}