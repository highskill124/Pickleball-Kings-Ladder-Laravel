<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateMatchRankCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    if(!Schema::hasTable('match_rank_categories')){
        Schema::create('match_rank_categories', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('type');
            $table->uuid('match_single_doubles_id')->index();
            $table->foreign('match_single_doubles_id')->references('id')->on('match_single_doubles')->onDelete('cascade');
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
        Schema::dropIfExists('match_rank_categories');
    }
}
