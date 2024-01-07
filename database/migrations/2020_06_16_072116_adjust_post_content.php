<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Targetforce\Base\UpgradeMigration;

class AdjustPostContent extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $posts = $this->getTableName('posts');

        Schema::table($posts, function (Blueprint $table) {
            $table->longText('content')->change();
        });
    }
}
