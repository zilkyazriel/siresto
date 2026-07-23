<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    private array $roles = ['pemilik', 'pelayan', 'koki', 'kasir', 'gudang'];

    public function index()
    {
        $staff = User::orderBy('name')->paginate(10);

        return view('staff.index', compact('staff'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'role'      => ['required', Rule::in($this->roles)],
            'password'  => ['required', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
        ], $this->messages());

        $user = User::create([
        'name'      => $data['name'],
        'email'     => $data['email'],
        'role'      => $data['role'],
        'password'  => Hash::make($data['password']),
        'is_active' => $data['is_active'],
    ]);

    // Staf dibuat langsung oleh Pemilik → tandai terverifikasi otomatis
    $user->forceFill(['email_verified_at' => now()])->save();

        return redirect()->route('staff.index')
            ->with('success', 'Staf "' . $data['name'] . '" berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'role'      => ['required', Rule::in($this->roles)],
            'password'  => ['nullable', 'string', 'min:8'],
            'is_active' => ['required', 'boolean'],
        ], $this->messages());

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->role = $data['role'];
        $user->is_active = $data['is_active'];

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return redirect()->route('staff.index')
            ->with('success', 'Staf "' . $data['name'] . '" berhasil diperbarui.');
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->id === $request->user()->id) {
            return redirect()->route('staff.index')
                ->with('error', 'Kamu tidak bisa menghapus akunmu sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('staff.index')
            ->with('success', 'Staf "' . $name . '" berhasil dihapus.');
    }

    private function messages(): array
    {
        return [
            'name.required'      => 'Nama wajib diisi.',
            'email.required'     => 'Email wajib diisi.',
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email ini sudah digunakan.',
            'role.required'      => 'Peran wajib dipilih.',
            'role.in'            => 'Peran yang dipilih tidak valid.',
            'password.required'  => 'Password wajib diisi.',
            'password.min'       => 'Password minimal 8 karakter.',
            'is_active.required' => 'Status wajib dipilih.',
        ];
    }
}
