<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive;

use App\Enum\DealStatusEnum;
use App\Enum\IntegrationTypeEnum;
use App\Helper\DateHelper;
use Carbon\Carbon;

class PipedriveDealDTO
{
    public int $integration_deal_id;

    public string $title;

    public float $value;

    public Carbon $add_time;

    public DealStatusEnum $status;

    public function __construct(array $integrationDealArray)
    {
        $this->integration_deal_id = $integrationDealArray['id'];
        $this->title = $integrationDealArray['title'];
        $this->value = $integrationDealArray['value'] ? $integrationDealArray['value'] * 100 : 0;
        $this->add_time = DateHelper::parsePipedriveTime($integrationDealArray['add_time']);
        $this->status = DealStatusEnum::tryFrom($integrationDealArray['status']);
    }

    public function integrationIdentifiers(): array
    {
        return [
            'integration_deal_id' => $this->integration_deal_id,
            'integration_type' => IntegrationTypeEnum::PIPEDRIVE->value,
        ];
    }

    public function toArray(): array
    {
        return [
            ...$this->integrationIdentifiers(),
            'title' => $this->title,
            'value' => $this->value,
            'add_time' => $this->add_time,
            'status' => $this->status->value,
        ];
    }
}
