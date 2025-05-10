<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('set null'); // <-- Added this line
            $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('created_date')->useCurrent();
            $table->timestamp('last_update_date')->useCurrent()->useCurrentOnUpdate();
            $table->rememberToken();
            // NOTE: Removed Laravel's default created_at and updated_at para customized ang fields natin
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
