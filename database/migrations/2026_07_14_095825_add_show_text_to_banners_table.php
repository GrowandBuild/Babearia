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
        Schema::table('banners', function (Blueprint $table) {
            $table->boolean('mostrar_titulo')->default(true)->comment('Mostrar título sobre o banner');
            $table->boolean('mostrar_descricao')->default(true)->comment('Mostrar descrição sobre o banner');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['mostrar_titulo', 'mostrar_descricao']);
        });
    }
    }
};
