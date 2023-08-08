<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TruncateTables extends Seeder
{
    public const NOT_TRUNCATED_TABLES = [
        'migrations',
        'integrations',
        'admins',
        'organizations',
        'custom_fields',
    ];

    public function run()
    {
        $this->unsetForeignKeyChecks();

        $this->truncateTables();

        $this->setForeignKeyChecks();
    }

    public function truncateTables(): void
    {
        foreach ($this->truncatedTables() as $tableToTruncate) {
            if (self::driverIsMysql()) {

                DB::table($tableToTruncate)->truncate();
            } else {
                DB::statement('DELETE FROM '.$tableToTruncate.';');
            }
        }
    }

    public function truncatedTables(): Collection
    {
        $notTruncatedTables = self::NOT_TRUNCATED_TABLES;

        return collect(DB::connection()->getDoctrineSchemaManager()->listTableNames())
            ->reject(function ($table) use ($notTruncatedTables) {
                return in_array($table, $notTruncatedTables);
            });
    }

    public function unsetForeignKeyChecks(): void
    {
        DB::statement(self::driverIsMysql() ? 'SET FOREIGN_KEY_CHECKS=0;' : 'PRAGMA foreign_keys=OFF;');
    }

    public function setForeignKeyChecks(): void
    {
        DB::statement(self::driverIsMysql() ? 'SET FOREIGN_KEY_CHECKS=1;' : 'PRAGMA foreign_keys=ON;');
    }

    public function driverIsMysql(): bool
    {
        return DB::getDriverName() === 'mysql';
    }
}
