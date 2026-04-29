<?php

namespace App\Http\Controllers;

use App\Jobs\DeleteAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function index()
    {
        if (! user()->isAdminUser()) {
            abort(403);
        }

        $users = User::with(['defaultUsername:id,username', 'defaultRecipient:id,email'])
            ->withCount(['aliases', 'recipients'])
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'username' => $user->defaultUsername?->username,
                    'email' => $user->defaultRecipient?->email,
                    'plan' => $user->plan,
                    'aliases_count' => $user->aliases_count,
                    'recipients_count' => $user->recipients_count,
                    'bandwidth' => round($user->bandwidth / 1024 / 1024, 2),
                    'created_at' => $user->created_at->toDateTimeString(),
                    'is_admin' => $user->isAdminUser(),
                ];
            });

        return Inertia::render('Admin/Index', [
            'users' => $users,
        ]);
    }

    public function destroyUser(Request $request, $id)
    {
        if (! user()->isAdminUser()) {
            abort(403);
        }

        $targetUser = User::findOrFail($id);

        if ($targetUser->isAdminUser()) {
            return response('Cannot delete the admin account.', 403);
        }

        DeleteAccount::dispatch($targetUser);

        return response()->json(['message' => 'User account queued for deletion.']);
    }
}
