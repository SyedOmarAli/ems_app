<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Step 1: Add as nullable
        Schema::table('payrolls', function (Blueprint $table) {
            $table->date('month')->nullable()->after('end_date')->change();
        });

        // Step 2: Fill with month derived from start_date, fallback to '2025-01-01'
        $payrolls = DB::table('payrolls')->get();
        foreach ($payrolls as $payroll) {
            $month = '2025-01-01';
            if ($payroll->start_date) {
                try {
                    $month = \Carbon\Carbon::parse($payroll->start_date)->startOfMonth()->toDateString();
                } catch (\Exception $e) {}
            }
            DB::table('payrolls')->where('id', $payroll->id)->update(['month' => $month]);
        }

        // Step 3: Make NOT NULL
        Schema::table('payrolls', function (Blueprint $table) {
            $table->date('month')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropColumn('month');
        });
    }
};
