<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Facades\Pipedrive;
use Illuminate\Http\Request;

class PipedriveAuthController extends Controller
{
    public function create()
    {
        $authorizationUrl = 'https://oauth.pipedrive.com/oauth/authorize?'.http_build_query([
            'client_id' => env('PIPEDRIVE_CLIENT_ID'),
            'redirect_uri' => env('PIPEDRIVE_CALLBACK_URL'),
        ]);

        return redirect($authorizationUrl);
    }

    public function store(Request $request)
    {
        if (! empty($request->query('code'))) {
            Pipedrive::authorize($request->query('code'));
        }

        return to_route('integrations');
    }
}
