<?php

use Illuminate\Support\Facades\DB;
use Targetforce\Base\Models\EmailServiceType;
use Targetforce\Base\UpgradeMigration;

class AddMailjetEmailService extends UpgradeMigration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $email_service_types = $this->getTableName('email_service_types');

        DB::table($email_service_types)
            ->insert(
                [
                    'id' => EmailServiceType::MAILJET,
                    'name' => 'Mailjet',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
    }
}
