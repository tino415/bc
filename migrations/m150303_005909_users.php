<?php

use yii\db\Schema;
use yii\db\Migration;

class m150303_005909_users extends Migration
{
    public function up()
    {
        $this->createTable('users', [
            'id' => 'pk',
            'email' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
            'password' => Schema::TYPE_STRING . ' NOT NULL',
        ]);

    }

    public function down()
    {
        echo "m150303_005909_users cannot be reverted.\n";

        $this->dropTable('users');

        return false;
    }
}
