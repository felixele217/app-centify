<?php

namespace App\Integrations\Pipedrive;

use App\Encrypter;
use App\Models\PipedriveToken as PipedriveTokenModel;
use Carbon\Carbon;
use Devio\Pipedrive\PipedriveToken;
use Devio\Pipedrive\PipedriveTokenStorage;
use Illuminate\Support\Facades\Auth;

class PipedriveTokenIO implements PipedriveTokenStorage
{
    public function setToken(PipedriveToken $token): void
    {
        PipedriveTokenModel::create([
            'user_id' => Auth::user()->id,
            'access_token' => Encrypter::encrypt($token->getAccessToken()),
            'refresh_token' => Encrypter::encrypt($token->getRefreshToken()),
            'expires_at' => Carbon::createFromTimestamp($token->expiresAt()),
        ]);
    }

    public function getToken(): PipedriveToken|null
    {
        $token = PipedriveTokenModel::whereUserId(Auth::user()->id)->first();

        if ($token) {
            return new PipedriveToken([
                'accessToken' => Encrypter::decrypt($token->access_token),
                'refreshToken' => Encrypter::decrypt($token->refresh_token),
                'expiresAt' => $token->expires_at->getTimestamp(),
            ]);
        } else {
            return null;
        }
    }
}
