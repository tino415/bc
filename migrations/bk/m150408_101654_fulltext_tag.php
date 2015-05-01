<?php

use yii\db\Schema;
use yii\db\Migration;

class m150408_101654_fulltext_tag extends Migration
{
    // Use safeUp/safeDown to run migration code within a transaction
    public function up()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql')
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=MYISAM';

        $this->createTable('document_search_string', [
            'id' => 'pk',
            'search_string' => Schema::TYPE_STRING,
            'document_id' => Schema::TYPE_INTEGER,
        ], $tableOptions);

        $this->addForeignKey('fk_document_search_string',
            'document_search_string', 'document_id',
            'document', 'id',
            'CASCADE'
        );

        $this->execute('
            CREATE FULLTEXT INDEX
            document_fulltext_search
            ON document_search_string (search_string)
        ');
    }
    
    public function down()
    {
        $this->dropForeignKey('fk_document_search_string');
        $this->dropTable('search_string');
    }
}
