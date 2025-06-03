<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            // Foreign key to transaction_headers table
            $table->unsignedBigInteger('transaction_header_id');
            $table->foreign('transaction_header_id')
                ->references('id')
                ->on('transaction_headers')
                ->onDelete('cascade');

            $table->decimal('transaction_amount', 15, 2);
            $table->unsignedBigInteger('bank_code_id');
            $table->string('transaction_reference_no');
            $table->string('supplier_ref_no')->nullable();
            $table->date('supplier_reference_date')->nullable();
            $table->date('transaction_reference_date')->nullable();
            $table->unsignedBigInteger('reference_type_id');

            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();

            // Foreign keys
            $table->foreign('bank_code_id')->references('id')->on('banks')->onDelete('cascade');
            $table->foreign('reference_type_id')->references('id')->on('reference_types')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transactions');
    }
}
