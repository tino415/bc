<?php

use yii\db\Schema;
use yii\db\Migration;

class m150331_122329_rename_password extends Migration
{
    public function up()
    {
        $this->renameColumn('users', 'password', 'password_hash');
    }

    public function down()
    {

        $this->renameColumn('users', 'password_hash', 'password');
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
