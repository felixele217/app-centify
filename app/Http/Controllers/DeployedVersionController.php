<?php

declare(strict_types=1);

namespace App\Http\Controllers;

class DeployedVersionController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'version' => trim(file_get_contents(base_path('storage/app/deployed_version.txt'))),
        ]);
    }
}
