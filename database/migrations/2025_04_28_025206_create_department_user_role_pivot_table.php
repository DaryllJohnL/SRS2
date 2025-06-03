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

            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('role_id');

            // Use RESTRICT or SET NULL to avoid SQL Server multiple cascade path issue
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('restrict');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('restrict');

            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();

            $table->unique(['department_id', 'user_id', 'role_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('department_user_role');
    }
}
