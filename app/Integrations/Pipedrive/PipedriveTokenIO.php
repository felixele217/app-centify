<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Enum\IntegrationTypeEnum;
use App\Models\Organization;
use App\Repositories\IntegrationRepository;
use Carbon\Carbon;
use Devio\Pipedrive\PipedriveToken;
use Devio\Pipedrive\PipedriveTokenStorage;

class PipedriveTokenIO implements PipedriveTokenStorage
{
    public function __construct(
        private Organization $organization
    ) {
    }

    public function setToken(PipedriveToken $token): void
    {
        IntegrationRepository::updateOrCreate($this->organization->id, [
            'name' => IntegrationTypeEnum::PIPEDRIVE->value,
            'access_token' => $token->getAccessToken(),
            'refresh_token' => $token->getRefreshToken(),
            'expires_at' => Carbon::createFromTimestamp($token->expiresAt()),
        ]);
    }

    public function getToken(): ?PipedriveToken
    {
        $integration = $this->organization->integrations()->whereName(IntegrationTypeEnum::PIPEDRIVE->value)->first();

        if ($integration->access_token) {
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
