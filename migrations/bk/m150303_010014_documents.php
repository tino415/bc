<?php

use yii\db\Schema;
use yii\db\Migration;

class m150303_010014_documents extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }


        $this->createTable('documents', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'link' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
            'type_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'interpret_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);


    }

    public function down()
    {
    
        $this->dropTable('documents');

        return true;
    }
}
