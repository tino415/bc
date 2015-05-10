<?php

use yii\db\Schema;
use yii\db\Migration;

class m150507_001515_index_map_document_tag extends Migration
{
    public function up()
    {
        $this->createIndex('map_document_tag_tag_id', 'map_document_tag', 'tag_id');
        $this->createIndex('map_document_tag_document_id', 'map_document_tag', 'document_id');
    }

    public function down()
    {
        echo "m150507_001515_index_map_document_tag cannot be reverted.\n";

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
