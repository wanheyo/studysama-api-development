<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateFolderIdToLessonIdInResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('resources', function (Blueprint $table) {
            // Drop the foreign key constraint on folder_id
            $table->dropForeign(['folder_id']);
            // Rename the column
            $table->renameColumn('folder_id', 'lesson_id');
            // Recreate the foreign key constraint for lesson_id
            $table->foreign('lesson_id')->references('id')->on('lessons')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('resources', function (Blueprint $table) {
            // Drop the foreign key constraint on lesson_id
            $table->dropForeign(['lesson_id']);
            // Rename the column back to folder_id
            $table->renameColumn('lesson_id', 'folder_id');
            // Recreate the foreign key constraint for folder_id
            $table->foreign('folder_id')->references('id')->on('folders')->onDelete('cascade');
        });
    }
}
