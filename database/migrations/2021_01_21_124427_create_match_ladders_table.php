<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMatchLaddersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('match_ladders')){
            Schema::create('match_ladders', function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('title');
                $table->string('gender');
                $table->uuid('seasons_id')->index();
                $table->uuid('match_rank_categories_id')->index();
                $table->foreign('seasons_id')->references('id')->on('seasons')->onDelete('cascade');
                $table->foreign('match_rank_categories_id')->references('id')->on('match_rank_categories')->onDelete('cascade');
                $table->timestamp('created_at')->useCurrent();
                $table->timestamp('updated_at')->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'))->nullable();
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
        Schema::dropIfExists('match_ladders');
    }
}
