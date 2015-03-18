<?php

use yii\db\Schema;
use yii\db\Migration;

class m150303_010014_documents extends Migration
{
    public function up()
    {
        $tableOptions = null;
        if($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_slovak_ci ENGINE=InnoDB';
        }

        $this->createTable('types', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
        ], $tableOptions);

        $this->createTable('interprets', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
        ], $tableOptions);

        $this->createTable('documents', [
            'id' => 'pk',
            'name' => Schema::TYPE_STRING . ' NOT NULL',
            'link' => Schema::TYPE_STRING . ' NOT NULL UNIQUE',
            'type_id' => Schema::TYPE_INTEGER . ' NOT NULL',
            'interpret_id' => Schema::TYPE_INTEGER . ' NOT NULL',
        ], $tableOptions);


        $this->addForeignKey('fk_document_has_type', 'documents', 'type_id', 'types', 'id');
        $this->addForeignKey(
            'fk_document_has_interpret',
            'documents', 'interpret_id',
            'interprets', 'id'
        );
    }

    public function down()
    {
        $this->dropForeignKey('fk_document_has_type', 'documents');
        $this->dropForeignKey('fk_document_has_interpret', 'documents');
    
        $this->dropTable('documents');
        $this->dropTable('types');
        $this->dropTable('interprets');

        return true;
    }
}
