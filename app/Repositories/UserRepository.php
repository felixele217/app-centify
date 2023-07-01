<?php

namespace App\Repositories;

use App\Enum\RoleEnum;
use App\Http\Requests\StoreAgentRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserRepository
{
    public static function create(StoreAgentRequest $request, RoleEnum $role = null): User
    {
        $user = User::create([
            ...$request->validated(),
            'organization_id' => Auth::user()->organization->id,
        ]);

        $user->assignRole($role->value);

        return $user;
    }
}
