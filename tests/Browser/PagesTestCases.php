<?php

namespace Tests\Browser;

class PagesTestCases
{
    public static function testCases(): array
    {
        return [
            [
                'expected_text' => 'Total Commission',
                'slug' => self::slug(route('dashboard')),
            ],

            [
                'expected_text' => 'Deals',
                'slug' => self::slug(route('deals.index')),
            ],

            [
                'expected_text' => 'Agents',
                'slug' => self::slug(route('agents.index')),
            ],

            [
                'expected_text' => 'pipedrive',
                'slug' => self::slug(route('integrations.index')),
            ],

            [
                'expected_text' => 'Profile Information',
                'slug' => self::slug(route('profile.edit')),
            ],

            [
                'expected_text' => 'Custom Integration Fields',
                'slug' => self::slug(route('integrations.custom-fields.index', 1)),
            ],

            [
                'expected_text' => 'Plans',
                'slug' => self::slug(route('plans.index')),
            ],

            [
                'expected_text' => 'Create Quota Attainment Plan',
                'slug' => self::slug(route('plans.create')),
            ],

            [
                'expected_text' => 'Update Quota Attainment Plan',
                'slug' => self::slug(route('plans.edit', 1)),
            ],
        ];
    }

    private static function slug(string $route): string
    {
        return parse_url($route, PHP_URL_PATH);
    }
}
