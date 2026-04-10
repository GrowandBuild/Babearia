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
        Schema::table('clientes', function (Blueprint $table) {
            $table->boolean('is_package_client')->default(false)->after('instagram');
            $table->integer('package_total_services')->nullable()->after('is_package_client');
            $table->integer('package_used_services')->default(0)->after('package_total_services');
            $table->decimal('package_price', 8, 2)->nullable()->after('package_used_services');
            $table->date('package_start_date')->nullable()->after('package_price');
            $table->date('package_end_date')->nullable()->after('package_start_date');
            $table->text('package_observations')->nullable()->after('package_end_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'is_package_client',
                'package_total_services',
                'package_used_services',
                'package_price',
                'package_start_date',
                'package_end_date',
                'package_observations'
            ]);
        });
    }
};
