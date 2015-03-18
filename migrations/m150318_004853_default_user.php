<?php

use yii\db\Schema;
use yii\db\Migration;
use yii\base\Security;

class m150318_004853_default_user extends Migration
{
    public function up()
    {
        $this->addColumn('users', 'role_id', Schema::TYPE_INTEGER);
        $this->insert('users', [
            'email' => 'cernakmartin3@gmail.com',
            'password' => Yii::$app->getSecurity()->generatePasswordHash('user'),
            'role_id' => 8
        ]);

        $this->insert('users', [
            'email' => 'martin.cernak@iaeste.sk',
            'password' => Yii::$app->getSecurity()->generatePasswordHash('admin'),
            'role_id' => '2',
        ]);
    }

    public function down()
    {
        $this->delete('users', ['email' => 'cernakmartin3@gmail.com']);
        $this->delete('users', ['email' => 'martin.cernak@iaeste.com']);
        $this->dropColumn('users', 'role_id');

        return True;
    }
}
