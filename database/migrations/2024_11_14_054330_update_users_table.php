<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Adding new fields based on typical requirements in your ERD
            $table->string('username')->unique()->after('email');
            $table->string('phone_num')->nullable()->after('password');
            $table->text('bio')->nullable();
            $table->integer('total_follower')->default(0);
            $table->decimal('average_rating', 3, 2)->default(0);
            $table->text('social_link')->nullable();
            $table->integer('verification_status')->default(0);
            $table->integer('status')->default(1);

            // Foreign key constraint for role_id
            // $table->foreign('role_id')->references('id')->on('roles')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            // Dropping the columns and foreign key in the reverse method
            // $table->dropForeign(['role_id']);
            $table->dropColumn([
                'username', 'phone_num', 'bio', 'total_follower', 
                'average_rating', 'social_link', 'verification_status',
                'status', 'remember_token'
            ]);
        });
    }
}
