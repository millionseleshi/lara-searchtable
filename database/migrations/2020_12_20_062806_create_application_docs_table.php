<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_docs', function (Blueprint $table) {
            $table->id();
            $table->string('doc_type');
            $table->string('submission_type');
            $table->string('title');
            $table->string('doc_url');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_docs');
    }
}
