<?php namespace OFFLINE\Mall\Updates;

use Artisan;
use DB;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;
use Schema;

class UpdateRainlabTableName extends Migration
{
    public function up()
    {
        Schema::rename('winter_location_countries', 'winter_location_countries');
    }

    public function down()
    {
        // Leave the columns. The migration might fail if data gets truncated.
    }
}	

/* winter_location_states
winter_translate_attributes
winter_translate_indexes
winter_translate_locales
winter_translate_messages	
winter_user_mail_blockers */