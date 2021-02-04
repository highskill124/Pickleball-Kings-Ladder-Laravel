<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSeasonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    if(!Schema::hasTable('seasons')){
        Schema::create('seasons', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->string('number_of_weeks');
            $table->timestamp('start_date');
            $table->timestamp('end_date');
            $table->timestamp('registration_deadline');
            $table->timestamp('playoff_date');
            $table->timestamp('playoff_date2');
            $table->integer('late_fee')->nullable();
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
        Schema::dropIfExists('seasons');
    }
}
