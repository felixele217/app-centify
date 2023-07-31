<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\SetPipedriveSubdomainAction;
use App\Facades\Pipedrive;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class PipedriveAuthController extends Controller
{
    public function create(): RedirectResponse
    {
        $authorizationUrl = 'https://oauth.pipedrive.com/oauth/authorize?'.http_build_query([
            'client_id' => env('PIPEDRIVE_CLIENT_ID'),
            'redirect_uri' => env('PIPEDRIVE_CALLBACK_URL'),
        ]);

        return redirect($authorizationUrl);
    }

    public function store(): RedirectResponse
    {
        if (! empty(request()->query('code'))) {
            Pipedrive::authorize(request()->query('code'));
        }

        SetPipedriveSubdomainAction::execute(Auth::user()->organization);

        return to_route('custom-integration-fields.index');
    }
}
