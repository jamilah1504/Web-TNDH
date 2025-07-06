<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->renameColumn('is_read', 'is_active');
            $table->dropColumn('role');
            $table->string('photo')->nullable()->after('message');
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->renameColumn('is_active', 'is_read');
            $table->string('role')->nullable();
            $table->dropColumn('photo');
        });
    }
};
