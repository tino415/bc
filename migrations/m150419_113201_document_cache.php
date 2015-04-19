<?php

use yii\db\Schema;
use yii\db\Migration;

class m150419_113201_document_cache extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->addColumn(
            'document', 
            'content', 
            Schema::TYPE_TEXT . ' DEFAULT NULL'
        );

        $this->createTable( 'schema', [
            'id' => 'pk',
            'content' => Schema::TYPE_STRING . ' NOT NULL',
            'document_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);

        $this->addForeignKey('fk_schema_document',
            'schema', 'document_id',
            'document', 'id',
            'CASCADE'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_schema_document', 'schema');

        $this->dropTable('schema');

        $this->dropColumn('document', 'content');
        $this->dropColumn('document', 'chords');
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
