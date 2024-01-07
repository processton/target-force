<?php

namespace Targetforce\Base;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

class UpgradeMigration extends Migration
{
    protected function getTableName(string $baseName): string
    {
        if (Schema::hasTable("targetforce_{$baseName}")) {
            return "targetforce_{$baseName}";
        }

        if (Schema::hasTable($baseName)) {
            return $baseName;
        }

        throw new RuntimeException('Could not find appropriate table for base name ' . $baseName);
    }
}
