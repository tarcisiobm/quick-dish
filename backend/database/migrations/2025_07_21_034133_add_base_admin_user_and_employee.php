<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Employee;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $adminUser = User::create([
            'name' => 'Admin',
            'email' => 'quickdish@admin.com',
            'password' => Hash::make('12345678'),
            'email_verified_at' => now(),
            'status' => true,
        ]);

        Employee::create([
            'user_id' => $adminUser->id,
            'job_title' => 'Administrador do Sistema',
            'salary' => 0,
            'hire_date' => now(),
            'work_schedule' => 'Horário de trabalho comercial'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $adminUser = User::where('email', 'quickdish@admin.com')->first();

        if ($adminUser) {
            Employee::where('user_id', $adminUser->id)->delete();
            $adminUser->forceDelete();
        }
    }
};
