<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Targetforce\Base\Models\EmailServiceType;

class AddSmtpEmailServiceType extends Migration
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
                    'id' => EmailServiceType::SMTP,
                    'name' => 'SMTP',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    }
}
