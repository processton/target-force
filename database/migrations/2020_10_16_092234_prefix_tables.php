<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class PrefixTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->getTables() as $table) {
            if (Schema::hasTable("targetforce_{$table}")) {
                continue;
            }

            if (!Schema::hasTable("{$table}")) {
                continue;
            }

            Schema::rename($table, "targetforce_{$table}");
        }
    }

    /**
     * @return array
     */
    protected function getTables()
    {
        return [
            'post_segment',
            'post_statuses',
            'posts',
            'email_service_types',
            'email_services',
            'message_failures',
            'message_urls',
            'messages',
            'segment_subscriber',
            'segments',
            'subscribers',
            'templates',
            'unsubscribe_event_types',
        ];
    }
}
