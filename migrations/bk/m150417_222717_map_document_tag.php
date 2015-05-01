<?php

use yii\db\Schema;
use yii\db\Migration;

class m150417_222717_map_document_tag extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->createTable('map_document_tag', [
            'id' => 'pk',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'tag_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk_map_document_tag_document',
            'map_document_tag', 'document_id',
            'document', 'id',
            'CASCADE'
        );

        $this->addForeignKey('fk_map_document_tag_tag',
            'map_document_tag', 'tag_id',
            'tag', 'id',
            'CASCADE'
        );
    }

    public function down()
    {
        echo "m150417_222717_map_document_tag cannot be reverted.\n";

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
