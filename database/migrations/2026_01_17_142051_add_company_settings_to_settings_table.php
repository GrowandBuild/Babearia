<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert default company settings
        \DB::table('settings')->insert([
            [
                'key' => 'company_name',
                'value' => 'Vida Maria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'company_logo',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \DB::table('settings')->whereIn('key', ['company_name', 'company_logo'])->delete();
    }
};
