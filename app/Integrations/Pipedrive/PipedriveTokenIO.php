<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Enum\IntegrationTypeEnum;
use App\Models\PipedriveConfig;
use App\Repositories\IntegrationRepository;
use Carbon\Carbon;
use Devio\Pipedrive\PipedriveToken;
use Devio\Pipedrive\PipedriveTokenStorage;
use Illuminate\Support\Facades\Auth;

class PipedriveTokenIO implements PipedriveTokenStorage
{
    public function setToken(PipedriveToken $token): void
    {
        IntegrationRepository::updateOrCreate(Auth::user()->organization->id, [
            'name' => IntegrationTypeEnum::PIPEDRIVE->value,
            'access_token' => $token->getAccessToken(),
            'refresh_token' => $token->getRefreshToken(),
            'expires_at' => Carbon::createFromTimestamp($token->expiresAt()),
        ]);
       
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
        $integration = Auth::user()->organization->integrations()->whereName(IntegrationTypeEnum::PIPEDRIVE->value)->first();

        if ($integration) {
            return new PipedriveToken([
                'accessToken' => $integration->access_token,
                'refreshToken' => $integration->refresh_token,
                'expiresAt' => $integration->expires_at->getTimestamp(),
            ]);
        } else {
            return null;
        }
    }
}
