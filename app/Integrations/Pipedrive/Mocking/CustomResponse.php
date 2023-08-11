<?php

declare(strict_types=1);

namespace App\Integrations\Pipedrive\Mocking;

class CustomResponse
{
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function all(): static
    {
        return $this;
    }

    public function toArray(): array
    {
        return json_decode(json_encode($this->getData()), true);
    }

    public function getData(): array
    {
        return $this->data;
    }
}
