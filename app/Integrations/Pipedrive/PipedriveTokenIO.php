<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Models\PipedriveConfig;
use Carbon\Carbon;
use Devio\Pipedrive\PipedriveToken;
use Devio\Pipedrive\PipedriveTokenStorage;
use Illuminate\Support\Facades\Auth;

class PipedriveTokenIO implements PipedriveTokenStorage
{
    public function setToken(PipedriveToken $token): void
    {
        PipedriveConfig::updateOrCreate(
            ['organization_id' => Auth::user()->organization->id],
            [
                'access_token' => $token->getAccessToken(),
                'refresh_token' => $token->getRefreshToken(),
                'expires_at' => Carbon::createFromTimestamp($token->expiresAt()),
            ],
        );
    }

    public function getToken(): ?PipedriveToken
    {
        $token = Auth::user()->organization->pipedriveConfig;

        if ($token) {
            return new PipedriveToken([
                'accessToken' => $token->access_token,
                'refreshToken' => $token->refresh_token,
                'expiresAt' => $token->expires_at->getTimestamp(),
            ]);
        } else {
            return null;
        }
    }
}
