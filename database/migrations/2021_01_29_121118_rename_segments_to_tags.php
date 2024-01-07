<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameSegmentsToTags extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::rename('targetforce_segments', 'targetforce_tags');

        Schema::table('targetforce_segment_subscriber', function (Blueprint $table) {
            $foreignKeys = $this->listTableForeignKeys('targetforce_segment_subscriber');

            if (in_array('targetforce_segment_subscriber_segment_id_foreign', $foreignKeys)) {
                $table->dropForeign('targetforce_segment_subscriber_segment_id_foreign');
            } elseif (in_array('segment_subscriber_segment_id_foreign', $foreignKeys)) {
                $table->dropForeign('segment_subscriber_segment_id_foreign');
            }

            $table->renameColumn('segment_id', 'tag_id');

            $table->foreign('tag_id')->references('id')->on('targetforce_tags');
        });

        Schema::rename("targetforce_segment_subscriber", "targetforce_tag_subscriber");


        Schema::table('targetforce_post_segment', function (Blueprint $table) {
            $foreignKeys = $this->listTableForeignKeys('targetforce_post_segment');

            if (in_array('targetforce_post_segment_segment_id_foreign', $foreignKeys)) {
                $table->dropForeign('targetforce_post_segment_segment_id_foreign');
            } elseif (in_array('post_segment_segment_id_foreign', $foreignKeys)) {
                $table->dropForeign('post_segment_segment_id_foreign');
            }

            $table->renameColumn('segment_id', 'tag_id');

            $table->foreign('tag_id')->references('id')->on('targetforce_tags');
        });

        Schema::rename("targetforce_post_segment", "targetforce_post_tag");
    }

    protected function listTableForeignKeys(string $table): array
    {
        $conn = Schema::getConnection()->getDoctrineSchemaManager();

        return array_map(function ($key) {
            return $key->getName();
        }, $conn->listTableForeignKeys($table));
    }
}
