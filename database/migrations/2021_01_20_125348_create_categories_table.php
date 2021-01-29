<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    if(!Schema::hasTable('categories')){
        Schema::create('categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('singles')->nullable();
            $table->string('additional_singles')->nullable();
            $table->string('doubles')->nullable();
            $table->string('double_partner')->nullable();
            $table->string('double_second_partner')->nullable();
            $table->string('additional_doubles')->nullable();
            $table->string('additional_double_partner')->nullable();
            $table->string('additional_double_second_partner')->nullable();
            $table->string('mixed_doubles')->nullable();
            $table->string('mixed_doubles_partner')->nullable();
            $table->string('mixed_doubles_second_partner')->nullable();
            $table->string('additional_mixed_doubles')->nullable();
            $table->string('additional_mixed_doubles_partner')->nullable();
            $table->string('additional_mixed_doubles_second_partner')->nullable();            
            $table->uuid('user_id')->index();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
       }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categories');
    }
}
