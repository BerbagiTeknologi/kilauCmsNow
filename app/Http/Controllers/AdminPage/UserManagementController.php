<?php

namespace App\Http\Controllers\AdminPage;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminPage\UpdateUserPasswordRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::orderByDesc('created_at')->get();

        return view('AdminPage.UserManagement.index', compact('users'));
    }

    public function updatePassword(UpdateUserPasswordRequest $request, User $user)
    {
        $data = $request->validated();

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Password user berhasil diperbarui.');
    }
}
