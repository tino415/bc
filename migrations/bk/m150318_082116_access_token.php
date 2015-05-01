<?php

use yii\db\Schema;
use yii\db\Migration;

class m150318_082116_access_token extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'access_token', Schema::TYPE_STRING);
        $this->addColumn('users', 'auth_key', Schema::TYPE_STRING);
        $this->addColumn('users', 'username', Schema::TYPE_STRING);
        $this->update('users', [
            'access_token' => 'test_access_token',
            'auth_key' => 'test_auth_key',
            'username' => 'admin',
        ], ['email' => 'cernakmartin3@gmail.com']);
        $this->update('users', [
            'access_token' => 'test_access_token',
            'auth_key' => 'test_auth_key',
            'username' => 'user1',
        ], ['email' => 'martin.cernak@iaeste.sk']);
                }

    public function down()
    {
        $this->dropColumn('users', 'access_token');
        $this->dropColumn('users', 'auth_key');
        $this->dropColumn('users', 'username');
        return True;
    }
}
