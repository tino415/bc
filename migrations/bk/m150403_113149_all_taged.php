<?php

use yii\db\Schema;
use yii\db\Migration;

class m150403_113149_all_taged extends Migration
{
    public function up()
    {

        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->createTable('tag_type', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
        ],$tableOptions);

        $this->batchInsert('tag_type', ['id', 'name'], [
            [1, 'type'],
            [2, 'author'],
            [3, 'name'],
            [4, 'genre'],
            [5, 'other'],
        ]);

        $this->addColumn('tags', 'type_id', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 5');

        $this->update('tags', ['type_id' => 1]);

        $this->addForeignKey('fk_tag_type', 
            'tags', 'type_id',
            'tag_type', 'id',
            'CASCADE'
        );

        $this->createIndex('unique_tags', 'tags', ['name', 'type_id'], true);
    }

    public function down()
    {
        $this->dropForeignKey('fk_tag_type', 'tags');

        $this->dropColumn('tags', 'type_id');

        $this->dropTable('tag_type');
    }
    
    public function safeUp()
    {
        $this->up();
    }
    
    public function safeDown()
    {
        $this->down();
    }
}
