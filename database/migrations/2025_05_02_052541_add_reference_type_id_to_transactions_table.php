<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reference_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('description')->nullable();
            $table->string('data_type')->nullable(); // Optional if needed
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('reference_types');
    }
};
