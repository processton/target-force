<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Targetforce\Base\Models\EmailServiceType;

class AddPostalEmailServiceType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('targetforce_email_service_types')
            ->insert(
                [
                    'id' => EmailServiceType::POSTAL,
                    'name' => 'Postal',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    }
}
