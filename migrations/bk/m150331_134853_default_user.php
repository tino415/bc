<?php

use yii\db\Schema;
use yii\db\Migration;
use yii\base\Security;

class m150331_134853_default_user extends Migration
{
    public function up()
    {
        $this->delete('users', ['email' => 'cernakmartin3@gmail.com']);
        $this->delete('users', ['email' => 'martin.cernak@iaeste.sk']);

        $this->addColumn('users', 'role_id', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 2');

        $this->insert('users', [
            'id' => 1,
            'email' => 'cernakmartin3@gmail.com',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
            'access_token' => 'test_access_token',
            'auth_key' => 'test_auth_key',
            'username' => 'admin',
            'role_id' => 8
        ]);

        $this->insert('users', [
            'id' => 2,
            'email' => 'guest@guest',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('guest'),
            'access_token' => 'guest_token',
            'auth_key' => 'guest_key',
            'username' => 'guest',
            'role_id' => '0',
        ]);

        $this->insert('users', [
            'id' => 3,
            'email' => 'martin.cernak@iaeste.sk',
            'password_hash' => Yii::$app->getSecurity()->generatePasswordHash('demo'),
            'access_token' => 'test_access_token',
            'auth_key' => 'test_auth_key',
            'username' => 'demo',
            'role_id' => '2',
        ]);

    }

    public function down()
    {
        $this->delete('users', ['id' => '1']);
        $this->delete('users', ['id' => '2']);
        $this->delete('users', ['id' => '3']);

        return True;
    }
}
