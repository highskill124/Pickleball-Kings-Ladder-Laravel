<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      if(!Schema::hasTable('matches')){
        Schema::create('matches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->timestamp('played')->nullable();
            $table->uuid('requests_id')->index();
            // $table->uuid('match_ladder_id')->index();           
            $table->integer('point1_user1')->nullable();
            $table->integer('point1_user2')->nullable();
            $table->integer('point2_user1')->nullable();
            $table->integer('point2_user2')->nullable();
            $table->integer('point3_user1')->nullable();
            $table->integer('point3_user2')->nullable();
            $table->integer('week')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->foreign('requests_id')->references('id')->on('requests')->onDelete('cascade');
            // $table->foreign('match_ladder_id')->references('id')->on('match_ladders')->onDelete('cascade');
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
        Schema::dropIfExists('matches');
    }
}
