<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Pipedrive;
use App\Enum\IntegrationTypeEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Repositories\IntegrationRepository;
use App\Actions\GetPipedriveSubdomainAction;
use App\Actions\SetPipedriveSubdomainAction;

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

        // IntegrationRepository::create(Auth::user()->organization->id, [
        //     'name' => IntegrationTypeEnum::PIPEDRIVE->value,
        //     'subdomain' => GetPipedriveSubdomainAction::execute(),
        // ]);

        return to_route('custom-integration-fields.index');
    }
}
