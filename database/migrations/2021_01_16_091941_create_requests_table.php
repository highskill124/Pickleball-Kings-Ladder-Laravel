<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    if(!Schema::hasTable('requests')){
        Schema::create('requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->uuid('ladder_id');
            $table->uuid('rank_category_id');
            $table->uuid('request_to')->index()->nullable();
            $table->uuid('request_by')->index();
            $table->uuid('matches_id')->index()->nullable();
            $table->string('location')->nullable();
            $table->timestamp('time')->nullable();
            $table->string('status')->default('pending')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->default(DB::raw('NULL ON UPDATE CURRENT_TIMESTAMP'))->nullable();
            $table->foreign('request_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('request_to')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('matches_id')->references('id')->on('matches')->onDelete('cascade');
            $table->foreign('ladder_id')->references('id')->on('match_ladders')->onDelete('cascade');
            $table->foreign('rank_category_id')->references('id')->on('match_rank_categories')->onDelete('cascade');
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
        Schema::dropIfExists('requests');
    }
}
