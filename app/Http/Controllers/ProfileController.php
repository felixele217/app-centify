<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => request()->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'organization' => Auth::user()->organization,
        ]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->safe()->only([
            'name',
            'email',
        ]));

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        $request->user()->organization->update($request->safe()->only([
            'auto_accept_demo_scheduled',
            'auto_accept_deal_won',
        ]));

        return Redirect::route('profile.edit');
    }

    public function destroy(): RedirectResponse
    {
        request()->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = request()->user();

        Auth::logout();

        $user->delete();

        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return Redirect::to('/');
    }
}
