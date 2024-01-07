<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Targetforce\Base\UpgradeMigration;

class CreatePostSegmentTable extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $segments = $this->getTableName('segments');
        $posts = $this->getTableName('posts');

        Schema::create('targetforce_post_segment', function (Blueprint $table) use ($posts, $segments) {
            $table->increments('id');
            $table->unsignedInteger('segment_id');
            $table->unsignedInteger('post_id');
            $table->timestamps();

            $table->foreign('segment_id')->references('id')->on($segments);
            $table->foreign('post_id')->references('id')->on($posts);
        });
    }
}
