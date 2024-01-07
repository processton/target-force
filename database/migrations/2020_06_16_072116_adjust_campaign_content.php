<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Targetforce\Base\UpgradeMigration;

class AdjustCampaignContent extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $campaigns = $this->getTableName('campaigns');

        Schema::table($campaigns, function (Blueprint $table) {
            $table->longText('content')->change();
        });
    }
}
