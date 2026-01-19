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
        // Insert default client appearance settings
        \DB::table('settings')->insert([
            [
                'key' => 'client.primary_color',
                'value' => '#3b82f6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'client.modal_theme',
                'value' => 'light',
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
        \DB::table('settings')->whereIn('key', [
            'client.primary_color', 
            'client.modal_theme'
        ])->delete();
    }
};
