<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateQuizAnswersAllowE extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Modify enum to include 'e'
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `quiz_answers` MODIFY `user_answer` ENUM('a','b','c','d','e') NOT NULL");
        } catch (\Exception $e) {
            // ignore if not supported
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `quiz_answers` MODIFY `user_answer` ENUM('a','b','c','d') NOT NULL");
        } catch (\Exception $e) {
            // ignore
        }
    }
}
