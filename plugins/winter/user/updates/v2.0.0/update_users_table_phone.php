<?php namespace Winter\User\Updates;

use Db;
use Schema;
use Winter\Storm\Database\Updates\Migration;

class UpdateUsersTablePhone extends Migration
{
    public function up()
    {
        Schema::table('users', function($table)
        {
            $table->string('phone')->nullable();
        });
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'phone')) {
            Schema::table('users', function($table)
            {
                $table->dropColumn('phone');
            });
        }
    }
}
