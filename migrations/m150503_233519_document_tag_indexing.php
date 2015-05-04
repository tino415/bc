<?php

use yii\db\Schema;
use yii\db\Migration;

class m150503_233519_document_tag_indexing extends Migration
{
    public function up()
    {
        $this->createIndex('tag_name_index', 'tag', 'name', true);
        $this->createIndex('document_name_index', 'document', 'name');
        $this->createIndex('interpret_name_index', 'interpret', 'name', true);

        $this->createIndex('interpret_pk_index', 'interpret', 'id', true);
        $this->createIndex('document_pk_index', 'document', 'id', true);
        $this->createIndex('map_document_tag_pk_index', 'map_document_tag', 'id', true);

        $this->createIndex('document_interpret_fk_index', 'document', 'interpret_id');
    }

    public function down()
    {
        $this->dropIndex('tag_name_index', 'tag');
        $this->dropIndex('document_name_index', 'document');
        $this->dropIndex('interpret_name_index', 'interpert');
        
        $this->dropIndex('interpret_pk_index', 'interpret');
        $this->dropIndex('document_pk_index', 'document');
        $this->dropIndex('map_document_tag_pk_index', 'map_document_tag');

        $this->dropIndex('document_interpret_fk_index', 'document');
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
