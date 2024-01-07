<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Targetforce\Base\UpgradeMigration;

class CreatePostStatusesTable extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('targetforce_post_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
        });

        DB::table('targetforce_post_statuses')
            ->insert([
               ['name' => 'Draft'],
               ['name' => 'Queued'],
               ['name' => 'Sending'],
               ['name' => 'Sent'],
            ]);
    }
}
