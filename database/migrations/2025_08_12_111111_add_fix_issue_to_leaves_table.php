<?php
// database/migrations/2025_08_12_rename_and_update_leaves_columns.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Rename Status to status and change to string if needed
            if (Schema::hasColumn('leaves', 'Status')) {
                $table->renameColumn('Status', 'status');
            }
            // Change status to string and allow 'Pending', 'Approved', 'Rejected'
            $table->string('status', 20)->default('Pending')->change();

            // Add missing columns if not present
            if (!Schema::hasColumn('leaves', 'from_date')) {
                $table->date('from_date')->nullable()->after('employee_id');
            }
            if (!Schema::hasColumn('leaves', 'to_date')) {
                $table->date('to_date')->nullable()->after('from_date');
            }
            if (!Schema::hasColumn('leaves', 'leave_type')) {
                $table->string('leave_type', 20)->nullable()->after('to_date');
            }
            if (!Schema::hasColumn('leaves', 'reason')) {
                $table->string('reason')->nullable()->after('leave_type');
            }
        });
    }

    public function down(): void
    {
        Schema::table('leaves', function (Blueprint $table) {
            // Optionally reverse changes
            // $table->renameColumn('status', 'Status');
            // $table->enum('Status', ['Accept','Reject'])->change();
            $table->dropColumn(['from_date', 'to_date', 'leave_type', 'reason']);
        });
    }
};