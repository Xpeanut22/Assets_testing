<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ExpandAssetHistoryRemarksForVehicleTripTicket extends Migration
{
    public function up()
    {
        DB::statement('ALTER TABLE asset_history MODIFY remarks TEXT NULL');
    }

    public function down()
    {
        DB::statement('ALTER TABLE asset_history MODIFY remarks VARCHAR(255) NULL');
    }
}
