<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartmentUserRolePivotTable extends Migration
{
    public function up()
    {
        Schema::create('department_user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();

            // Add unique constraint to ensure no duplicate user-role-department entries
            $table->unique(['department_id', 'user_id', 'role_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('department_user_role');
    }
}
