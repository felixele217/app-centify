<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\PipedriveFacade;
use App\Integrations\Pipedrive\PipedriveHelper;
use App\Repositories\IntegrationRepository;
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
        $pipedriveFacade = new PipedriveFacade(Auth::user()->organization);

        if (! empty(request()->query('code'))) {
            $pipedriveFacade->authorize(request()->query('code'));
        }

        $integration = IntegrationRepository::update(Auth::user()->organization->id, [
            'subdomain' => PipedriveHelper::organizationSubdomain($pipedriveFacade->deals()[0]),
        ]);

        return to_route('integrations.custom-fields.index', $integration);
    }
}
