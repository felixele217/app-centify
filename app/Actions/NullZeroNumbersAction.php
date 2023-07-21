<?php

namespace App\Actions;

class NullZeroNumbersAction
{
    public static function execute(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (! isset($data[$field])) {
                continue;
            }

            $data[$field] = $data[$field] === 0 ? null : $data[$field];
        }

        return $data;
    }
}
