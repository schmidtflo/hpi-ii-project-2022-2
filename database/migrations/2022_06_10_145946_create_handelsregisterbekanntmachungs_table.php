<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('handelsregisterbekanntmachungen', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('rb_id');
            $table->string('state');
            $table->string('reference_id');
            $table->foreignId('company_id')->nullable();
            $table->date('event_date');
            $table->string('event_type');
            $table->string('status');
            $table->text('information');
            $table->timestamps();

            $table->unique(['rb_id', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('handelsregisterbekanntmachungen');
    }
};
