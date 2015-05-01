<?php

use yii\db\Schema;
use yii\db\Migration;

class m150405_151942_tag_remove_index extends Migration
{
    public function safeUp()
    {
        $this->dropIndex('unique_tags', 'tag');
        $this->addColumn('tag', 'count', Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0');
        $this->addColumn('tag', 'document_id', Schema::TYPE_INTEGER . ' NOT NULL');
        $this->addForeignKey('fk_document_tag',
            'tag', 'document_id',
            'document', 'id',
            'CASCADE'
        );
    }

    public function safeDown()
    {
        $this->dropForeignKey('fk_document_tag', 'tag');
        $this->createIndex('unique_tags', 'tag', ['name', 'type_id'], true);
        $this->dropColumn('tag', 'count');
        $this->dropColumn('tag', 'document_id');
    }
    
}
