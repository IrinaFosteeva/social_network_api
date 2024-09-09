<?php

// app/Http/Controllers/RoleAssignmentController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class RoleAssignmentController extends Controller
{
    public function assignRole(Request $request, $userId)
    {
        $request->validate([
            'role' => 'required|string|in:user,moderator,admin',
        ]);

        $user = User::findOrFail($userId);

        if (!Auth::user()->hasRole('admin')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $role = $request->input('role');
        $user->syncRoles($role);

        return response()->json(['message' => 'Role assigned successfully.']);
    }
}

