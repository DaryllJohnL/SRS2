<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionHeadersTable extends Migration
{
    public function up()
    {
        Schema::create('transaction_headers', function (Blueprint $table) {
            $table->id();

            // Foreign key for supplier_code (linked to suppliers table)
            $table->unsignedBigInteger('supplier_code');
            $table->foreign('supplier_code')->references('id')->on('suppliers')->onDelete('cascade');

            // Foreign key for rebate_type (linked to rebate_types table)
            $table->unsignedBigInteger('rebate_type');
            $table->foreign('rebate_type')->references('id')->on('rebate_types')->onDelete('cascade');

            // Additional fields
            $table->string('particulars');
            $table->decimal('incentive', 15, 2)->nullable();
            $table->string('conversion')->nullable(); // Changed from decimal to string
            $table->unsignedBigInteger('prepared_by');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('review_by')->nullable();
            $table->date('approve_date')->nullable();
            $table->string('status');

            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaction_headers');
    }
}
