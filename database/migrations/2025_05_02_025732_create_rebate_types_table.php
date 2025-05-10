<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rebate_types', function (Blueprint $table) {
            $table->id();
            $table->string('rebate_code')->unique();  // Ensure rebate_code is unique
            $table->string('rebate_name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rebate_types');
    }
};
