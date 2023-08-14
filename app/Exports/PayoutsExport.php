<?php

namespace App\Exports;

use App\Models\Organization;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PayoutsExport implements FromArray, ShouldAutoSize
{
    public function __construct(
        private Organization $organization,
    ) {
    }

    public function array(): array
    {
        return [
            [
                'Total Commission',
                'User Name',
                'User Email',
                'Sick Days',
                'Vacation Days',
                'Month',
                'Cliff',
                'Kicker',
                'Absence Payments',
                'Commission',
                'Total Commission',
                'Quota Attainment',
            ],
            // payouts data here

            //TODO: put this in a separate export
            [
                'Deal List',
                'User Name',
                'User Email',
                'Deal Name',
                'Link to CRM',
                'Deal Value',
                'Deal Status',
                'Timestamp',
                'Notes',
                'Attributed Plan',
                'Share',
                'Attributed Commission',
            ],
            // deals data here
        ];
    }
}
