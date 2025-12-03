<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOptionEToQuestions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->string('option_e')->nullable()->after('option_d');
        });

        // Update enum to include 'e' — use raw statement for portability
        // MySQL syntax: ALTER TABLE `questions` MODIFY `correct_answer` ENUM('a','b','c','d','e');
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `questions` MODIFY `correct_answer` ENUM('a','b','c','d','e') NOT NULL");
        } catch (\Exception $e) {
            // ignore on DBs that don't support enum alter this way
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // attempt to revert enum and drop column
        try {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE `questions` MODIFY `correct_answer` ENUM('a','b','c','d') NOT NULL");
        } catch (\Exception $e) {
            // ignore
        }

        Schema::table('questions', function (Blueprint $table) {
            if (Schema::hasColumn('questions', 'option_e')) {
                $table->dropColumn('option_e');
            }
        });
    }
}
