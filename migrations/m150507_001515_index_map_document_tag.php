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
        $this->dropIndex('map_document_tag_tag_id', 'map_document_tag');
        $this->dropIndex('map_document_tag_document_id', 'map_document_tag');
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
