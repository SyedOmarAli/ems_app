<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->string('leave_type', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            $table->string('leave_type', 4)->nullable()->change(); // or whatever the old length was
        });
    }
};