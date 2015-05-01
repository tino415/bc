<?php

use yii\db\Schema;
use yii\db\Migration;

class m150501_022511_session_add_col extends Migration
{
    public function up()
    {
        $this->addColumn('session', 'psql_check', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0');
    }

    public function down()
    {
        echo "m150501_022511_session_add_col cannot be reverted.\n";

        return false;
    }
    
    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }
    
    public function safeDown()
    {
    }
    */
}
