<?php

use Illuminate\Support\Facades\DB;
use Targetforce\Base\UpgradeMigration;

class AddCancelledPostStatus extends UpgradeMigration
{
    public function up()
    {
        $post_statuses = $this->getTableName('post_statuses');

        DB::table($post_statuses)
            ->insert([
                'id' => 5,
                'name' => 'Cancelled',
            ]);
    }
}
