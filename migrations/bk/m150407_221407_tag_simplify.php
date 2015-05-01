<?php

use yii\db\Schema;
use yii\db\Migration;

class m150407_221407_tag_simplify extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->dropForeignKey('fk_tag_type', 'tag');
        $this->dropForeignKey('fk_document_tag', 'tag');
        $this->dropTable('tag_type');
        $this->dropTable('map_user_tag');
        $this->dropTable('map_document_tag');
        $this->dropTable('tag');


        $this->createTable('tag', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'document_id' =>  Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk_documnet_tag',
            'tag', 'document_id',
            'document', 'id',
            'CASCADE'
        );
    }
    
    public function safeDown()
    {
    }
}
