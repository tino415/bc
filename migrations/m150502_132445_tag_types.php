<?php

use yii\db\Schema;
use yii\db\Migration;

class m150502_132445_tag_types extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->createTable('map_document_tag_type', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
        ], $tableOptions);

        $this->batchInsert('map_document_tag_type', ['id', 'name'], [
            ['1', 'Unknown'],
            ['2', 'Document name'],
            ['3', 'Interpret name'],
            ['4', 'Document full name'],
            ['5', 'Interpret full name'],
            ['6', 'Labels'],
        ]);

        $this->addColumn(
            'map_document_tag',
            'type_id',
            Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 1'
        );

        // Updates also made in main
        $this->addForeignKey('fk_map_document_tag_type',
            'map_document_tag', 'type_id',
            'map_document_tag_type', 'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_map_document_tag_type', 'map_document_tag');

        $this->dropTable('map_document_tag_type');
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
